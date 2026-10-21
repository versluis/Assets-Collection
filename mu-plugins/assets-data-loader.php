<?php
/**
 * Plugin Name: Assets Data (loader)
 * Description: Loads the Assets Data plugin, preferring the copy deployed with the theme repo.
 *
 * The repo's mu-plugins/ folder mirrors wp-content/mu-plugins/, so it can be moved there
 * in one go. Plugin updates arrive with each repo pull, so the copy inside a theme folder
 * wins when present (its name differs between sites: Assets-Collection locally,
 * Asset-Collection live); the copy next to this loader is the fallback.
 */

$assets_data_main = glob(WP_CONTENT_DIR . '/themes/*/mu-plugins/assets-data/assets-data.php');

if (empty($assets_data_main) && file_exists(__DIR__ . '/assets-data/assets-data.php')) {
  $assets_data_main = array(__DIR__ . '/assets-data/assets-data.php');
}

if (!empty($assets_data_main)) {
  require_once $assets_data_main[0];
} else {
  // fail quietly on the front end, but tell admins why their Assets have vanished
  add_action('admin_notices', function () {
    echo '<div class="notice notice-error"><p><strong>Assets Data:</strong> plugin not found in wp-content/themes/*/mu-plugins/assets-data/ or wp-content/mu-plugins/assets-data/ - the Asset post type is not registered.</p></div>';
  });
}
