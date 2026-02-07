<?php
/**
 * ============================================================
 * API/ARTICLES/DELETE.PHP - Endpoint API de suppression d'article
 * ============================================================
 * 
 * ROLE : Traite la suppression physique (Hard Delete) d'un article
 *        via un formulaire POST (ancienne version).
 * 
 * ATTENTION - CODE MORT :
 * Comme les autres fichiers api/articles/, ce fichier contient
 * du code inaccessible apres les instructions "exit;".
 * Il y a 3 blocs try/catch identiques dont seul le premier s'execute.
 * Les 2 derniers blocs ajoutent progressivement la gestion des CIR
 * (commentaires, likes, mots-cles) mais ne sont jamais atteints.
 * 
 * DIFFERENCE AVEC views/backend/articles/delete.php :
 * - views/delete.php fait un SOFT DELETE (delLogiq = 1)
 * - api/delete.php fait un HARD DELETE (DELETE FROM)
 * - views/permanent-delete.php fait aussi un HARD DELETE avec CIR
 * 
 * FONCTIONNEMENT (partie active) :
 * 1. Verifie le token CSRF
 * 2. Verifie la methode POST et l'ID
 * 3. Charge l'article pour recuperer l'image
 * 4. Supprime l'image du serveur si elle existe
 * 5. Supprime l'article de la BDD (SANS gerer les CIR -> bug potentiel)
 * 6. Redirige vers list.php
 * 
 * PROBLEME CONNU :
 * Le premier try/catch ne supprime PAS les donnees associees
 * (likes, commentaires, mots-cles) avant de supprimer l'article.
 * Cela causera une erreur MySQL si des FK existent
 * (ON DELETE RESTRICT dans les CIR).
 * 
 * SECURITE :
 * - Token CSRF verifie
 * - Verification de l'existence de l'article avant suppression
 * ============================================================
 */

/* --- Demarrage session + verification CSRF --- */
session_start();
require_once '../../functions/csrf.php';

$token = $_POST['csrf_token'] ?? '';
if (!verifyCSRFToken($token)) {
    die('Token CSRF invalide');
}

/* --- Inclusion de la fonction de suppression generique --- */
require_once '../../functions/query/delete.php';

/* --- Verification methode POST --- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/backend/articles/list.php');
    exit;
}

/* --- Recuperation et validation de l'ID --- */
$numArt = $_POST['numArt'] ?? null;

if (!$numArt) {
    $_SESSION['error'] = "ID manquant";
    header('Location: ../../views/backend/articles/list.php');
    exit;
}

/* --- Inclusion des fonctions d'upload et de selection --- */
require_once '../../functions/upload.php';
require_once '../../functions/query/select.php';

/* 
 * --- Charger l'article pour recuperer le nom de l'image ---
 * On doit charger l'article AVANT de le supprimer pour pouvoir
 * supprimer le fichier image du serveur
 */
$art = selectOne('ARTICLE', 'numArt', $numArt);

/* --- Verification que l'article existe --- */
if (!$art) {
    $_SESSION['error'] = "Article introuvable";
    header('Location: ../../views/backend/articles/list.php');
    exit;
}

/* 
 * --- Suppression de l'article (1er bloc - ACTIF) ---
 * PROBLEME : ne supprime pas les CIR (likes, commentaires, mots-cles)
 * avant de supprimer l'article -> erreur FK probable
 */
try {
    /* Supprimer l'image du serveur si elle existe */
    if ($art['urlPhotArt']) {
        deleteImage($art['urlPhotArt']);
    }
    
    /* Supprimer l'article de la BDD */
    $result = delete('ARTICLE', 'numArt', $numArt);
    
    if ($result) {
        $_SESSION['success'] = "Article supprime";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur : " . $e->getMessage();
}

/* 
 * NOTE : Le commentaire ci-dessous indique que les CIR ne sont pas gerees
 * Ce code est le seul qui s'execute reellement
 */

/* --- 2eme tentative de suppression (DOUBLON - ne devrait pas etre la) --- */
try {
    $result = delete('ARTICLE', 'numArt', $numArt);
    if ($result) {
        $_SESSION['success'] = "Article supprime";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur : " . $e->getMessage();
}

header('Location: ../../views/backend/articles/list.php');
exit;

/* 
 * ============================================================
 * CODE MORT CI-DESSOUS (UNREACHABLE CODE)
 * ============================================================
 * Les 2 blocs try/catch suivants ne s'executent JAMAIS.
 * Ils ajoutent progressivement la gestion des CIR :
 * - Bloc 2 : supprime mots-cles + article
 * - Bloc 3 : supprime mots-cles + commentaires + likes + article
 * Le bloc 3 est la version complete qui aurait du remplacer le bloc 1.
 * ============================================================
 */

/* --- Bloc avec gestion mots-cles (CODE MORT) --- */
try {
    $pdo = getConnection();
    
    if ($art['urlPhotArt']) {
        deleteImage($art['urlPhotArt']);
    }
    
    /* Supprimer les associations mots-cles (respect des CIR) */
    $stmtMotCle = $pdo->prepare(
        "DELETE FROM MOTCLEARTICLE WHERE numArt = ?"
    );
    $stmtMotCle->execute([$numArt]);
    
    $result = delete('ARTICLE', 'numArt', $numArt);
    
    if ($result) {
        $_SESSION['success'] = "Article et ses associations supprimes";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur : " . $e->getMessage();
}

header('Location: ../../views/backend/articles/list.php');
exit;
?>
<?php
/* --- Bloc complet avec tous les CIR (CODE MORT) --- */
try {
    $pdo = getConnection();
    
    /* 1. Supprimer l'image */
    if ($art['urlPhotArt']) {
        deleteImage($art['urlPhotArt']);
    }
    
    /* 2. Supprimer les associations mots-cles (FK -> ARTICLE) */
    $stmtMotCle = $pdo->prepare("DELETE FROM MOTCLEARTICLE WHERE numArt = ?");
    $stmtMotCle->execute([$numArt]);
    
    /* 3. Supprimer les commentaires (FK -> ARTICLE) */
    $stmtComments = $pdo->prepare("DELETE FROM COMMENT WHERE numArt = ?");
    $stmtComments->execute([$numArt]);
    
    /* 4. Supprimer les likes (FK -> ARTICLE) */
    $stmtLikes = $pdo->prepare("DELETE FROM LIKEART WHERE numArt = ?");
    $stmtLikes->execute([$numArt]);
    
    /* 5. Supprimer l'article (maintenant possible car plus de FK) */
    $result = delete('ARTICLE', 'numArt', $numArt);
    
    if ($result) {
        $_SESSION['success'] = "Article, commentaires, likes et associations supprimes";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur : " . $e->getMessage();
}

header('Location: ../../views/backend/articles/list.php');
exit;
?>
