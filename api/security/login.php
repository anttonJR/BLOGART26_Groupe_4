<?php
session_start();
require_once '../../config.php';
require_once '../../functions/query/select.php';

global $DB;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/frontend/security/login.php');
    exit;
}

// Récupération des données
$pseudoMemb = trim($_POST['pseudoMemb'] ?? '');
$passMemb = $_POST['passMemb'] ?? '';

// Validation basique
if (empty($pseudoMemb) || empty($passMemb)) {
    $_SESSION['error'] = "Pseudo et mot de passe requis";
    header('Location: ../../views/frontend/security/login.php');
    exit;
}

// Vérification du reCAPTCHA v3
$recaptcha_token = $_POST['g-recaptcha-response'] ?? '';
if (empty($recaptcha_token)) {
    $_SESSION['error'] = "Vérification de sécurité échouée. Veuillez réessayer.";
    header('Location: ../../views/frontend/security/login.php');
    exit;
}

$recaptcha_secret = getenv('RECAPTCHA_SECRET_KEY');
$recaptcha_threshold = floatval(getenv('RECAPTCHA_SCORE_THRESHOLD') ?: 0.5);

$response = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-type: application/x-www-form-urlencoded' . "\r\n",
        'content' => http_build_query(['secret' => $recaptcha_secret, 'response' => $recaptcha_token])
    ]
]));

if ($response === false) {
    $_SESSION['error'] = "Erreur de vérification de sécurité";
    header('Location: ../../views/frontend/security/login.php');
    exit;
}

$recaptcha_result = json_decode($response);
if (!$recaptcha_result->success || $recaptcha_result->score < $recaptcha_threshold) {
    // Logs de débogage
    if (getenv('APP_DEBUG') == 'true') {
        error_log("reCAPTCHA v3 - Login: success=" . ($recaptcha_result->success ? 'true' : 'false') . ", score=" . ($recaptcha_result->score ?? 'N/A') . ", threshold=" . $recaptcha_threshold);
    }
    $_SESSION['error'] = "Vérification de sécurité échouée. Vous semblez être un bot.";
    header('Location: ../../views/frontend/security/login.php');
    exit;
}

// Logs de débogage - Succès
if (getenv('APP_DEBUG') == 'true') {
    error_log("reCAPTCHA v3 - Login réussi: score=" . $recaptcha_result->score);
}

// Recherche du membre en BDD
$sql = "SELECT m.*, s.libStat 
        FROM MEMBRE m 
        INNER JOIN STATUT s ON m.numStat = s.numStat 
        WHERE m.pseudoMemb = ?";
$stmt = $DB->prepare($sql);
$stmt->execute([$pseudoMemb]);
$membre = $stmt->fetch();

// Vérification de l'existence du membre
if (!$membre) {
    $_SESSION['error'] = "Pseudo ou mot de passe incorrect";
    header('Location: ../../views/frontend/security/login.php');
    exit;
}

// Vérification du mot de passe
if (!password_verify($passMemb, $membre['passMemb'])) {
    $_SESSION['error'] = "Pseudo ou mot de passe incorrect";
    header('Location: ../../views/frontend/security/login.php');
    exit;
}

// === CONNEXION RÉUSSIE ===
// Stockage des informations en session
$_SESSION['user'] = [
    'numMemb' => $membre['numMemb'],
    'pseudoMemb' => $membre['pseudoMemb'],
    'prenomMemb' => $membre['prenomMemb'],
    'nomMemb' => $membre['nomMemb'],
    'numStat' => $membre['numStat'],
    'libStat' => $membre['libStat']
];

$_SESSION['logged_in'] = true;

// Redirection selon le statut
if ($membre['numStat'] == 1) {
    // Administrateur → Panneau d'administration
    header('Location: ../../views/backend/dashboard.php');
} elseif ($membre['numStat'] == 2) {
    // Modérateur → Panneau de modération
    header('Location: ../../views/backend/moderation/comments.php');
} else {
    // Membre → Page d'accueil
    header('Location: ../../index.php');
}
exit;
?>