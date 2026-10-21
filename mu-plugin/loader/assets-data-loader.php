<?php
/**
 * Plugin Name: Assets Data (loader)
 * Description: Loads the Assets Data plugin from the Assets-Collection repo folder.
 *
 * Copy this file into wp-content/mu-plugins/ (one-time, on every site using the repo).
 * The plugin code itself is deployed with the repo, so updates arrive with each pull.
 * The repo's folder name differs between sites (Assets-Collection locally, Asset-Collection
 * on the live site), so we look for the plugin in any theme folder rather than a fixed path.
 */

$assets_data_main = glob(WP_CONTENT_DIR . '/themes/*/mu-plugin/assets-data.php');

if (!empty($assets_data_main)) {
  require_once $assets_data_main[0];
} else {
  // fail quietly on the front end, but tell admins why their Assets have vanished
  add_action('admin_notices', function () {
    echo '<div class="notice notice-error"><p><strong>Assets Data:</strong> plugin file not found at wp-content/themes/*/mu-plugin/assets-data.php - the Asset post type is not registered.</p></div>';
  });
}
