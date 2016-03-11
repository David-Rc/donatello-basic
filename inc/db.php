<?php

function getDB(){
    global $db;

    if($db)
        return $db;

    try{
        $dbHost = "localhost:3306";
        $dbName = "simpllo";
        $dbUser = "root";
        $dbPass = "root";

        $db = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    } catch( PDOException $e ){
        die("Erreur de connexion : $e");
    }

    return $db;
}

?>