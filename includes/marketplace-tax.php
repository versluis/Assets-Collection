<?php

// create custom "Marketplace" taxonomy
function guru_create_marketplace_tax()
{
  // Add new taxonomy, make it hierarchical (like categories)
  $labels = array(
    'name' => _x('Marketplaces', 'taxonomy general name', 'textdomain'),
    'singular_name' => _x('Marketplace', 'taxonomy singular name', 'textdomain'),
    'search_items' => __('Search Marketplaces', 'textdomain'),
    'all_items' => __('All Marketplaces', 'textdomain'),
    'parent_item' => __('Parent Marketplace', 'textdomain'),
    'parent_item_colon' => __('Parent Marketplace:', 'textdomain'),
    'edit_item' => __('Edit Marketplace', 'textdomain'),
    'update_item' => __('Update Marketplace', 'textdomain'),
    'add_new_item' => __('Add New Marketplace', 'textdomain'),
    'new_item_name' => __('New Marketplace Name', 'textdomain'),
    'menu_name' => __('Marketplaces', 'textdomain'),
  );

  // show_in_rest makes this thing show up in the post editor
  $args = array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'query_var' => true,
    'rewrite' => array('slug' => 'marketplace'),
    'show_in_rest' => true,
  );

  register_taxonomy('marketplace', array('assets'), $args);
}
// initialise on theme setup
add_action('init', 'guru_create_marketplace_tax');
