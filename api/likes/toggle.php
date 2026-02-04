<?php
session_start();
require_once '../../config.php';
require_once ROOT . '/functions/csrf.php';
require_once ROOT . '/functions/auth.php';

// Vérifier que la requête est bien en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Méthode non autorisée';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? ROOT_URL . '/index.php'));
    exit;
}

// Vérifier le token CSRF
$token = $_POST['csrf_token'] ?? '';
if (!verifyCSRFToken($token)) {
    $_SESSION['error'] = 'Token CSRF invalide';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? ROOT_URL . '/index.php'));
    exit;
}

// Vérifier que l'utilisateur est connecté
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Vous devez être connecté pour liker';
    header('Location: ' . ROOT_URL . '/views/frontend/security/login.php');
    exit;
}

// Récupérer les données POST (formulaire classique)
$numArt = $_POST['numArt'] ?? null;

// Validation de base
if (!$numArt) {
    $_SESSION['error'] = 'Article non spécifié';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? ROOT_URL . '/index.php'));
    exit;
}

$numMemb = $_SESSION['user']['numMemb'];

try {
    global $DB;
    
    // Vérifier si le like existe déjà
    $sqlCheck = "SELECT likeA FROM LIKEART WHERE numMemb = ? AND numArt = ?";
    $stmtCheck = $DB->prepare($sqlCheck);
    $stmtCheck->execute([$numMemb, $numArt]);
    $existing = $stmtCheck->fetch();
    
    if ($existing) {
        // Le like existe → toggle (0 → 1 ou 1 → 0)
        $newValue = $existing['likeA'] == 1 ? 0 : 1;
        $sqlUpdate = "UPDATE LIKEART SET likeA = ? WHERE numMemb = ? AND numArt = ?";
        $stmtUpdate = $DB->prepare($sqlUpdate);
        $stmtUpdate->execute([$newValue, $numMemb, $numArt]);
    } else {
        // Le like n'existe pas → créer
        $sqlInsert = "INSERT INTO LIKEART (numMemb, numArt, likeA) VALUES (?, ?, 1)";
        $stmtInsert = $DB->prepare($sqlInsert);
        $stmtInsert->execute([$numMemb, $numArt]);
    }
    
    $_SESSION['success'] = 'Like mis à jour !';
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Erreur : ' . $e->getMessage();
}

// Rediriger vers la page d'origine (ou l'article si pas de referer)
$referer = $_SERVER['HTTP_REFERER'] ?? null;
if ($referer) {
    header('Location: ' . $referer);
} else {
    header('Location: ' . ROOT_URL . '/views/frontend/articles/article1.php?id=' . $numArt);
}
exit;
?>