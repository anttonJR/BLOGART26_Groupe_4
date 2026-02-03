<?php
session_start();
require_once '../../functions/query/select.php';

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

// Recherche du membre en BDD
$sql = "SELECT m.*, s.libStat 
        FROM MEMBRE m 
        INNER JOIN STATUT s ON m.numStat = s.numStat 
        WHERE m.pseudoMemb = ?";
$pdo = getConnection();
$stmt = $pdo->prepare($sql);
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
if ($membre['numStat'] == 3) {
    // Administrateur → Panneau d'administration
    header('Location: ../../views/backend/dashboard.php');
} elseif ($membre['numStat'] == 2) {
    // Modérateur → Panneau de modération
    header('Location: ../../views/backend/moderation.php');
} else {
    // Membre → Page d'accueil
    header('Location: ../../views/frontend/index.php');
}
exit;
?>