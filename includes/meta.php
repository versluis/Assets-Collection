<?php
/************************************/
/*
/* META DATA
/*
/************************************/

// make meta data show up on Asset posts
add_filter( 'generate_entry_meta_post_types', function( $types ) {
    $types[] = 'assets';
    return $types;
  } );
  
add_filter( 'generate_footer_meta_post_types', function( $types ) {
    // $types[] = 'my-post-type';
    $types[] = 'assets';
    return $types;
  } );
     
  // add Status in front of categories
  add_filter( 'generate_category_list_output', function() {
    $categories = apply_filters( 'generate_show_categories', true );
  
    $term_separator = apply_filters( 'generate_term_separator', _x( ', ', 'Used between list items, there is a space after the comma.', 'generatepress' ), 'categories' );
    $categories_list = get_the_category_list( $term_separator );
  
    return sprintf( '<span class="cat-links">Status: %3$s<span class="screen-reader-text">%1$s </span>%2$s</span> ', // WPCS: XSS ok, sanitization ok.
        esc_html_x( 'Categories', 'Used before category names.', 'generatepress' ),
        $categories_list,
        // strip_tags( $categories_list ),
        apply_filters( 'generate_inside_post_meta_item_output', '', 'categories' )
    );
  } );
  
  // add Asset Type in front of Tags
  // can't be done - but this is where it should be done :-)
  add_filter( 'generate_tag_list_output', function() {
    $categories = apply_filters( 'generate_show_tags', true );
  
    $tag_list = get_the_tag_list( 'Asset Type: ', ', ');
  
    return sprintf( '<span class="tags-links">%3$s<span class="screen-reader-text">%1$s </span>%2$s</span> ', // WPCS: XSS ok, sanitization ok.
        esc_html_x( 'Categories', 'Used before category names.', 'generatepress' ),
        $tag_list,
        // strip_tags( $categories_list ),
        apply_filters( 'generate_inside_post_meta_item_output', '', 'categories' )
    );
  } );
  
  // add Marketplace taxonomy to footer meta (when available)
  add_filter( 'generate_category_list_output', function( $output ) {
    $terms = get_the_term_list( get_the_ID(), 'marketplace', '', ', ' );
    if ($terms) {
      return '<span class="dashicons dashicons-megaphone" style="color: #595959; margin-left: -3px;"></span> Bought from: ' . $terms . $output;
    } else 
    return $output;
    
  } );
  