<?php
function echo_topbar()
{
    $logoutView = '';
    if (isset($_SESSION['id_user']))
        $logoutView = '<a id="logout" href="' . APP_PATH . '/logout.php">Déconnexion</a>';

    echo '<div id="topbar">' . $logoutView . '
        <h1>Donatello</h1>
    </div>';
}

?>