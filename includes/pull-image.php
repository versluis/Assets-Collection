<?php
/************************************/
/*
/* EMBED WEB PREVIEW
/* 
/* 
/*
/************************************/

// embed a screenshot of our asset 
function guru_pull_image ($sourceURL, $imageID) {

    $image = 'imagecache/image' . $imageID . '.jpg';
    $imageURL = "https://assets.wpguru.co.uk/" . $image;

    // if we have a cached image, return that
    if (file_exists($image)) {
        return $imageURL;

    } else {
        // otherwise let's generate it first
        // disabled: seems to have stopped working as of Januar 2022
        // $command = 'wkhtmltoimage --enable-javascript --javascript-delay 3000 --images --height 768 ' . $sourceURL . ' ' . $image;
        // exec($command);
    
        // return $imageURL;
  
    }
    

}

// marketplace link for display: DAZ links get our affiliate code appended
function guru_marketplace_link ($url) {
    if (strpos($url, 'daz3d.com') !== false) {
        return $url . '?cjref=1&utm_source=cj&utm_medium=affiliate&cjevent=bd1e3350dfe911eb80a6d9200a82b821';
    }
    return $url;
}
