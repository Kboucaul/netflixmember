<?php
    session_start();
    if (isset($_SESSION['connect']))
    {
        session_unset();
        session_destroy();
        //On rend le cookie perimé
        setcookie('auth', '', time()-1, '/', null, false, true);
    }
    header('location: index.php');
    exit();
?>