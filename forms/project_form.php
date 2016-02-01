<?php
session_start();

require_once "../inc/auth.php";

require_once "../inc/db.php";

require_once "../managers/category_manager.php";

$db = getDB();


function linkItems ($tableName,$id1Name, $id1, $id2Name, $id2)
{
    global $db;

    if ( !$db )
        return false;
    else {
        $q = "INSERT INTO `$tableName` (`$id1Name`, `$id2Name`) ";
        $q .= "VALUES (:id1, :id2) " ;
        error_log( "Q $q" );
        $req = $db->prepare( $q );
        $req->bindParam( ":id1", $id1, PDO::PARAM_INT );
        $req->bindParam( ":id2", $id2, PDO::PARAM_INT );

        return $req->execute();
    }
}

function addProject ( $projectName )
{
    global $db;

    if ( !$db )
        return false;
    else {
        $q = "INSERT INTO lo_projects(`title`)";
        $q .= " VALUES (:projectName)";

        $req = $db->prepare( $q );
        $req->bindParam( ":projectName", $projectName, PDO::PARAM_STR );

        if ( $req->execute() ) { // liaison
            $lastProjectId = $db->lastInsertId();
            error_log( 'project_created' . $lastProjectId );
            // link project / user
            $link_result = linkItems( "lo_users_projects", "id_user", $_SESSION[ 'id_user' ],"id_project", $lastProjectId );

            // creation d'une categorie Todo par défaut
            $defaultCategoryResult = addDefaultCategory($lastProjectId) ;
        }

        return $link_result;
    }
}

if ( isset( $_POST[ 'project_name' ] ) ) {
    if ( addProject( $_POST[ 'project_name' ] ) ) {
        header( 'location:../home.php');
    } else
        header( 'location:' . $_SERVER[ 'PHP_SELF' ] );
}
?>

<?php require_once "../templates/head.php"; ?>

<div class="card-form">

    <form action="<?php echo $_SERVER[ 'PHP_SELF' ]; ?>" method="post">
        <?php if ( isset( $_GET[ 'new_project_error' ] ) ) { ?>
            <div class="form-header-error">Erreur : le projet n'a pu être créé !</div>
        <?php } ?>

        <div>
            <div class="label-box"><label for="fld_project_name">Nom du projet</label></div>
            <input id="fld_project_name" name="project_name">
        </div>
        <div class="control-bar">
            <a href="../home.php">Annuler</a>
            <span class="pusher"></span>
            <button>Enregistrer</button>
        </div>
    </form>

</div>

<?php require_once "../templates/foot.php"; ?>