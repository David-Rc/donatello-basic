<?php
session_start();

define( 'HOME_PATH', 'http://localhost/labz/simplon/php_exemples/simpllo/home.php' );

if ( isset( $_SESSION[ 'id_user' ] ) && isset( $_SESSION[ 'username' ] ) && isset( $_SESSION[ 'login' ] ) )
    header( 'location:' . HOME_PATH );

require_once "templates/topbar.php";
?>
<!doctype html>
<html lang="fr_FR">
<head>
    <meta charset="UTF-8">
    <title>Identification</title>
    <link rel="stylesheet" href="styles/base.css"/>
    <link rel="stylesheet" href="styles/simpllo.css"/>
</head>
<body>

<?php echo_topbar(); ?>

<div class="card-form">
    <?php
    if ( isset( $_GET[ 'subscribe' ] ) || isset( $_GET[ 'subscribe_error' ] ) ) {
        ?>

        <form method="post" action="subscribe.php">

            <?php if ( isset( $_GET[ 'subscribe_error' ] ) ) { ?>
                <div class="form-header-error">Erreur : le compte n'a pu être créé !</div>
            <?php } ?>

            <div class="form-header">Inscription</div>
            <div class="form-field">
                <div class="label-box"><label for="fld_username">Nom d'utilisateur</label></div>
                <input id="fld_username" name="username">
            </div>
            <div class="form-field">
                <div class="label-box"><label for="fld_user_login">Email</label></div>
                <input id="fld_user_login" name="user_login">
            </div>
            <div class="form-field">
                <div class="label-box"><label for="user_pass">Mot de passe</label></div>
                <input id="fld_user_pass" name="user_pass" type="password">
            </div>
            <div class="control-bar" class="form-field">
                <a href="index.php">Identification</a>
                <!--<span class="pusher"></span>-->
                <button type="reset" class="r">Annuler</button>
                <button class="r">Inscription</button>
            </div>
        </form>


        <?php
    } else {
        ?>

        <?php if ( isset( $_GET[ 'login_error' ] ) ) { ?>
            <div class="form-header-error">Erreur d'identification</div>
        <?php } ?>

        <form method="post" action="login.php">
            <div class="form-header">Identification</div>
            <div class="form-field">
                <div class="label-box"><label for="user_login">Email</label></div>
                <input id="user_login" name="user_login">
            </div>
            <div class="form-field">
                <div  class="label-box"><span><label id="user_pass" for="user_pass">Mot de passe</label></span></div>
                <input id="user_pass" name="user_pass" type="password">
            </div>
            <div class="control-bar">
                <a href="index.php?subscribe=1">Nouveau membre ?</a>
                <span class="pusher"></span>
                <button class="action bt-validate" style="width: 60px;">Entrer</button>
            </div>
        </form>
        <?php
    }
    ?>
</div>
</body>
</html>
