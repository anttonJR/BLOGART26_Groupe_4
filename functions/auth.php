<?php
/**
 * Vérifie si l'utilisateur est connecté
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Vérifie si l'utilisateur est administrateur
 * @return bool
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user']['numStat']) && $_SESSION['user']['numStat'] == 3;
}

/**
 * Vérifie si l'utilisateur est modérateur
 * @return bool
 */
function isModerator() {
    return isLoggedIn() && isset($_SESSION['user']['numStat']) && $_SESSION['user']['numStat'] == 2;
}

/**
 * Redirige vers la page de connexion si non connecté
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = "Vous devez être connecté pour accéder à cette page";
        header('Location: /views/frontend/security/login.php');
        exit;
    }
}

/**
 * Redirige si pas admin
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        $_SESSION['error'] = "Accès interdit";
        header('Location: /views/frontend/index.php');
        exit;
    }
}

/**
 * Redirige si pas modérateur ou admin
 */
function requireModerator() {
    requireLogin();
    if (!isModerator() && !isAdmin()) {
        $_SESSION['error'] = "Accès interdit";
        header('Location: /views/frontend/index.php');
        exit;
    }
}

?>
<?php
session_start();
require_once '../../functions/auth.php';
requireAdmin(); // Vérifie que l'utilisateur est admin
?>
<!DOCTYPE html>
<html>
<head>
    <title>Panneau d'administration</title>
</head>
<body>
    <h1>Bienvenue, <?= htmlspecialchars($_SESSION['user']['prenomMemb']) ?> !</h1>
    <!-- Contenu du panneau -->
</body>
</html>