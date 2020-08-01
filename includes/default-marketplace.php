<?php

// set default Category and Marketpalce on Asset Posts
// https://silentcomics.com/wordpress/automatic-default-taxonomy-for-a-custom-post-type/

function guru_set_default_marketplace ( $post_id, $post ) {
    if ( 'publish' === $post->post_status && $post->post_type === 'assets' ) {
        $defaults = array(
            'category' => array( 'not-tried' ),
            'marketplace' => array ( 'epic' ),
            );
        $taxonomies = get_object_taxonomies( $post->post_type );
        foreach ( (array) $taxonomies as $taxonomy ) {
            $terms = wp_get_post_terms( $post_id, $taxonomy );
            if ( empty( $terms ) && array_key_exists( $taxonomy, $defaults ) ) {
                wp_set_object_terms( $post_id, $defaults[$taxonomy], $taxonomy );
            }
        }
    }
  }
  add_action( 'save_post', 'guru_set_default_marketplace', 0, 2 );
  