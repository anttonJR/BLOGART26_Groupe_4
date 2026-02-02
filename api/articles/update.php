<?php
session_start();
require_once '../../functions/query/update.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/backend/articles/list.php');
    exit;
}
// Ajouter la récupération
$numThem = $_POST['numThem'] ?? null;

// Ajouter la validation
if (!$numThem) {
    $errors[] = "La thématique est obligatoire";
}

// Ajouter au tableau $data
$data = [
    // ... autres champs ...
    'numThem' => $numThem
];

require_once '../../functions/upload.php';
require_once '../../functions/query/select.php';

// Charger l'article actuel
$artActuel = selectOne('ARTICLE', 'numArt', $numArt);

// ... après la validation ...

// Gérer l'upload de la nouvelle image
$urlPhotArt = $artActuel['urlPhotArt']; // Conserver l'ancienne par défaut

if (isset($_FILES['imageArt']) && $_FILES['imageArt']['error'] !== UPLOAD_ERR_NO_FILE) {
    $uploadResult = uploadImage($_FILES['imageArt']);
    
    if ($uploadResult['success']) {
        // Supprimer l'ancienne image si elle existe
        if ($artActuel['urlPhotArt']) {
            deleteImage($artActuel['urlPhotArt']);
        }
        
        $urlPhotArt = $uploadResult['filename'];
    } else {
        $errors[] = $uploadResult['error'];
    }
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: ../../views/backend/articles/edit.php?id=' . $numArt);
    exit;
}

// Ajouter au tableau $data
$data = [
    // ... autres champs ...
    'urlPhotArt' => $urlPhotArt
];
// Récupération des données

$numArt = $_POST['numArt'] ?? null;
$libTltArt = trim($_POST['libTltArt'] ?? '');
$libChapArt = trim($_POST['libChapArt'] ?? '');
$parag1Art = trim($_POST['parag1Art'] ?? '');
$parag2Art = trim($_POST['parag2Art'] ?? '');
$parag3Art = trim($_POST['parag3Art'] ?? '');
$libSsTitl1Art = trim($_POST['libSsTitl1Art'] ?? '');
$libAccrochArt = trim($_POST['libAccrochArt'] ?? '');
$libConcArt = trim($_POST['libConcArt'] ?? '');

// Validation
$errors = [];

if (!$numArt) {
    $errors[] = "ID manquant";
}

if (empty($libTltArt)) {
    $errors[] = "Le titre est obligatoire";
}

if (empty($libChapArt)) {
    $errors[] = "Le chapô est obligatoire";
}

if (empty($parag1Art)) {
    $errors[] = "Le paragraphe 1 est obligatoire";
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: ../../views/backend/articles/edit.php?id=' . $numArt);
    exit;
}

// Préparation des données
$data = [
    'dtMajArt' => date('Y-m-d H:i:s'),
    'libTltArt' => $libTltArt,
    'libChapArt' => $libChapArt,
    'parag1Art' => $parag1Art,
    'parag2Art' => $parag2Art ?: null,
    'parag3Art' => $parag3Art ?: null,
    'libSsTitl1Art' => $libSsTitl1Art ?: null,
    'libAccrochArt' => $libAccrochArt ?: null,
    'libConcArt' => $libConcArt ?: null
];

try {
    $result = update('ARTICLE', $data, 'numArt', $numArt);
    if ($result) {
        $_SESSION['success'] = "Article mis à jour";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur : " . $e->getMessage();
}

header('Location: ../../views/backend/articles/list.php');
exit;
?>