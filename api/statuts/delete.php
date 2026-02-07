<?php
// CRUD Statuts (API) : DELETE
session_start();
require_once '../../functions/csrf.php';

$token = $_POST['csrf_token'] ?? '';
if (!verifyCSRFToken($token)) {
    die('Token CSRF invalide');
}

// Chargement de la config + helpers
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once '../../functions/ctrlSaisies.php';

// Récupération de l'ID statut
$numStat = ($_POST['numStat']);

// Suppression du statut
sql_delete('STATUT', "numStat = $numStat");

// Redirection vers la liste
header('Location: ../../views/backend/statuts/list.php');