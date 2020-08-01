<?php 
/************************************/
/*
/* CUSTOM ASSET POST
/*
/************************************/

// Create a custom ASSET Post Type
function guru_create_asset_posttype()
{

  // create out custom post type
  register_post_type(
    'assets',
    // with these options
    array(
      'labels' => array(
        'name' => __('Assets'),
        'singular_name' => __('Asset')
      ),
      'public' => true,
      'has_archive' => true,
      'rewrite' => array('slug' => 'asset'),
      'show_in_rest' => true,
      'menu_icon'   => 'dashicons-nametag',

    )
  );

  // register our post type with tags and categories
  register_taxonomy_for_object_type('category', 'assets');
  register_taxonomy_for_object_type('post_tag', 'assets');
  register_taxonomy_for_object_type('platform', 'assets');
}
// initialise on theme setup
add_action('init', 'guru_create_asset_posttype');
