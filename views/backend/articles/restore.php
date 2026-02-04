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

// Restaurer l'article
$stmt = $DB->prepare("UPDATE ARTICLE SET delLogiq = 0, dtDelLogArt = NULL WHERE numArt = ?");
$stmt->execute([$id]);

$_SESSION['success'] = "Article restauré avec succès";
header('Location: ' . ROOT_URL . '/views/backend/articles/trash.php');
exit;
