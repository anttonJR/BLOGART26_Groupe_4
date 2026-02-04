<?php
require_once __DIR__ . '/../../../config.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;

if (!isset($_GET['id'])) {
    header('Location: ' . ROOT_URL . '/views/backend/statuts/list.php');
    exit;
}

$id = (int)$_GET['id'];

// Ne pas supprimer les statuts de base (1, 2, 3)
if ($id <= 3) {
    $_SESSION['error'] = "Impossible de supprimer les statuts de base";
    header('Location: ' . ROOT_URL . '/views/backend/statuts/list.php');
    exit;
}

$stmt = $DB->prepare("DELETE FROM STATUT WHERE numStat = ?");
$stmt->execute([$id]);

$_SESSION['success'] = "Statut supprimé avec succès";
header('Location: ' . ROOT_URL . '/views/backend/statuts/list.php');
exit;
