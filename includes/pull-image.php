<?php
/************************************/
/*
/* PULL IMAGE
/* found here: https://www.ostraining.com/blog/coding/extract-image-php/
/* and here: https://stackoverflow.com/questions/7479835/getting-the-first-image-in-string-with-php
/*
/************************************/

// require 'phpQuery-onefile.php';

function guru_pull_image ($sourceURL) {

    $html = file_get_contents($sourceURL);

    echo ("The URL reads: <br>");
    echo $html;

    preg_match_all('/<img.*?src=[\'"](.*?)[\'"].*?>/i', $html, $matches);
    var_dump($matches);


    $elements = $matches[1];

    foreach($elements as $element) {
       echo $element . "\n";
}


}
