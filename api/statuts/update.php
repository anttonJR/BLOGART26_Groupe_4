<?php
// CRUD Statuts (API) : UPDATE
// 1. Démarrer la session pour les messages
session_start();
require_once '../../functions/csrf.php';

$token = $_POST['csrf_token'] ?? '';
if (!verifyCSRFToken($token)) {
    die('Token CSRF invalide');
}

// 2. Inclure les fonctions nécessaires
require_once '../../functions/query/update.php';
require_once '../../functions/ctrlSaisies.php';

// 3. Vérifier que la requête est bien en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/backend/statuts/list.php');
    exit;
}

// 4. Récupérer les données du formulaire
$numStat = $_POST['numStat'] ?? null;
$libStat = trim($_POST['libStat'] ?? '');

// 5. Validation des données
$errors = [];

// Vérifier que l'ID existe
if (!$numStat) {
    $errors[] = "ID du statut manquant";
}

// Vérifier que le libellé n'est pas vide
if (empty($libStat)) {
    $errors[] = "Le libellé du statut est obligatoire";
}

// Vérifier la longueur du libellé (max 25 caractères)
if (strlen($libStat) > 25) {
    $errors[] = "Le libellé ne peut pas dépasser 25 caractères";
}

// 6. S'il y a des erreurs, retour au formulaire
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old_data'] = $_POST;
    header('Location: ../../views/backend/statuts/edit.php?id=' . $numStat);
    exit;
}

// 7. Préparer les données pour la mise à jour
$data = [
    'libStat' => $libStat
];

// 8. Exécuter la mise à jour
try {
    $result = update('STATUT', $data, 'numStat', $numStat);
    
    if ($result) {
        $_SESSION['success'] = "Statut mis à jour avec succès";
    } else {
        $_SESSION['error'] = "Erreur lors de la mise à jour";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur : " . $e->getMessage();
}

// 9. Redirection vers la liste
header('Location: ../../views/backend/statuts/list.php');
exit;
?>