<?php
// CRUD Statuts (API) : CREATE
session_start();
require_once '../../functions/csrf.php';

$token = $_POST['csrf_token'] ?? '';
if (!verifyCSRFToken($token)) {
    die('Token CSRF invalide');
}

// Chargement de la config + helpers
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once '../../functions/ctrlSaisies.php';

// Récupération du libellé depuis le formulaire
$libStat = ($_POST['libStat']);

// Insertion du statut
sql_insert('STATUT', 'libStat', "'$libStat'");

// Redirection vers la liste
header('Location: ../../views/backend/statuts/list.php');