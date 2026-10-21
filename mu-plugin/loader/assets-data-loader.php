<?php
/**
 * Plugin Name: Assets Data (loader)
 * Description: Loads the Assets Data plugin from the Assets-Collection repo folder.
 *
 * Copy this file into wp-content/mu-plugins/ (one-time, on every site using the repo).
 * The plugin code itself is deployed with the repo, so updates arrive with each pull.
 */

$assets_data_main = WP_CONTENT_DIR . '/themes/Assets-Collection/mu-plugin/assets-data.php';

if (file_exists($assets_data_main)) {
  require_once $assets_data_main;
} else {
  // fail quietly on the front end, but tell admins why their Assets have vanished
  add_action('admin_notices', function () {
    echo '<div class="notice notice-error"><p><strong>Assets Data:</strong> plugin file not found at wp-content/themes/Assets-Collection/mu-plugin/assets-data.php - the Asset post type is not registered.</p></div>';
  });
}
