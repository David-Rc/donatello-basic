<?php

function getDB(){
    global $db;

    if($db)
        return $db;

    try{
        $dbHost = "localhost";
        $dbName = "simpllo";
        $dbUser = "root";
        $dbPass = "root";

        $db = new PDO("mysql:host=$dbHost; dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    } catch( Exception $e ){
        die("Erreur de connexion : $e->getMessage()");
    }

    return $db;
}

?>