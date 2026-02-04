<?php
require_once __DIR__ . '/../../../config.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;

if (!isset($_GET['id'])) {
    header('Location: ' . ROOT_URL . '/views/backend/comments/list.php');
    exit;
}

$id = (int)$_GET['id'];

$stmt = $DB->prepare("DELETE FROM COMMENT WHERE numCom = ?");
$stmt->execute([$id]);

$_SESSION['success'] = "Commentaire supprimé avec succès";
header('Location: ' . ROOT_URL . '/views/backend/comments/list.php');
exit;
