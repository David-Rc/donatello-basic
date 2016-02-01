<?php

require_once "inc/db.php";

function subscribeUser( $mail, $password, $username ){
    $db = getDB();

    if( ! $db )
        return false;
    else {
        $q = "INSERT INTO lo_users(`login`, `password`, `username`) ";
        $q .= "VALUES (:login, SHA(:password), :username)";

        $req = $db->prepare( $q );
        $req->bindParam(":login", $mail, PDO::PARAM_STR);
        $req->bindParam(":password", $password, PDO::PARAM_STR);
        $req->bindParam(":username", $username, PDO::PARAM_STR);
        $res = $req->execute();
        return $res ? $db->lastInsertId() : 0;
    }
}

if(isset($_POST['user_login']) && isset($_POST['user_pass']) && isset($_POST['username']) ){

    $userId = subscribeUser($_POST['user_login'], $_POST['user_pass'], $_POST['username']);
    if( $userId > 0 ){
        session_start();

        $_SESSION['username'] = $_POST['username'];
        $_SESSION['login'] = $_POST['user_login'];
        $_SESSION['id_user'] = $userId;

        header( 'location:home.php?new_user=1' );
    }
    else
        header( 'location:index.php' );

} else
    header('location:index.php?subscribe_error=1');
?>