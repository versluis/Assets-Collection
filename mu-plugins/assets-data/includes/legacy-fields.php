<?php
/************************************/
/*
/* LEGACY MARKETPLACE URL FIELD (guru_fields[steamURL])
/* (moved from the theme's includes/custom-fields.php)
/*
/************************************/  

// add a meta box to post input dialogue
function assets_data_add_legacy_meta_box()
{
  add_meta_box(
    'guru_fields_meta_box', // $id
    'Marketplace Links', // $title
    'assets_data_display_legacy_meta_box', // $callback
    ASSETS_DATA_POST_TYPE, // $screen
    'normal', // $context
    'high' // $priority
  );
}
add_action('add_meta_boxes', 'assets_data_add_legacy_meta_box');

// contents of our custom field box
function assets_data_display_legacy_meta_box($post)
{
  $meta = get_post_meta($post->ID, 'guru_fields', true); ?>

  <input type="hidden" name="guru_meta_box_nonce" value="<?php echo wp_create_nonce(basename(__FILE__)); ?>">

  <!-- All fields will go here -->
  <p>Add a link to the product page, it'll display below the main text above ;-)</p>

  <p>
    <label for="guru_fields[steamURL]">Marketplace URL: </label>
    <input type="url" name="guru_fields[steamURL]" id="guru_fields[steamURL]"
    class="regular-text" value="<?php echo esc_attr( is_array($meta) ? ( $meta['steamURL'] ?? '' ) : '' ); ?>">
  </p>

<?php }

// save custom fields
function assets_data_save_legacy_meta($post_id)
{
  // verify nonce
  if (!isset($_POST['guru_meta_box_nonce']) || !wp_verify_nonce($_POST['guru_meta_box_nonce'], basename(__FILE__))) {
    return $post_id;
  }
  // check autosave
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return $post_id;
  }
  // check permissions
  if (!current_user_can('edit_post', $post_id)) {
    return $post_id;
  }

  $old = get_post_meta($post_id, 'guru_fields', true);
  $new = isset($_POST['guru_fields']) ? $_POST['guru_fields'] : null;
  if (is_array($new) && isset($new['steamURL'])) {
    $url = trim($new['steamURL']);
    if ($url !== '' && !filter_var($url, FILTER_VALIDATE_URL)) {
      return $post_id; // not a valid URL format, don't save
    }
    $new['steamURL'] = esc_url_raw($url);
  }

  // an emptied URL removes the meta instead of storing array('steamURL' => '')
  if (is_array($new) && '' === ($new['steamURL'] ?? '')) {
    delete_post_meta($post_id, 'guru_fields');
  } elseif ($new && $new !== $old) {
    update_post_meta($post_id, 'guru_fields', $new);
  }
}
add_action('save_post', 'assets_data_save_legacy_meta');

// expose the legacy field to the REST API (and so to the MCP connector)
function assets_data_register_legacy_meta()
{
  register_post_meta(ASSETS_DATA_POST_TYPE, 'guru_fields', array(
    'type' => 'object',
    'single' => true,
    'description' => 'Legacy marketplace link, entered by hand over the years.',
    'show_in_rest' => array(
      'schema' => array(
        'type' => 'object',
        'properties' => array(
          'steamURL' => array('type' => 'string'),
        ),
        'additionalProperties' => false,
      ),
    ),
    'sanitize_callback' => function ($value) {
      if (!is_array($value)) {
        return array();
      }
      return array('steamURL' => esc_url_raw(trim($value['steamURL'] ?? '')));
    },
    'auth_callback' => 'assets_data_can_edit_meta',
  ));
}
add_action('init', 'assets_data_register_legacy_meta');
