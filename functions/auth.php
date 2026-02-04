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
    return isLoggedIn() && isset($_SESSION['user']['numStat']) && $_SESSION['user']['numStat'] == 1;
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
        header('Location: /BLOGART26/views/frontend/security/login.php');
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
        header('Location: /BLOGART26/index.php');
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
        header('Location: /BLOGART26/index.php');
        exit;
    }
}
?>