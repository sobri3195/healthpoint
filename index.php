<?php
if(!file_exists("config/config.inc.php")) {
    header("Location: install/start.php");
} else {
    if(!file_exists("index.html")) {
        require_once('db/connection.php');
        $query = "SELECT code,name,meta_title,meta_description,meta_image FROM sml_maps WHERE show_in_first_page=1 AND active=1 LIMIT 1;";
        $result = $mysqli->query($query);
        if($result) {
            if($result->num_rows==1) {
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $code = $row['code'];
                $name = $row['name'];
                if(empty($row['meta_title'])) {
                    $meta_title = $name;
                } else {
                    $meta_title = $row['meta_title'];
                }
                if(empty($row['meta_description'])) {
                    $meta_description = '';
                } else {
                    $meta_description = $row['meta_description'];
                }
                if(empty($row['meta_image'])) {
                    $meta_image = '';
                } else {
                    $meta_image = $row['meta_image'];
                }
                $html_meta_image = "";
                $html_meta_description = "";
                if(!empty($meta_image)) {
                    $html_meta_image = '<meta itemprop="image" content="viewer/content/'.$meta_image.'">
                                        <meta property="og:image" content="viewer/content/'.$meta_image.'" />
                                        <meta property="twitter:image" content="viewer/content/'.$meta_image.'">';
                }
                if(!empty($meta_description)) {
                    $html_meta_description = '<meta itemprop="description" content="'.$meta_description.'">
                                            <meta name="description" content="'.$meta_description.'"/>
                                            <meta property="og:description" content="'.$meta_description.'" />
                                            <meta property="twitter:description" content="'.$meta_description.'">';
                }
                if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'viewer'.DIRECTORY_SEPARATOR.'favicons'.DIRECTORY_SEPARATOR.$code.DIRECTORY_SEPARATOR.'favicon.ico')) {
                    $favicon_url = "viewer/favicons/$code/favicon.ico";
                } else if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'viewer'.DIRECTORY_SEPARATOR.'favicons'.DIRECTORY_SEPARATOR.'favicon.ico')) {
                    $favicon_url = "viewer/favicons/favicon.ico";
                } else {
                    $favicon_url = "favicon.ico";
                }
                $html = <<<HTML_CODE
<html>
    <head>
        <title>$meta_title</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1, minimum-scale=1">
        <link rel="icon" type="image/x-icon" href="$favicon_url">
        <meta property="og:type" content="website">
        <meta property="twitter:card" content="summary_large_image">
        <meta property="og:url" content="viewer/index.php?code=$code">
        <meta property="twitter:url" content="viewer/index.php?code=$code">
        <meta itemprop="name" content="$meta_title">
        <meta property="og:title" content="$meta_title">
        <meta property="twitter:title" content="$meta_title">
        $html_meta_image
        $html_meta_description
        <style>
            html, body { margin: 0; padding: 0; overflow: hidden; }
            iframe { height: 100%; }
        </style>
    </head>
    <body>
        <iframe allowfullscreen allow="fullscreen; geolocation;" width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="viewer/index.php?code=$code"></iframe>
    </body>
</html>
HTML_CODE;
                echo $html;
                exit;
            }
        }
        header("Location: backend/login.php");
    } else {
        header("Location: index.html");
    }
}