<?php
/**
 * Vérifie si l'utilisateur peut tenter de se connecter
 * @param string $ip
 * @return bool
 */
function canAttemptLogin($ip) {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = [];
    }
    
    // Nettoyer les tentatives de plus de 15 minutes
    $now = time();
    $_SESSION['login_attempts'] = array_filter(
        $_SESSION['login_attempts'],
        function($timestamp) use ($now) {
            return ($now - $timestamp) < 900; // 15 minutes
        }
    );
    
    // Vérifier le nombre de tentatives
    return count($_SESSION['login_attempts']) < 5;
}

/**
 * Enregistre une tentative de connexion
 */
function recordLoginAttempt() {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = [];
    }
    $_SESSION['login_attempts'][] = time();
}

/**
 * Réinitialise les tentatives (après connexion réussie)
 */
function resetLoginAttempts() {
    $_SESSION['login_attempts'] = [];
}
?>