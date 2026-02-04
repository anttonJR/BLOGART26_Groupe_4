<?php
require_once __DIR__ . '/../../../config.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;

if (!isset($_GET['id'])) {
    header('Location: ' . ROOT_URL . '/views/backend/thematiques/list.php');
    exit;
}

$id = (int)$_GET['id'];

$stmt = $DB->prepare("DELETE FROM THEMATIQUE WHERE numThem = ?");
$stmt->execute([$id]);

$_SESSION['success'] = "Thématique supprimée avec succès";
header('Location: ' . ROOT_URL . '/views/backend/thematiques/list.php');
exit;
