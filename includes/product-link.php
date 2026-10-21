<?php
/************************************/
/*
/* PRODUCT LINKS
/* How marketplace links are labelled and decorated on the front end.
/*
/************************************/

// marketplace link for display: DAZ links get our affiliate code appended
function guru_marketplace_link ($url) {
    if (strpos($url, 'daz3d.com') !== false) {
        return $url . '?cjref=1&utm_source=cj&utm_medium=affiliate&cjevent=bd1e3350dfe911eb80a6d9200a82b821';
    }
    return $url;
}

// button label for a product link, from the store it points to
function guru_product_link_label ($url) {
    $stores = array(
        'fab.com'                   => 'Fab',
        'unrealengine.com'          => 'Unreal Marketplace',
        'unrealecosystem.com'       => 'Unreal Ecosystem',
        'daz3d.com'                 => 'DAZ 3D',
        'syntystore.com'            => 'Synty Store',
        'cosmos.leartesstudios.com' => 'Cosmos',
        'gumroad.com'               => 'Gumroad',
        'reallusion.com'            => 'Reallusion',
        'artstation.com'            => 'ArtStation',
        'mocaponline.com'           => 'MoCap Online',
        'superhivemarket.com'       => 'Superhive',
        'blendermarket.com'         => 'Superhive',
        'humblebundle.com'          => 'Humble Bundle',
        'renderosity.com'           => 'Renderosity',
    );
    $host = preg_replace('/^www\./', '', strtolower((string) wp_parse_url($url, PHP_URL_HOST)));
    foreach ($stores as $store => $name) {
        if ($host === $store || str_ends_with($host, '.' . $store)) {
            return 'View on ' . $name;
        }
    }
    return 'View product';
}
