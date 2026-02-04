<?php
require_once __DIR__ . '/../../../config.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;

if (!isset($_GET['id'])) {
    header('Location: ' . ROOT_URL . '/views/backend/articles/list.php');
    exit;
}

$id = (int)$_GET['id'];

// Soft delete - marquer comme supprimé au lieu de supprimer vraiment
$stmt = $DB->prepare("UPDATE ARTICLE SET delLogiq = 1, dtDelLogArt = NOW() WHERE numArt = ?");
$stmt->execute([$id]);

$_SESSION['success'] = "Article déplacé dans la corbeille";
header('Location: ' . ROOT_URL . '/views/backend/articles/list.php');
exit;
