<?php
/**
 * Plugin Name: Assets Data
 * Description: Data layer for the Assets Collection: the Asset post type, Marketplace taxonomy and catalog meta fields. Loaded as a must-use plugin so it survives theme swaps.
 * Author:      Jay Versluis
 * Author URI:  https://versluis.com
 * Version:     1.0
 * License:     GNU General Public License v2 or later
 *
 * This file lives in the Assets-Collection repo and is pulled in by a small
 * loader in wp-content/mu-plugins/ (see mu-plugin/loader/assets-data-loader.php).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ASSETS_DATA_VERSION', '1.0' );
define( 'ASSETS_DATA_POST_TYPE', 'assets' );

require_once __DIR__ . '/includes/post-type.php';
require_once __DIR__ . '/includes/marketplace-tax.php';
require_once __DIR__ . '/includes/default-marketplace.php';
require_once __DIR__ . '/includes/legacy-fields.php';
require_once __DIR__ . '/includes/catalog-meta.php';
require_once __DIR__ . '/includes/catalog-meta-box.php';
