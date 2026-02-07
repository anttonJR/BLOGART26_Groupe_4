<?php
session_start();
require_once '../../functions/csrf.php';

$token = $_POST['csrf_token'] ?? '';
if (!verifyCSRFToken($token)) {
    die('Token CSRF invalide');
}

require_once '../../functions/query/update.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/backend/articles/list.php');
    exit;
}

$numThem = $_POST['numThem'] ?? null;

if (!$numThem) {
    $errors[] = "La thematique est obligatoire";
}

$data = [
    'numThem' => $numThem
];

require_once '../../functions/upload.php';
require_once '../../functions/query/select.php';

$artActuel = selectOne('ARTICLE', 'numArt', $numArt);

$urlPhotArt = $artActuel['urlPhotArt'];

if (isset($_FILES['imageArt']) && $_FILES['imageArt']['error'] !== UPLOAD_ERR_NO_FILE) {
    $uploadResult = uploadImage($_FILES['imageArt']);
    
    if ($uploadResult['success']) {
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

$data = [
    'urlPhotArt' => $urlPhotArt
];

$numArt = $_POST['numArt'] ?? null;
$libTltArt = trim($_POST['libTltArt'] ?? '');
$libChapArt = trim($_POST['libChapArt'] ?? '');
$parag1Art = trim($_POST['parag1Art'] ?? '');
$parag2Art = trim($_POST['parag2Art'] ?? '');
$parag3Art = trim($_POST['parag3Art'] ?? '');
$libSsTitl1Art = trim($_POST['libSsTitl1Art'] ?? '');
$libAccrochArt = trim($_POST['libAccrochArt'] ?? '');
$libConcArt = trim($_POST['libConcArt'] ?? '');

$errors = [];

if (!$numArt) {
    $errors[] = "ID manquant";
}

if (empty($libTltArt)) {
    $errors[] = "Le titre est obligatoire";
}

if (empty($libChapArt)) {
    $errors[] = "Le chapo est obligatoire";
}

if (empty($parag1Art)) {
    $errors[] = "Le paragraphe 1 est obligatoire";
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: ../../views/backend/articles/edit.php?id=' . $numArt);
    exit;
}

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
        $_SESSION['success'] = "Article mis a jour";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur : " . $e->getMessage();
}

header('Location: ../../views/backend/articles/list.php');
exit;

require_once '../../functions/motcle.php';

$motscles = $_POST['motscles'] ?? [];

if (count($motscles) < 3) {
    $errors[] = "Vous devez selectionner au moins 3 mots-cles";
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: ../../views/backend/articles/edit.php?id=' . $numArt);
    exit;
}

try {
    $result = update('ARTICLE', $data, 'numArt', $numArt);
    
    if ($result) {
        $pdo = getConnection();
        
        $stmtDelete = $pdo->prepare(
            "DELETE FROM MOTCLEARTICLE WHERE numArt = ?"
        );
        $stmtDelete->execute([$numArt]);
        
        $stmtInsert = $pdo->prepare(
            "INSERT INTO MOTCLEARTICLE (numArt, numMotCle) VALUES (?, ?)"
        );
        
        foreach ($motscles as $numMotCle) {
            $stmtInsert->execute([$numArt, $numMotCle]);
        }
        
        $_SESSION['success'] = "Article mis a jour avec " . count($motscles) . " mots-cles";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur : " . $e->getMessage();
}

header('Location: ../../views/backend/articles/list.php');
exit;
?><?php
/**
 * ============================================================
 * API/ARTICLES/UPDATE.PHP - Endpoint API de mise a jour d'article
 * ============================================================
 * 
 * ROLE : Traite la soumission du formulaire de modification d'article
 *        provenant d'une ancienne version du formulaire.
 * 
 * ATTENTION - CODE MORT :
 * Comme api/articles/create.php, ce fichier contient du code inaccessible
 * apres les instructions "exit;". La gestion de l'upload d'image et des
 * mots-cles se trouve apres un exit et ne s'executera jamais.
 * 
 * DIFFERENCE AVEC views/backend/articles/edit.php :
 * - Ce fichier utilise les fonctions query/ (update.php) -> approche generique
 * - Le fichier views/ utilise directement PDO -> approche directe
 * - Le fichier views/ est celui qui fonctionne actuellement
 * 
 * FONCTIONNEMENT (partie active) :
 * 1. Verifie le token CSRF
 * 2. Verifie que la methode est POST
 * 3. Recupere et valide les donnees
 * 4. Met a jour la table ARTICLE via la fonction update()
 * 5. Redirige vers list.php
 * 
 * SECURITE :
 * - Token CSRF verifie
 * - Validation des champs obligatoires
 * - Messages d'erreur en session
 * ============================================================
 */

/* --- Demarrage session + verification CSRF --- */
session_start();
require_once '../../functions/csrf.php';

$token = $_POST['csrf_token'] ?? '';
if (!verifyCSRFToken($token)) {
    die('Token CSRF invalide');
}

/* --- Inclusion de la fonction de mise a jour generique --- */
require_once '../../functions/query/update.php';

/* --- Verification methode POST --- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/backend/articles/list.php');
    exit;
}

/* --- Recuperation de la thematique --- */
$numThem = $_POST['numThem'] ?? null;

if (!$numThem) {
    $errors[] = "La thematique est obligatoire";
}

/* --- Preparation initiale des donnees --- */
$data = [
    'numThem' => $numThem
];

/* 
 * --- Gestion de l'upload d'image (AVANT le code principal) ---
 * Inclut upload.php et select.php pour gerer le remplacement d'image
 */
require_once '../../functions/upload.php';
require_once '../../functions/query/select.php';

/* 
 * --- Charger l'article actuel pour recuperer l'ancienne image ---
 * selectOne() retourne une seule ligne de la table ARTICLE
 * On en a besoin pour savoir si l'article a deja une image a remplacer
 */
$artActuel = selectOne('ARTICLE', 'numArt', $numArt);

/* --- Gestion du remplacement d'image --- */
$urlPhotArt = $artActuel['urlPhotArt']; // Conserver l'ancienne image par defaut

if (isset($_FILES['imageArt']) && $_FILES['imageArt']['error'] !== UPLOAD_ERR_NO_FILE) {
    $uploadResult = uploadImage($_FILES['imageArt']);
    
    if ($uploadResult['success']) {
        /* 
         * Si une nouvelle image est uploadee avec succes,
         * on supprime l'ancienne du serveur (si elle existe)
         * pour eviter d'accumuler des fichiers inutiles
         */
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

/* --- Mise a jour du tableau $data avec l'image --- */
$data = [
    'urlPhotArt' => $urlPhotArt
];

/* 
 * --- Recuperation des donnees du formulaire ---
 * Memes champs que dans create.php mais pour la modification
 */
$numArt = $_POST['numArt'] ?? null;
$libTltArt = trim($_POST['libTltArt'] ?? '');
$libChapArt = trim($_POST['libChapArt'] ?? '');
$parag1Art = trim($_POST['parag1Art'] ?? '');
$parag2Art = trim($_POST['parag2Art'] ?? '');
$parag3Art = trim($_POST['parag3Art'] ?? '');
$libSsTitl1Art = trim($_POST['libSsTitl1Art'] ?? '');
$libAccrochArt = trim($_POST['libAccrochArt'] ?? '');
$libConcArt = trim($_POST['libConcArt'] ?? '');

/* --- Validation des champs obligatoires --- */
$errors = [];

if (!$numArt) {
    $errors[] = "ID manquant";
}

if (empty($libTltArt)) {
    $errors[] = "Le titre est obligatoire";
}

if (empty($libChapArt)) {
    $errors[] = "Le chapo est obligatoire";
}

if (empty($parag1Art)) {
    $errors[] = "Le paragraphe 1 est obligatoire";
}

/* --- Redirection en cas d'erreur avec conservation des donnees --- */
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: ../../views/backend/articles/edit.php?id=' . $numArt);
    exit;
}

/* 
 * --- Preparation des donnees pour la mise a jour ---
 * Difference avec create.php : pas de numArt ni dtCreaArt
 * On ajoute dtMajArt (date de mise a jour) avec la date actuelle
 */
$data = [
    'dtMajArt' => date('Y-m-d H:i:s'),    // Date de modification
    'libTltArt' => $libTltArt,
    'libChapArt' => $libChapArt,
    'parag1Art' => $parag1Art,
    'parag2Art' => $parag2Art ?: null,
    'parag3Art' => $parag3Art ?: null,
    'libSsTitl1Art' => $libSsTitl1Art ?: null,
    'libAccrochArt' => $libAccrochArt ?: null,
    'libConcArt' => $libConcArt ?: null
];

/* 
 * --- Mise a jour dans la BDD ---
 * update('ARTICLE', $data, 'numArt', $numArt) :
 *   - Table : ARTICLE
 *   - Donnees : $data (les colonnes a modifier)
 *   - Condition : WHERE numArt = $numArt
 */
try {
    $result = update('ARTICLE', $data, 'numArt', $numArt);
    if ($result) {
        $_SESSION['success'] = "Article mis a jour";
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
 * Le code apres exit; ne s'execute JAMAIS.
 * Il contient la gestion des mots-cles qui aurait du etre
 * integree AVANT le exit ci-dessus.
 * ============================================================
 */

require_once '../../functions/motcle.php';

// Recuperation des mots-cles selectionnes
$motscles = $_POST['motscles'] ?? [];

// Validation : minimum 3 mots-cles requis
if (count($motscles) < 3) {
    $errors[] = "Vous devez selectionner au moins 3 mots-cles";
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: ../../views/backend/articles/edit.php?id=' . $numArt);
    exit;
}

// Mise a jour de l'article avec gestion des mots-cles
try {
    $result = update('ARTICLE', $data, 'numArt', $numArt);
    
    if ($result) {
        $pdo = getConnection();
        
        /* 
         * Strategie Delete & Re-insert pour les mots-cles :
         * 1. Supprimer TOUTES les anciennes associations
         * 2. Inserer les nouvelles
         */
        $stmtDelete = $pdo->prepare(
            "DELETE FROM MOTCLEARTICLE WHERE numArt = ?"
        );
        $stmtDelete->execute([$numArt]);
        
        $stmtInsert = $pdo->prepare(
            "INSERT INTO MOTCLEARTICLE (numArt, numMotCle) VALUES (?, ?)"
        );
        
        foreach ($motscles as $numMotCle) {
            $stmtInsert->execute([$numArt, $numMotCle]);
        }
        
        $_SESSION['success'] = "Article mis a jour avec " . count($motscles) . " mots-cles";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur : " . $e->getMessage();
}

header('Location: ../../views/backend/articles/list.php');
exit;
?>
