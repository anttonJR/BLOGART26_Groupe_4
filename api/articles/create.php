<?php
/**
 * ============================================================
 * API/ARTICLES/CREATE.PHP - Endpoint API de creation d'article
 * ============================================================
 * 
 * ROLE : Traite la soumission du formulaire de creation d'article
 *        provenant d'une ancienne version du formulaire (champs detailles).
 * 
 * ATTENTION - CODE MORT :
 * Ce fichier contient du code inaccessible (unreachable code) apres
 * les instructions "exit;" (lignes ~97 et ~105). Le code apres ces exit
 * ne s'executera JAMAIS. Les fonctionnalites d'upload image et de mots-cles
 * ont ete ajoutees APRES les exit au lieu d'etre integrees AVANT.
 * 
 * DIFFERENCE AVEC views/backend/articles/create.php :
 * - Ce fichier utilise les fonctions query/ (insert.php) -> approche generique
 * - Le fichier views/ utilise directement PDO -> approche directe
 * - Le fichier views/ est celui qui fonctionne actuellement
 * 
 * FONCTIONNEMENT (partie active uniquement) :
 * 1. Verifie le token CSRF (protection contre les attaques CSRF)
 * 2. Verifie que la methode est POST
 * 3. Recupere et valide les donnees du formulaire
 * 4. Insere dans la table ARTICLE via la fonction insert()
 * 5. Redirige vers list.php avec message succes/erreur
 * 
 * SECURITE :
 * - Token CSRF verifie avant tout traitement
 * - Validation des champs obligatoires
 * - Messages d'erreur stockes en session (pas affiches directement)
 * ============================================================
 */

/* --- Demarrage de la session PHP --- */
/* Necessaire pour stocker les messages d'erreur/succes et le token CSRF */
session_start();

/* 
 * --- Verification du token CSRF ---
 * CSRF = Cross-Site Request Forgery
 * Le token est genere dans le formulaire (champ cache) et verifie ici.
 * Si le token est invalide (absent, expire, ou different), on bloque la requete.
 * Cela empeche un site malveillant de soumettre le formulaire a la place de l'utilisateur.
 */
require_once '../../functions/csrf.php';
$token = $_POST['csrf_token'] ?? '';
if (!verifyCSRFToken($token)) {
    die('Token CSRF invalide');
}

/* --- Inclusion de la fonction d'insertion generique --- */
require_once '../../functions/query/insert.php';

/* 
 * --- Verification de la methode HTTP ---
 * Ce fichier ne doit traiter que les requetes POST (soumission de formulaire).
 * Si quelqu'un accede directement via l'URL (GET), on le redirige vers la liste.
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/backend/articles/list.php');
    exit;
}

/* --- Recuperation de la thematique (FK vers THEMATIQUE) --- */
$numThem = $_POST['numThem'] ?? null;

/* --- Validation de la thematique --- */
if (!$numThem) {
    $errors[] = "La thematique est obligatoire";
}

/* --- Preparation du tableau de donnees (sera ecrase plus bas) --- */
$data = [
    'numThem' => $numThem
];

/* 
 * --- Recuperation de TOUS les champs du formulaire ---
 * trim() : supprime les espaces en debut/fin de chaine
 * ?? '' : si le champ POST n'existe pas, valeur par defaut vide
 * ?? null : si le champ n'existe pas, valeur NULL (pour les champs optionnels)
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

/* 
 * --- Validation des champs obligatoires ---
 * On accumule les erreurs dans un tableau $errors
 * pour les afficher toutes en meme temps (meilleure UX)
 */
$errors = [];

if (!$numArt) {
    $errors[] = "Le numero d'article est obligatoire";
}

if (empty($libTltArt)) {
    $errors[] = "Le titre est obligatoire";
}

if (strlen($libTltArt) > 100) {
    $errors[] = "Le titre ne peut pas depasser 100 caracteres";
}

if (empty($libChapArt)) {
    $errors[] = "Le chapo est obligatoire";
}

if (empty($parag1Art)) {
    $errors[] = "Le paragraphe 1 est obligatoire";
}

/* 
 * --- Gestion des erreurs de validation ---
 * Si au moins une erreur existe, on redirige vers le formulaire
 * avec les erreurs ET les anciennes donnees en session
 * (pour ne pas obliger l'utilisateur a tout retaper)
 */
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old_data'] = $_POST;
    header('Location: ../../views/backend/articles/create.php');
    exit;
}

/* 
 * --- Construction du tableau $data pour l'insertion ---
 * Ce tableau correspond exactement aux colonnes de la table ARTICLE
 * Les champs optionnels utilisent ?: null -> si la valeur est vide (''),
 * on stocke NULL au lieu d'une chaine vide (meilleure pratique en BDD)
 */
$data = [
    'numArt' => $numArt,
    'dtCreaArt' => date('Y-m-d H:i:s'),  // Date de creation au format MySQL
    'dtMajArt' => null,                    // Pas de date de modification a la creation
    'libTltArt' => $libTltArt,
    'libChapArt' => $libChapArt,
    'parag1Art' => $parag1Art,
    'parag2Art' => $parag2Art ?: null,
    'parag3Art' => $parag3Art ?: null,
    'libSsTitl1Art' => $libSsTitl1Art ?: null,
    'libAccrochArt' => $libAccrochArt ?: null,
    'libConcArt' => $libConcArt ?: null,
    'urlPhotArt' => null,                  // Pas d'image dans cette version
    'numThem' => null                      // NOTE : ecrase la valeur recuperee plus haut
];

/* 
 * --- Insertion dans la BDD ---
 * try/catch : gere les erreurs PDO (contrainte FK, doublon, etc.)
 * La fonction insert() est definie dans functions/query/insert.php
 */
try {
    $result = insert('ARTICLE', $data);
    if ($result) {
        $_SESSION['success'] = "Article cree avec succes";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur : " . $e->getMessage();
}

/* --- Redirection vers la liste (succes ou erreur) --- */
header('Location: ../../views/backend/articles/list.php');
exit;

/* 
 * ============================================================
 * CODE MORT CI-DESSOUS (UNREACHABLE CODE)
 * ============================================================
 * Tout le code apres exit; ne s'execute JAMAIS.
 * Il contient la gestion de l'upload d'image et des mots-cles
 * qui aurait du etre integree AVANT le exit ci-dessus.
 * Ce code est conserve comme reference mais n'est pas fonctionnel.
 * ============================================================
 */

require_once '../../functions/upload.php';

// Gerer l'upload de l'image
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
    'urlPhotArt' => $urlPhotArt
];

// Recuperation des mots-cles
$motscles = $_POST['motscles'] ?? [];

// Validation des mots-cles
if (count($motscles) < 3) {
    $errors[] = "Vous devez selectionner au moins 3 mots-cles";
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: ../../views/backend/articles/create.php');
    exit;
}

// Insertion de l'article avec mots-cles
try {
    $result = insert('ARTICLE', $data);
    
    if ($result) {
        // Insertion des associations article-motcle dans la table de jointure
        $pdo = getConnection();
        $stmt = $pdo->prepare(
            "INSERT INTO MOTCLEARTICLE (numArt, numMotCle) VALUES (?, ?)"
        );
        
        foreach ($motscles as $numMotCle) {
            $stmt->execute([$numArt, $numMotCle]);
        }
        
        $_SESSION['success'] = "Article cree avec " . count($motscles) . " mots-cles";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Erreur : " . $e->getMessage();
}

header('Location: ../../views/backend/articles/list.php');
exit;

?>
