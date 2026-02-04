<?php
require_once __DIR__ . '/../../../config.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;

if (!isset($_GET['id'])) {
    header('Location: ' . ROOT_URL . '/views/backend/keywords/list.php');
    exit;
}

$id = (int)$_GET['id'];

// Supprimer les liaisons avec les articles
$stmt = $DB->prepare("DELETE FROM MOTCLEARTICLE WHERE numMotCle = ?");
$stmt->execute([$id]);

$stmt = $DB->prepare("DELETE FROM MOTCLE WHERE numMotCle = ?");
$stmt->execute([$id]);

$_SESSION['success'] = "Mot-clé supprimé avec succès";
header('Location: ' . ROOT_URL . '/views/backend/keywords/list.php');
exit;
