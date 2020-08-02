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
        $command = 'wkhtmltoimage --enable-javascript --javascript-delay 3000 --images --height 768 ' . $sourceURL . ' ' . $image;
        exec($command);
    
        return $imageURL;

    }
    

}
