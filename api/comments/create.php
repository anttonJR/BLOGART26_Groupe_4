<?php
session_start();
require_once '../../config.php';
require_once ROOT . '/functions/csrf.php';
require_once ROOT . '/functions/auth.php';

// Vérifier le token CSRF
$token = $_POST['csrf_token'] ?? '';
if (!verifyCSRFToken($token)) {
    $_SESSION['comment_error'] = 'Token CSRF invalide';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? ROOT_URL . '/index.php'));
    exit;
}

// Vérifier que l'utilisateur est connecté
if (!isLoggedIn()) {
    $_SESSION['comment_error'] = "Vous devez être connecté pour commenter";
    header('Location: ' . ROOT_URL . '/views/frontend/security/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . ROOT_URL . '/index.php');
    exit;
}

// Récupération des données
$numArt = $_POST['numArt'] ?? null;
$libCom = trim($_POST['libCom'] ?? '');
$numMemb = $_SESSION['user']['numMemb'];

// Validation
$errors = [];

if (!$numArt) {
    $errors[] = "Article non spécifié";
}

if (empty($libCom)) {
    $errors[] = "Le commentaire ne peut pas être vide";
}

if (strlen($libCom) < 10) {
    $errors[] = "Le commentaire doit contenir au moins 10 caractères";
}

if (!empty($errors)) {
    $_SESSION['comment_error'] = implode(', ', $errors);
    header('Location: ' . ROOT_URL . '/views/frontend/articles/article1.php?id=' . $numArt);
    exit;
}

// Génération du numéro de commentaire
global $DB;
$sql = "SELECT MAX(numCom) as max FROM COMMENT";
$stmt = $DB->query($sql);
$result = $stmt->fetch();
$numCom = ($result['max'] ?? 0) + 1;

try {
    // Insertion du commentaire
    $sqlInsert = "INSERT INTO COMMENT (numCom, dtCreaCom, libCom, attModOK, numMemb, numArt) 
                  VALUES (?, NOW(), ?, 0, ?, ?)";
    $stmtInsert = $DB->prepare($sqlInsert);
    $stmtInsert->execute([$numCom, $libCom, $numMemb, $numArt]);
    
    $_SESSION['comment_success'] = "Votre commentaire a été envoyé. Il sera visible après validation par un modérateur.";
} catch (Exception $e) {
    $_SESSION['comment_error'] = "Erreur : " . $e->getMessage();
}

header('Location: ' . ROOT_URL . '/views/frontend/articles/article1.php?id=' . $numArt);
exit;
?>