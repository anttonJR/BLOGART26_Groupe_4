<?php
require_once __DIR__ . '/../../../config.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;

if (!isset($_GET['id'])) {
    header('Location: ' . ROOT_URL . '/views/backend/articles/trash.php');
    exit;
}

$id = (int)$_GET['id'];

// Supprimer les likes associés
$stmt = $DB->prepare("DELETE FROM LIKEART WHERE numArt = ?");
$stmt->execute([$id]);

// Supprimer les commentaires associés
$stmt = $DB->prepare("DELETE FROM COMMENT WHERE numArt = ?");
$stmt->execute([$id]);

// Supprimer les mots-clés associés
$stmt = $DB->prepare("DELETE FROM MOTCLEARTICLE WHERE numArt = ?");
$stmt->execute([$id]);

// Supprimer définitivement l'article
$stmt = $DB->prepare("DELETE FROM ARTICLE WHERE numArt = ?");
$stmt->execute([$id]);

$_SESSION['success'] = "Article supprimé définitivement";
header('Location: ' . ROOT_URL . '/views/backend/articles/trash.php');
exit;
