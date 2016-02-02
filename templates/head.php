<?php

function echoHead ( $title )
{
    echo str_replace( '{{CATEGORY}}', 'test', '<!doctype html>
<html lang="fr_FR">
<head>
    <meta charset="UTF-8">
    <title>Donatello - {{CATEGORY}}</title>
    <link rel="stylesheet" href="'.APP_PATH.'/styles/base.css"/>
    <link rel="stylesheet" href="' . APP_PATH . '/styles/simpllo.css"/>
</head>
<body>
' );
}


?>