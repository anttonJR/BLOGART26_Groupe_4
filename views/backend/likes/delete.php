<?php
require_once __DIR__ . '/../../../config.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;

if (!isset($_GET['art']) || !isset($_GET['memb'])) {
    header('Location: ' . ROOT_URL . '/views/backend/likes/list.php');
    exit;
}

$numArt = (int)$_GET['art'];
$numMemb = (int)$_GET['memb'];

$stmt = $DB->prepare("DELETE FROM LIKEART WHERE numArt = ? AND numMemb = ?");
$stmt->execute([$numArt, $numMemb]);

$_SESSION['success'] = "Like supprimé avec succès";
header('Location: ' . ROOT_URL . '/views/backend/likes/list.php');
exit;
