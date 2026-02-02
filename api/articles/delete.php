<?php
session_start();
require_once '../../functions/query/delete.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/backend/articles/list.php');
    exit;
}

$numArt = $_POST['numArt'] ?? null;

if (!$numArt) {
    $_SESSION['error'] = "ID manquant";
    header('Location: ../../views/backend/articles/list.php');
    exit;
}
require_once '../../functions/upload.php';
require_once '../../functions/query/select.php';

// Charger l'article
$art = selectOne('ARTICLE', 'numArt', $numArt);

if (!$art) {
    $_SESSION['error'] = "Article introuvable";
    header('Location: ../../views/backend/articles/list.php');
    exit;
}

try {
    // Supprimer l'image du serveur si elle existe
    if ($art['urlPhotArt']) {
        deleteImage($art['urlPhotArt']);
    }
    
    // Supprimer l'article de la BDD
    $result = delete('ARTICLE', 'numArt', $numArt);
    
    if ($result) {
        $_SESSION['success'] = "Article supprimé";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur : " . $e->getMessage();
}

// ⚠️ À ce stade, on ne vérifie pas encore les CIR
// (commentaires, likes, mots-clés)
// Ces vérifications seront ajoutées plus tard

try {
    $result = delete('ARTICLE', 'numArt', $numArt);
    if ($result) {
        $_SESSION['success'] = "Article supprimé";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur : " . $e->getMessage();
}

header('Location: ../../views/backend/articles/list.php');
exit;
?>