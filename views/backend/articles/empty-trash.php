<?php
require_once __DIR__ . '/../../../config.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;

// Récupérer tous les articles dans la corbeille
$stmt = $DB->query("SELECT numArt FROM ARTICLE WHERE delLogiq = 1");
$articles = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($articles as $numArt) {
    // Supprimer les likes
    $stmtLike = $DB->prepare("DELETE FROM LIKEART WHERE numArt = ?");
    $stmtLike->execute([$numArt]);
    
    // Supprimer les commentaires
    $stmtCom = $DB->prepare("DELETE FROM COMMENT WHERE numArt = ?");
    $stmtCom->execute([$numArt]);
    
    // Supprimer les mots-clés associés
    $stmtMot = $DB->prepare("DELETE FROM MOTCLEARTICLE WHERE numArt = ?");
    $stmtMot->execute([$numArt]);
}

// Supprimer tous les articles de la corbeille
$DB->exec("DELETE FROM ARTICLE WHERE delLogiq = 1");

$_SESSION['success'] = "Corbeille vidée avec succès";
header('Location: ' . ROOT_URL . '/views/backend/articles/list.php');
exit;
