<?php
define( 'APP_PATH', 'http://localhost:8080/' );
//define( 'APP_PATH', 'http://localhost/labz/simplon/php_exemples/simpllo/' );
if ( !( isset( $_SESSION[ 'id_user' ] ) && isset( $_SESSION[ 'username' ] ) && isset( $_SESSION[ 'login' ] ) ) )
    header( 'location:'. APP_PATH);