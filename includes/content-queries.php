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

// list all assets
function assets_ListAll () {

    $query = new WP_Query( array( 
        'post_type' => 'assets', 
        'nopaging' => true )
    );
    $results = $query->found_posts;
    echo "<p>Here's a list of all <strong>$results Assets</strong> in my collection:</p><ul>";

    // iterate
    if ($query->have_posts() ) {
        echo "";
        while ($query->have_posts() ) {
            $query->the_post();
            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        }
        echo "</ul>";
    } // end list all
}

// DAZ with Interactive License
function assets_dazi() {
    $query = new WP_Query( array( 
        'marketplace' => 'daz-interactive', 
        'nopaging' => true )
    );
    $results = $query->found_posts;
    echo "<p>There are <strong>$results DAZ3D Assets</strong> with interactive licenses in my collection:</p><ul>";

    // list all games
    if ($query->have_posts() ) {
        echo "";
        while ($query->have_posts() ) {
            $query->the_post();
            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        }
        echo "</ul>";
    } // end DAZ Interactive
}

// DAZ regular assets
function assets_daz() {
    $query = new WP_Query( array( 
        'marketplace' => 'daz', 
        'nopaging' => true )
    );
    $results = $query->found_posts;
    echo "<p>There are <strong>$results DAZ3D Assets</strong> with interactive licenses in my collection:</p><ul>";

    // list all games
    if ($query->have_posts() ) {
        echo "";
        while ($query->have_posts() ) {
            $query->the_post();
            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        }
        echo "</ul>";
    } // end DAZ Interactive
}

// EPIC Assets
function assets_ListEPIC() {
    
    $query = new WP_Query( array( 
        'marketplace' => 'epic', 
        'nopaging' => true )
    );
    $results = $query->found_posts;
    echo "<p>There are <strong>$results Unreal Marketplace Assets</strong> in my collection:</p><ul>";

    // list all games
    if ($query->have_posts() ) {
        echo "";
        while ($query->have_posts() ) {
            $query->the_post();
            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        }
        echo "</ul>";
    } // end list EPIC
}

// Humble Assets
function assets_ListHumble() {
    $query = new WP_Query( array( 
        'marketplace' => 'humble', 
        'nopaging' => true )
    );
    $results = $query->found_posts;
    echo "<p>There are <strong>$results Humble Bundle Assets</strong> in my collection:</p><ul>";

    // list all games
    if ($query->have_posts() ) {
        echo "";
        while ($query->have_posts() ) {
            $query->the_post();
            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        }
        echo "</ul>";
    } // end list Humble
}

// Synty Assets
function assets_ListSynty() {
    $query = new WP_Query( array( 
        'marketplace' => 'synty', 
        'nopaging' => true )
    );
    $results = $query->found_posts;
    echo "<p>There are <strong>$results Synty Store Assets</strong> in my collection:</p><ul>";

    // list all games
    if ($query->have_posts() ) {
        echo "";
        while ($query->have_posts() ) {
            $query->the_post();
            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        }
        echo "</ul>";
    } // end list Synty
}