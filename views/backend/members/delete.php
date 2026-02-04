<?php
require_once __DIR__ . '/../../../config.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;

if (!isset($_GET['id'])) {
    header('Location: ' . ROOT_URL . '/views/backend/members/list.php');
    exit;
}

$id = (int)$_GET['id'];

// Ne pas supprimer l'admin principal
if ($id === 1) {
    $_SESSION['error'] = "Impossible de supprimer l'administrateur principal";
    header('Location: ' . ROOT_URL . '/views/backend/members/list.php');
    exit;
}

// Supprimer les likes du membre
$stmt = $DB->prepare("DELETE FROM LIKEART WHERE numMemb = ?");
$stmt->execute([$id]);

// Supprimer les commentaires du membre
$stmt = $DB->prepare("DELETE FROM COMMENT WHERE numMemb = ?");
$stmt->execute([$id]);

// Supprimer le membre
$stmt = $DB->prepare("DELETE FROM MEMBRE WHERE numMemb = ?");
$stmt->execute([$id]);

$_SESSION['success'] = "Membre supprimé avec succès";
header('Location: ' . ROOT_URL . '/views/backend/members/list.php');
exit;
