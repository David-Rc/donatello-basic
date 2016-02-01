<?php

require_once "inc/db.php";

function getUser( $mail, $password ){
    $db = getDB();

    if( ! $db )
        return false;
    else {
        $q = "SELECT id_user, username, login FROM lo_users ";
        $q .= "WHERE `login`= :login AND `password`=SHA(:password)";

        $req = $db->prepare( $q );
        $req->bindParam(":login", $mail, PDO::PARAM_STR);
        $req->bindParam(":password", $password, PDO::PARAM_STR);
        $res = $req->execute();

        if( $user = $req->fetch())
            return $user;
        else
            return false;
    }
}

if(isset($_POST['user_login']) && isset($_POST['user_pass']) ){

    $user = getUser($_POST['user_login'], $_POST['user_pass']);

    if( $user ){
        session_start();
        $_SESSION['username'] = $user['username'];
        $_SESSION['login'] = $user['login'];
        $_SESSION['id_user'] = $user['id_user'];
        error_log($user['username'] .'/'. $_POST['user_pass']);
        header( 'location:home.php' );
    }
    else
        header( 'location:index.php?login_error=1' );

} else
    header('location:index.php?login_error=1');
?>