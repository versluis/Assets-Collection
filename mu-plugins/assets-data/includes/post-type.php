<?php
/************************************/
/*
/* CUSTOM ASSET POST
/* (moved from the theme's includes/assets-post.php)
/*
/************************************/

// Create a custom ASSET Post Type
function assets_data_register_post_type()
{
  register_post_type(
    ASSETS_DATA_POST_TYPE,
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

  // registered meta only shows up in the REST API when the post type supports custom fields
  add_post_type_support(ASSETS_DATA_POST_TYPE, 'custom-fields');

  // register our post type with tags and categories
  register_taxonomy_for_object_type('category', ASSETS_DATA_POST_TYPE);
  register_taxonomy_for_object_type('post_tag', ASSETS_DATA_POST_TYPE);
  register_taxonomy_for_object_type('platform', ASSETS_DATA_POST_TYPE);
}
add_action('init', 'assets_data_register_post_type');
