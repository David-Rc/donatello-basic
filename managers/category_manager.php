<?php

function addCategory($categoryName, $idProject){
    global $db;
    $q = "INSERT INTO lo_categories (`id_project`, `name`) VALUES (:idProject, :categoryName)";

    $req = $db->prepare( $q );
    $req->bindParam(":idProject", $idProject,PDO::PARAM_INT);
    $req->bindParam(":categoryName", $categoryName,PDO::PARAM_STR);

    return $req->execute();
}

function addDefaultCategory($idProject){
    return addCategory( "Todo", $idProject );
}

?>