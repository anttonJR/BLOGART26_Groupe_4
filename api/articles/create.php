<?php
session_start();
require_once '../../functions/query/insert.php'; //active la session PHP → permet d’utiliser $_SESSION (pour stocker erreurs, succès, etc.).

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/backend/articles/list.php');
    exit;
}//regarde si c get ou post si c'est pas post redirige vers la liste des articles

// Ajouter la récupération de la thématique
$numThem = $_POST['numThem'] ?? null;

// Ajouter la validation
if (!$numThem) {
    $errors[] = "La thématique est obligatoire";
}

// Modifier le tableau $data
$data = [
    // ... autres champs ...
    'numThem' => $numThem  // Au lieu de null
];

require_once '../../functions/upload.php';

// ... après la validation des autres champs ...

// Gérer l'upload de l'image
$urlPhotArt = null;
if (isset($_FILES['imageArt']) && $_FILES['imageArt']['error'] !== UPLOAD_ERR_NO_FILE) {
    $uploadResult = uploadImage($_FILES['imageArt']);
    
    if ($uploadResult['success']) {
        $urlPhotArt = $uploadResult['filename'];
    } else {
        $errors[] = $uploadResult['error'];
    }
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: ../../views/backend/articles/create.php');
    exit;
}

// Modifier le tableau $data
$data = [
    // ... autres champs ...
    'urlPhotArt' => $urlPhotArt  // Au lieu de null
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

// creation des erreurs de validation
$errors = [];

if (!$numArt) {
    $errors[] = "Le numéro d'article est obligatoire";
}

if (empty($libTltArt)) {
    $errors[] = "Le titre est obligatoire";
}

if (strlen($libTltArt) > 100) {
    $errors[] = "Le titre ne peut pas dépasser 100 caractères";
}

if (empty($libChapArt)) {
    $errors[] = "Le chapô est obligatoire";
}

if (empty($parag1Art)) {
    $errors[] = "Le paragraphe 1 est obligatoire";
}

// S'il y au moins une erreurs, retour au formulaire
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;//on stocke les erreurs en session pour les afficher dans la page create.php
    $_SESSION['old_data'] = $_POST; //on stocke aussi les anciennes valeurs saisies pour éviter que l’utilisateur retape tout.
    header('Location: ../../views/backend/articles/create.php');
    exit;
}

// construit un tableau qui coresspond a la table ARTICLE
$data = [
    'numArt' => $numArt,
    'dtCreaArt' => date('Y-m-d H:i:s'),
    'dtMajArt' => null,
    'libTltArt' => $libTltArt,
    'libChapArt' => $libChapArt,
    'parag1Art' => $parag1Art,
    'parag2Art' => $parag2Art ?: null, //si parag2Art est vide on met null au lieu de ''
    'parag3Art' => $parag3Art ?: null,
    'libSsTitl1Art' => $libSsTitl1Art ?: null,
    'libAccrochArt' => $libAccrochArt ?: null,
    'libConcArt' => $libConcArt ?: null,
    'urlPhotArt' => null,
    'numThem' => null
];

try { // gere les erreurs de la BDD
    $result = insert('ARTICLE', $data); // creer un fonction insert pour inserer dans la table article
    if ($result) {
        $_SESSION['success'] = "Article créé avec succès";// si ca marche succes 
    }
} catch (Exception $e) {//si ca flop on recuperer l erreur dans $e->getMessage()
    $_SESSION['error'] = "Erreur : " . $e->getMessage();//on met un message d’erreur dans $_SESSION['error']
}

header('Location: ../../views/backend/articles/list.php'); //Peu importe succès ou erreur, tu reviens à la liste des articles.
exit;
?>