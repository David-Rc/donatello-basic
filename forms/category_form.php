<?php
session_start();

require_once "../inc/auth.php";

require_once "../inc/db.php";
require_once "../inc/utils.php";

require_once "../managers/category_manager.php";

$db = getDB();

if ( isset( $_GET[ "current_project_id" ] ) ) {
    $idProject = $_GET[ "current_project_id" ];
}

if ( isset( $_POST[ "category_name" ] ) && isset( $_POST['id_project'] ) ) {
    addCategory( $_POST[ "category_name" ], $_POST['id_project'] );
}

?>

<?php require_once "../templates/head.php"; ?>

    <div><a href="../home.php">Retour à la liste</a></div>

    <div class="card-form">

        <form action="<?php echo $_SERVER[ 'PHP_SELF' ]; ?>" method="post">
            <div>
                <div class="label-box"><label for="fld_category_name">Nom</label></div>
                <input id="fld_category_name" name="category_name">
                <?php
                if ( isset( $idProject ) ) {
                    ?>
                    <input type="hidden" name="id_project" value="<?php echo $idProject; ?>">
                <?php } ?>
            </div>

            <div class="control-bar">
                <a href="../home.php">Annuler</a>
                <span class="pusher"></span>
                <button>Enregistrer</button>
            </div>
        </form>
    </div>

<?php require_once "../templates/foot.php"; ?>