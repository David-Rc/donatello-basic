<?php
session_start();

require_once "inc/auth.php";
require_once "inc/db.php";
require_once "inc/utils.php";

require_once "templates/topbar.php";

$db = getDB();

function getUserProjects ( $idUser )
{
    global $db;
    error_log( "getUserProjects $idUser" );

    $q = "SELECT id_project, title,notes FROM lo_projects ";
    $q .= " NATURAL JOIN lo_users_projects WHERE lo_users_projects.id_user = :idUser";
    $req = $db->prepare( $q );
    $req->bindParam( ":idUser", $idUser, PDO::PARAM_INT );
    $req->execute();
    $projects = $req->fetchAll();
    $req->closeCursor();
    return $projects;
}


function getProjectCategories ( $idProject )
{
    global $db;

    $q = "SELECT id_category, name, notes FROM lo_categories WHERE id_project = :idProject";
    $req = $db->prepare( $q );
    $req->bindParam( ":idProject", $idProject, PDO::PARAM_INT );
    $req->execute();

    $categories = $req->fetchAll();
    foreach ( $categories as &$category ) {
        $category[ 'tasks' ] = getCategoryTasks( $category[ 'id_category' ] );
        error_log( 'num tasks ' . $category[ 'id_category' ] . " " . count( $category[ 'tasks' ] ) );
    }
    unset( $category );
    $req->closeCursor();
    return $categories;
}


function getCategoryTasks ( $idCategory )
{
    global $db;

    $q = "SELECT id_task, title, added, completed FROM lo_tasks WHERE `id_category`= :idCategory ";
    $q .= " ORDER BY completed ASC, added DESC";
    try {
        $query = $db->prepare( $q );
        $query->bindValue( ":idCategory", $idCategory, PDO::PARAM_INT );
        $query->execute();

        return $query->fetchAll();
    } catch ( PDOException $err ) {
        error_log( '!!! ERROR | ' . __METHOD__ . ' | ' . $err->getCode() . " | " . $err->getMessage() . " | " . $err->errorInfo[ 1 ] );
    }

    return null;
}

function addTask ( $title, $idCategory )
{
    global $db;
    $q = "INSERT INTO lo_tasks (`title`, `id_category`, `added`) ";
    $q .= "VALUES (:title, :idCategory, NOW())";

    error_log( "addTask Q $q , $idCategory, $title" );

    $req = $db->prepare( $q );
    $req->bindParam( ":title", $title, PDO::PARAM_STR );
    $req->bindParam( ":idCategory", $idCategory, PDO::PARAM_INT );

    return $req->execute();
}

function updateTask ( $idTask, $completed )
{
    global $db;
    $q = "UPDATE lo_tasks SET `completed` = " . ( $completed == true ? 'NOW()' : 'NULL' ) . " WHERE `id_task`=:idTask ";

    error_log( "updateTask Q $q , $idTask, $completed" );

    $req = $db->prepare( $q );
    $req->bindParam( ":idTask", $idTask, PDO::PARAM_INT );

    return $req->execute();
}

function clearArchives ( $idProject )
{
    global $db;

    $q = "DELETE t.* FROM lo_tasks AS t NATURAL JOIN lo_categories AS c ";
    $q .= "WHERE t.completed IS NOT NULL AND c.id_project = :idProject";

    try {
        $query = $db->prepare( $q );
        $query->bindValue( ':idProject', $idProject, PDO::PARAM_INT );
        $result = $query->execute();

        return $result;
    } catch ( PDOException $err ) {
        error_log( '!!! ERROR | ' . __METHOD__ . ' | ' . $err->getCode() . " | " . $err->getMessage() . " | " . $err->errorInfo[ 1 ] );
    }

    return null;
}

function deleteProject ( $idProject )
{

    global $db;

    $q = "DELETE p.* FROM lo_projects AS p NATURAL JOIN lo_users_projects AS up WHERE p.`id_project`=:idProject AND up.id_user = :idUser";
    error_log( $q );
    try {
        $query = $db->prepare( $q );
        $query->bindValue( ':idProject', $idProject, PDO::PARAM_INT );
        $idUser = $_SESSION[ 'id_user' ];
        $query->bindValue( ':idUser', $idUser, PDO::PARAM_INT );
        return $query->execute();

    } catch ( PDOException $err ) {
        error_log( '!!! ERROR | ' . __METHOD__ . ' | ' . $err->getCode() . " | " . $err->getMessage() . " | " . $err->errorInfo[ 1 ] );
    }

    return null;
}

function deleteCategory( $idCategory )
{
    global $db;

    $q = "DELETE * FROM lo_category as c NATURAL JOIN lo_users_projects as up WHERE `id_category`=:idCategory AND `up`.`id_user` = :idUser";
    error_log( $q );
    try {
        $query = $db->prepare( $q );
        $query->bindValue( ':idCategory', $idCategory, PDO::PARAM_INT );
        $idUser = $_SESSION[ 'id_user' ];
        $query->bindValue( ':idUser', $idUser, PDO::PARAM_INT );
        return $query->execute();

    } catch ( PDOException $err ) {
        error_log( '!!! ERROR | ' . __METHOD__ . ' | ' . $err->getCode() . " | " . $err->getMessage() . " | " . $err->errorInfo[ 1 ] );
    }

    return null;
}

function deselectProject ()
{
    unset( $_SESSION[ 'selected_project_id' ] );
    unset( $_SESSION[ 'selected_project' ] );
}

/*
 * SELF ACTIONS
*/

if ( isset( $_POST[ "action" ] ) ) {

    switch ( $_POST[ "action" ] ) {
        case 'new_task':
            if ( isset( $_POST[ "task_title" ] ) && isset( $_POST[ "id_category" ] ) )
                if ( addTask( $_POST[ "task_title" ], $_POST[ "id_category" ] ) ) {
                    header( "HTTP/1.1 302 Redirect" );
                    header( "location:home.php" );
                };
            break;
        case 'update_task_state':
            if ( isset( $_POST[ "id_task" ] ) ) {
                error_log( 'update task » ' . $_POST[ "id_task" ] . " " . isset( $_POST[ "is_complete" ] ) );
                updateTask( $_POST[ "id_task" ], isset( $_POST[ "is_complete" ] ) );
            }
            break;
        case 'clear_completed':
            if ( isset( $_POST[ "id_project" ] ) ) {
                error_log( 'clear_completed task » ' . $_POST[ "id_project" ] );
                clearArchives( $_POST[ "id_project" ] );
            }
            break;
        case 'delete_project':
            if ( isset( $_POST[ "id_project" ] ) ) {
                error_log( 'delete_project task » ' . $_POST[ "id_project" ] );
                if ( deleteProject( $_POST[ "id_project" ] ) ) {
                    error_log( "project deleted" );
                    deselectProject();
                    header( "HTTP/1.1 302 Redirect" );
                    header( "location:home.php" );
                }
            }
            break;
        case 'delete_category':
            if ( isset( $_POST[ "id_category" ] ) ) {
                error_log( 'delete_category task » ' . $_POST[ "id_category" ] );
                if ( deleteCategory( $_POST[ "id_category" ] ) ) {
                    error_log( "category deleted" );
                    //deselectProject();
                    header( "HTTP/1.1 302 Redirect" );
                    header( "location:home.php" );
                }
            }
            break;
        default:
            error_log( 'undefined action : ' . $_POST[ "action" ] );
            break;
    }
}

// recup les projets de l'utilisateur connecté
$projects = getUserProjects( $_SESSION[ 'id_user' ] );

// si un projet est sélectionné
if ( isset( $_GET[ 'selected_project' ] ) ) {
    $currentProjectId = $_GET[ 'selected_project' ];
    $currentProject = searchWhere( $projects, "id_project", $currentProjectId );
    $_SESSION[ "selected_project_id" ] = $currentProjectId;
    $_SESSION[ "selected_project" ] = $currentProject;

    // print_r( $currentProject );
} // si un projet est sélectionné ( session )
else if ( isset( $_SESSION[ 'selected_project_id' ] ) ) {
    $currentProjectId = $_SESSION[ 'selected_project_id' ];
    $currentProject = $_SESSION[ 'selected_project' ];
    //$currentProject = searchWhere( $projects, "id_project", $currentProjectId );

    // print_r( $currentProject );
} else if ( count( $projects ) > 0 ) {
    // si aucun item sélectionné et qu'il existe des projets : selectionne le 1er projet
    $currentProject = $projects[ 0 ];
    $currentProjectId = $currentProject[ "id_project" ];
    error_log( "default project selection $currentProjectId » " . $currentProject[ 'title' ] );
}

// chargement des catégories du projet selectionné
if ( isset( $currentProjectId ) ) {
    $projectCategories = getProjectCategories( $currentProjectId );

    error_log( "projectCategories " . count( $projectCategories ) );
}
?>

<!doctype html>
<html lang="fr_FR">
<head>
    <meta charset="UTF-8">
    <title>Donatello - Home</title>
    <link rel="stylesheet" href="styles/base.css"/>
    <link rel="stylesheet" href="styles/simpllo.css"/>
    <script type="text/javascript" src="js/lib.js"></script>
</head>
<body>

<?php echo_topbar(); ?>

<?php
// si utilisateur vient d'être inscrit : proposition tuto ( non implémenté )
if ( isset( $_GET[ 'new_user' ] ) ) {
    ?>
    <div id="tutoLink" class="info-box">
        <p>Bienvenue sur Donatello <?php echo $_SESSION[ 'username' ]; ?>, souhaitez-vous voir le tutoriel ?
            <a href="tutorial">Oui</a> - <a href="#" onclick="hideTutoLink()">Plus tard</a></p>
    </div>
    <?php
}
?>

<div id="container">
    <div id="projects-box">
        <h2>Projets</h2>
        <div class="box">
            <a class="add" href="forms/project_form.php">Nouveau projet</a>
        </div>
        <?php

        foreach ( $projects as $project ) {
            $selected = ( $project[ 'id_project' ] == $currentProjectId ) ? 'selected' : '';
            $selectionLink = $_SERVER[ 'PHP_SELF' ] . "?selected_project=" . $project[ 'id_project' ];
            ?>

            <div class="project <?php echo $selected; ?>">
                <a href="<?php echo $selectionLink; ?>">
                    <?php echo $project[ 'title' ]; ?>
                </a>
            </div>

        <?php } ?>

    </div>

    <?php if ( isset( $currentProject ) ) { ?>
        <div id="detail-box">
            <div class="detail-header">

                <h2><?php if ( isset( $currentProject ) ) {
                        echo $currentProject[ 'title' ];
                    } ?>
                    <form action="<?php echo $_SERVER[ 'PHP_SELF' ]; ?>" method="post">
                        <input type="hidden" name="id_project" value="<?php echo $currentProjectId ?>"/>
                        <input type="hidden" name="action" value="delete_project"/>
                        <button>Supprimer</button>
                    </form>
                    <span class="clr"></span>
                </h2>

            </div>
            <div class="toolbar">
                <a href="forms/category_form.php?current_project_id=<?php echo $currentProjectId; ?>">
                    Ajouter une catégorie</a>
            <span>
                <input id="chk_showArchives" type="checkbox" onchange="updateArchivesVisibility()"/>
                <label for="chk_showArchives">Afficher les tâches complétées</label>
            </span>
                <form class="inline-form" action="<?php echo $_SERVER[ 'PHP_SELF' ]; ?>" method="post">
                    <input type="hidden" name="id_project" value="<?php echo $currentProjectId ?>"/>
                    <input type="hidden" name="action" value="clear_completed"/>
                    <button>Supprimer les archives</button>
                </form>
            </div>
            <?php
            foreach ( $projectCategories as $category ) {
                $name = $category[ 'name' ];
                error_log( 'category tasks ' . count( $category[ 'tasks' ] ) );
                ?>
                <div class="category-box">
                    <h4 class="category"><?php echo $name; ?>
                        <form action="<?php echo $_SERVER[ 'PHP_SELF' ]; ?>">
                            <input type="hidden" name="id_category"
                                   value="<?php echo $category[ 'id_category' ]; ?>"/>
                            <input type="hidden" name="action" value="delete_category"/>
                            <span><a onclick="this.form.submit()" href="#">Supprimer</a></span>
                        </form>
                    </h4>
                    <div class="new-task-box">
                        <form action="<?php echo $_SERVER[ 'PHP_SELF' ]; ?>" method="post">
                            <input class="t-input" type="text" name="task_title" required/>
                            <!--<input class="dt-input" type="datetime-local" name="due" width="60"/>-->
                            <input type="hidden" name="id_category"
                                   value="<?php echo $category[ 'id_category' ]; ?>"/>
                            <input type="hidden" name="action" value="new_task"/>
                            <button>Ajouter</button>
                        </form>
                    </div>
                    <ul class="task-list">
                        <?php
                        if ( isset( $category[ 'tasks' ] ) ) {
                            error_log( 'task renderer' . count( $category[ 'tasks' ] ) );
                            foreach ( $category[ 'tasks' ] as $task ) {
                                $idTask = $task[ 'id_task' ];
                                $title = $task[ 'title' ];
                                $checkId = "chkbox_" . $idTask;
                                $isSelected = $task[ 'completed' ] == false ? '' : ' checked ';
                                error_log( '$task[ \'completed\' ] ' . $task[ 'completed' ] . ' » ' . ( $task[ 'completed' ] == false ) . ' s ' . $isSelected );
                                ?>
                                <li class="task" data-complete="<?php echo $isSelected ? 1 : 0; ?>">
                                    <form method="post" action="<?php echo $_SERVER[ 'PHP_SELF' ]; ?>">
                                        <input id="<?php echo $checkId; ?>" type="checkbox" name="is_complete" value="1" <?php echo $isSelected; ?>
                                               onchange="this.form.submit()"
                                        >
                                        <label for="<?php echo $checkId; ?>"><?php echo $title; ?></label>
                                        <input type="hidden" name="id_task" value="<?php echo $idTask; ?>"/>
                                        <input type="hidden" name="action" value="update_task_state"/>

                                    </form>
                                </li>
                            <?php }
                        } ?>

                    </ul>
                </div>
                <?php
            }
            ?>
        </div>
    <?php } ?>
</div>
<script>

    function updateArchivesVisibility() {
        var showArchives = byId('chk_showArchives').checked;
        var tasks = byClass("task");
        tasks.forEach(function (task) {
            console.log('task state', task.dataset.complete);
            if (task.dataset.complete == 1)
                task.style.display = showArchives ? 'block' : 'none';
        })
    }

    function hideTutoLink() {
        console.log('hideTutoLink');
        hide(byId('tutoLink'));
    }

    window.onload = updateArchivesVisibility;

</script>

</body>
</html>