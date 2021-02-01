<?php
/************************************/
/*
/* CONTENT QUERY FUNCTIONS
/* Jay's Assets Collection
/* @since 1.1
/*
/************************************/

// register shortcodes
function guru_shortcodes() {
    add_shortcode( 'all-assets', 'assets_ListAll' );
    add_shortcode( 'daz-inter', 'assets_dazi' );
    add_shortcode( 'daz', 'assets_daz' );
    add_shortcode( 'epic', 'assets_ListEPIC' );
    add_shortcode( 'humble', 'assets_ListHumble' );
    add_shortcode( 'synty', 'assets_ListSynty' );
}
add_action( 'init', 'guru_shortcodes' );

// test function
function querytest() {
    echo '<br>';
    echo '<h2>This thing on?</h2>';
    echo '<br>';
}

// returns formatted TOC list
// requires $query
function guru_toc_output ( $query ) {

    // list articles
    if ($query->have_posts()) {
        $output = $output . '<ul>';

        $count = 0;
        while ($query->have_posts()) {

            $count++;
            $query->the_post();
            $output = $output .  '<li style="list-style: disclosure-closed"><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';

            if ($count >= 10) {
                $output = $output . "<hr class='slimline'>";
                $count = 0;
            }
        }
        $output = $output .  "</ul>";

        return $output;
    } // end list articles
}

// list all assets
function assets_ListAll () {

    $query = new WP_Query( array( 
        'post_type' => 'assets', 
        'nopaging' => true )
    );
    $results = $query->found_posts;
    echo "<p>Here's a list of all <strong>$results Assets</strong> in my collection:</p><ul>";

    // print query with separators
    echo guru_toc_output( $query );
}

// DAZ with Interactive License
function assets_dazi() {
    $query = new WP_Query( array( 
        'marketplace' => 'daz-interactive', 
        'nopaging' => true )
    );
    $results = $query->found_posts;
    echo "<p>There are <strong>$results DAZ3D Assets</strong> with interactive licenses in my collection:</p><ul>";

    // print query with separators
    echo guru_toc_output( $query );
}

// DAZ regular assets
function assets_daz() {
    $query = new WP_Query( array( 
        'marketplace' => 'daz', 
        'nopaging' => true )
    );
    $results = $query->found_posts;
    echo "<p>There are <strong>$results DAZ3D Assets</strong> with interactive licenses in my collection:</p><ul>";

    // print query with separators
    echo guru_toc_output( $query );
}

// EPIC Assets
function assets_ListEPIC() {
    
    $query = new WP_Query( array( 
        'marketplace' => 'epic', 
        'nopaging' => true )
    );
    $results = $query->found_posts;
    echo "<p>There are <strong>$results Unreal Marketplace Assets</strong> in my collection:</p><ul>";

    // print query with separators
    echo guru_toc_output( $query );
}

// Humble Assets
function assets_ListHumble() {
    $query = new WP_Query( array( 
        'marketplace' => 'humble', 
        'nopaging' => true )
    );
    $results = $query->found_posts;
    echo "<p>There are <strong>$results Humble Bundle Assets</strong> in my collection:</p><ul>";

    // print query with separators
    echo guru_toc_output( $query );
}

// Synty Assets
function assets_ListSynty() {
    $query = new WP_Query( array( 
        'marketplace' => 'synty', 
        'nopaging' => true )
    );
    $results = $query->found_posts;
    echo "<p>There are <strong>$results Synty Store Assets</strong> in my collection:</p><ul>";

    // print query with separators
    echo guru_toc_output( $query );
}