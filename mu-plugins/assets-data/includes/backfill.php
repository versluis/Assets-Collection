<?php
/************************************/
/*
/* PRODUCT URL BACKFILL
/* Fills the product_url field on Asset posts that don't have one yet.
/* Rules, in order:
/*   1. product_url already set          -> skip
/*   2. old fab_url field set            -> move it to product_url (field was renamed)
/*   3. legacy guru_fields[steamURL] set -> use it
/*   4. exactly one store link in body   -> use it
/*   5. several store links in body      -> flag for a human, write nothing
/* Post bodies are only ever read, never written. Writes only fill empty
/* fields, so running it again changes nothing.
/*
/* Run it from Tools > Assets Backfill, or: wp assets-data backfill [--dry-run]
/*
/************************************/

// hosts we treat as product pages; anything else in a body is ignored
function assets_data_store_hosts()
{
  return array(
    'fab.com', 'unrealengine.com', 'daz3d.com', 'syntystore.com', 'cosmos.leartesstudios.com',
    'gumroad.com', 'reallusion.com', 'artstation.com', 'mocaponline.com', 'unrealecosystem.com',
    'superhivemarket.com', 'blendermarket.com', 'humblebundle.com', 'renderosity.com',
  );
}

function assets_data_is_store_url($url)
{
  $host = preg_replace('/^www\./', '', strtolower((string) wp_parse_url($url, PHP_URL_HOST)));
  if ('' === $host || in_array($host, array('docs.unrealengine.com', 'dev.epicgames.com'), true)) {
    return false; // documentation, not a product page
  }
  if ('fab.com' === $host && false !== strpos($url, '/portal/')) {
    return false; // seller portal preview, not a public listing
  }
  foreach (assets_data_store_hosts() as $store) {
    if ($host === $store || str_ends_with($host, '.' . $store)) {
      return true;
    }
  }
  return false;
}

// distinct store links in a post body (bare URLs count too, like on the front end)
function assets_data_body_store_links($content)
{
  preg_match_all('/href="([^"]+)"/', make_clickable($content), $matches);
  $links = array();
  foreach ($matches[1] as $url) {
    $url = html_entity_decode($url, ENT_QUOTES);
    if (assets_data_is_store_url($url)) {
      $links[$url] = true;
    }
  }
  return array_keys($links);
}

// work out what the backfill would do for one post
function assets_data_backfill_plan($post)
{
  if ('' !== (string) get_post_meta($post->ID, 'product_url', true)) {
    return array('status' => 'already_set', 'url' => '');
  }

  $old_fab = (string) get_post_meta($post->ID, 'fab_url', true);
  if ('' !== $old_fab) {
    return array('status' => 'from_fab_url', 'url' => $old_fab);
  }

  $legacy = get_post_meta($post->ID, 'guru_fields', true);
  $legacy = is_array($legacy) ? trim((string) ($legacy['steamURL'] ?? '')) : '';
  if ('' !== $legacy && assets_data_is_store_url($legacy)) {
    return array('status' => 'from_legacy', 'url' => $legacy);
  }

  $body = assets_data_body_store_links($post->post_content);
  if (1 === count($body)) {
    return array('status' => 'from_body', 'url' => $body[0]);
  }
  if (count($body) > 1) {
    return array('status' => 'flag_several_links', 'url' => '', 'links' => $body);
  }
  return array('status' => 'nothing_found', 'url' => '');
}

/**
 * Scan every Asset post and optionally write the planned values.
 *
 * @param bool $write false for a dry run.
 * @return array{counts: array<string,int>, rows: array<int,array>, written: int, bodies_modified: int}
 */
function assets_data_run_backfill($write = false)
{
  $ids = get_posts(array(
    'post_type' => ASSETS_DATA_POST_TYPE,
    'post_status' => 'any',
    'posts_per_page' => -1,
    'fields' => 'ids',
    'orderby' => 'ID',
    'order' => 'ASC',
    'no_found_rows' => true,
  ));

  update_meta_cache('post', $ids); // one query for all meta instead of three per post

  $counts = array_fill_keys(array('already_set', 'from_fab_url', 'from_legacy', 'from_body', 'flag_several_links', 'nothing_found'), 0);
  $rows = array();
  $written = 0;
  $bodies_modified = 0;

  foreach ($ids as $id) {
    $post = get_post($id);
    $before = md5($post->post_content);
    $plan = assets_data_backfill_plan($post);
    $counts[$plan['status']]++;
    $rows[] = array('id' => $id, 'title' => get_the_title($post)) + $plan;

    if ($write && '' !== $plan['url']) {
      // registered sanitize_callback cleans the URL
      if (update_post_meta($id, 'product_url', $plan['url'])) {
        if ('from_fab_url' === $plan['status']) {
          delete_post_meta($id, 'fab_url');
        }
        $written++;
      } else {
        error_log("Assets Data backfill: could not write product_url on post $id");
      }
    }

    // belt and braces: prove we never touched a body
    clean_post_cache($id);
    if (md5(get_post_field('post_content', $id, 'raw')) !== $before) {
      $bodies_modified++;
    }
  }

  return array('total' => count($ids), 'counts' => $counts, 'rows' => $rows, 'written' => $written, 'bodies_modified' => $bodies_modified);
}

/* ---------------------------------------------------------------------
 * Tools > Assets Backfill
 * ------------------------------------------------------------------ */

function assets_data_backfill_menu()
{
  add_management_page('Assets Backfill', 'Assets Backfill', 'manage_options', 'assets-data-backfill', 'assets_data_backfill_page');
}
add_action('admin_menu', 'assets_data_backfill_menu');

function assets_data_backfill_labels()
{
  return array(
    'already_set' => 'Product URL already set (skipped)',
    'from_fab_url' => 'Moved from the old fab_url field',
    'from_legacy' => 'From the legacy Marketplace URL field',
    'from_body' => 'From a link in the post body',
    'flag_several_links' => 'Flagged: several store links in body, nothing written',
    'nothing_found' => 'No product link found',
  );
}

function assets_data_backfill_page()
{
  if (!current_user_can('manage_options')) {
    return;
  }

  $mode = '';
  if (isset($_POST['assets_data_backfill'])) {
    check_admin_referer('assets_data_backfill');
    $mode = 'run' === $_POST['assets_data_backfill'] ? 'run' : 'dry';
  }
  $labels = assets_data_backfill_labels();
  ?>
  <div class="wrap">
    <h1>Assets Backfill</h1>
    <p>Fills the <strong>Product URL</strong> field on Asset posts that don't have one yet: first from the old <code>fab_url</code> field, then from the legacy Marketplace URL field, then from a single store link in the post body. Post bodies are never changed, and existing values are never overwritten.</p>

    <form method="post">
      <?php wp_nonce_field('assets_data_backfill'); ?>
      <button type="submit" name="assets_data_backfill" value="dry" class="button button-secondary">Dry run (no changes)</button>
    </form>

    <?php
    if (!$mode) {
      echo '</div>';
      return;
    }

    $result = assets_data_run_backfill('run' === $mode);
    $planned = $result['counts']['from_fab_url'] + $result['counts']['from_legacy'] + $result['counts']['from_body'];
    ?>
    <h2><?php echo 'run' === $mode ? 'Backfill complete' : 'Dry run report'; ?></h2>
    <table class="widefat striped" style="max-width: 40em;">
      <tbody>
        <tr><th>Asset posts scanned</th><td><?php echo (int) $result['total']; ?></td></tr>
        <?php foreach ($labels as $key => $label) : ?>
          <tr><th><?php echo esc_html($label); ?></th><td><?php echo (int) $result['counts'][$key]; ?></td></tr>
        <?php endforeach; ?>
        <tr><th><?php echo 'run' === $mode ? 'Product URLs written' : 'Planned writes'; ?></th><td><strong><?php echo (int) ('run' === $mode ? $result['written'] : $planned); ?></strong></td></tr>
        <tr><th>Post bodies modified</th><td><strong><?php echo (int) $result['bodies_modified']; ?></strong></td></tr>
      </tbody>
    </table>

    <?php if ('dry' === $mode && $planned) : ?>
      <form method="post" style="margin-top: 1em;">
        <?php wp_nonce_field('assets_data_backfill'); ?>
        <button type="submit" name="assets_data_backfill" value="run" class="button button-primary">Run backfill (<?php echo (int) $planned; ?> writes)</button>
      </form>
    <?php endif; ?>

    <?php foreach (array('flag_several_links', 'from_fab_url', 'from_body', 'from_legacy', 'nothing_found') as $key) :
      $rows = array_filter($result['rows'], function ($row) use ($key) { return $row['status'] === $key; });
      if (!$rows) {
        continue;
      }
      ?>
      <details style="margin-top: 1.5em;" <?php echo 'flag_several_links' === $key ? 'open' : ''; ?>>
        <summary><strong><?php echo esc_html($labels[$key]); ?> (<?php echo count($rows); ?>)</strong></summary>
        <table class="widefat striped" style="margin-top: 0.5em;">
          <thead><tr><th style="width: 6em;">ID</th><th>Title</th><th>Product URL</th></tr></thead>
          <tbody>
            <?php foreach ($rows as $row) : ?>
              <tr>
                <td><a href="<?php echo esc_url(get_edit_post_link($row['id'])); ?>"><?php echo (int) $row['id']; ?></a></td>
                <td><?php echo esc_html($row['title']); ?></td>
                <td><?php echo esc_html('' !== $row['url'] ? $row['url'] : implode(' | ', $row['links'] ?? array())); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </details>
    <?php endforeach; ?>
  </div>
  <?php
}

/* ---------------------------------------------------------------------
 * wp assets-data backfill [--dry-run]   (for SSH)
 * ------------------------------------------------------------------ */

if (defined('WP_CLI') && WP_CLI) {
  WP_CLI::add_command('assets-data backfill', function ($args, $assoc) {
    $dry = !empty($assoc['dry-run']);
    $result = assets_data_run_backfill(!$dry);
    WP_CLI::log(($dry ? 'Dry run' : 'Backfill') . ': ' . $result['total'] . ' Asset posts scanned');
    foreach (assets_data_backfill_labels() as $key => $label) {
      WP_CLI::log(sprintf('  %5d  %s', $result['counts'][$key], $label));
    }
    foreach ($result['rows'] as $row) {
      if ('flag_several_links' === $row['status']) {
        WP_CLI::warning("Post {$row['id']} \"{$row['title']}\": " . implode(' | ', $row['links']));
      }
    }
    if (!$dry) {
      WP_CLI::log("Product URLs written: {$result['written']}");
    }
    WP_CLI::log("Post bodies modified: {$result['bodies_modified']}");
    WP_CLI::success('Done.');
  }, array('shortdesc' => 'Fill the product_url field on Asset posts from older sources. Use --dry-run first.'));
}
