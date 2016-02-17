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

        $db = new PDO("mysql:host=$dbHost:3306;dbname=$dbName", $dbUser, $dbPass);
    } catch( PDOException $e ){
        die("Erreur de connexion : $e");
    }

    return $db;
}

?>