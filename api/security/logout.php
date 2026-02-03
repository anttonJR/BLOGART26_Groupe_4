<?php
session_start();

// Détruire toutes les données de session
$_SESSION = [];

// Détruire le cookie de session
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Détruire la session
session_destroy();

// Redirection
$_SESSION = [];
session_start();
$_SESSION['success'] = "Vous êtes maintenant déconnecté";
header('Location: ../../views/frontend/security/login.php');
exit;
?>