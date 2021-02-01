<?php
/************************************/
/* ARCHIVE QUERIES
/************************************/  

// amend archive pages to show last updated posts first
function guru_order_category_archives( $query ) {
    if ( ! is_admin() && $query->is_main_query() ) {
        // Not a query for an admin page.
        // It's the main query for a front end page of your site.
 
        if ( is_category() || is_tag() ) {
            // It's the main query for a category/tag archive.
 
            // Update parameters before query is executed 
            $query->set( 'orderby', 'modified' );
        }
    }
}
add_action( 'pre_get_posts', 'guru_order_category_archive' );
