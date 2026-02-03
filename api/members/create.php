<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DEBUG: Afficher le début
echo "=== DEBUG START ===<br>";
echo "Method: " . $_SERVER['REQUEST_METHOD'] . "<br>";

require_once '../../config.php';
echo "Config OK<br>";

require_once '../../functions/csrf.php';
echo "CSRF OK<br>";

global $DB;
if (!$DB) {
    echo "ERREUR: Pas de DB<br>";
    exit;
}
echo "DB OK<br>";

$token = $_POST['csrf_token'] ?? '';
echo "Token reçu: " . ($token ? substr($token, 0, 20) . "..." : "VIDE") . "<br>";
echo "Token session: " . (isset($_SESSION['csrf_token']) ? substr($_SESSION['csrf_token'], 0, 20) . "..." : "NON DÉFINI") . "<br>";

if (!verifyCSRFToken($token)) {
    echo "ERREUR: Token CSRF invalide<br>";
    echo "<a href='../../views/frontend/security/signup.php'>Retour</a>";
    exit;
}
echo "CSRF validé<br>";

require_once '../../functions/query/insert.php';
echo "Insert OK<br>";
require_once '../../functions/query/select.php';
echo "Select OK<br>";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['errors'] = ["Méthode non autorisée"];
    header('Location: ../../views/frontend/security/signup.php');
    exit;
}
echo "Méthode POST OK<br>";

// === 1. RÉCUPÉRATION DES DONNÉES ===
$pseudoMemb = trim($_POST['pseudoMemb'] ?? '');
$prenomMemb = trim($_POST['prenomMemb'] ?? '');
$nomMemb = trim($_POST['nomMemb'] ?? '');
$eMailMemb = trim($_POST['eMailMemb'] ?? '');
$eMailMemb_confirm = trim($_POST['eMailMemb_confirm'] ?? '');
$passMemb = $_POST['passMemb'] ?? '';
$passMemb_confirm = $_POST['passMemb_confirm'] ?? '';

echo "Données reçues:<br>";
echo "- Pseudo: $pseudoMemb<br>";
echo "- Prénom: $prenomMemb<br>";
echo "- Nom: $nomMemb<br>";
echo "- Email: $eMailMemb<br>";
$accordMemb = $_POST['accordMemb'] ?? 0;

$errors = [];

// === 2. VALIDATION DU PSEUDO ===
if (empty($pseudoMemb)) {
    $errors[] = "Le pseudo est obligatoire";
} elseif (strlen($pseudoMemb) < 6) {
    $errors[] = "Le pseudo doit contenir au moins 6 caractères";
} elseif (strlen($pseudoMemb) > 70) {
    $errors[] = "Le pseudo ne peut pas dépasser 70 caractères";
} else {
    // Vérifier l'unicité du pseudo
    global $DB;
    $sql = "SELECT COUNT(*) as count FROM MEMBRE WHERE pseudoMemb = ?";
    $stmt = $DB->prepare($sql);
    $stmt->execute([$pseudoMemb]);
    $result = $stmt->fetch();
    
    if ($result['count'] > 0) {
        $errors[] = "Ce pseudo est déjà utilisé";
    }
}

// === 3. VALIDATION PRÉNOM ET NOM ===
if (empty($prenomMemb)) {
    $errors[] = "Le prénom est obligatoire";
}

if (empty($nomMemb)) {
    $errors[] = "Le nom est obligatoire";
}

// === 4. VALIDATION EMAIL ===
if (empty($eMailMemb)) {
    $errors[] = "L'email est obligatoire";
} elseif (!filter_var($eMailMemb, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "L'email n'est pas valide";
} elseif ($eMailMemb !== $eMailMemb_confirm) {
    $errors[] = "Les deux emails ne correspondent pas";
} else {
    // Vérifier l'unicité de l'email
    global $DB;
    $sql = "SELECT COUNT(*) as count FROM MEMBRE WHERE eMailMemb = ?";
    $stmt = $DB->prepare($sql);
    $stmt->execute([$eMailMemb]);
    $result = $stmt->fetch();
    
    if ($result['count'] > 0) {
        $errors[] = "Cet email est déjà utilisé";
    }
}

// === 5. VALIDATION PASSWORD ===
$passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,15}$/';

if (empty($passMemb)) {
    $errors[] = "Le mot de passe est obligatoire";
} elseif (!preg_match($passwordRegex, $passMemb)) {
    $errors[] = "Le mot de passe doit contenir entre 8 et 15 caractères, dont au moins 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial";
} elseif ($passMemb !== $passMemb_confirm) {
    $errors[] = "Les deux mots de passe ne correspondent pas";
}

// === 6. VALIDATION RGPD ===
if ($accordMemb != 1) {
    $errors[] = "Vous devez accepter le stockage de vos données pour vous inscrire";
}

echo "Validation RGPD OK<br>";
echo "Erreurs jusqu'ici: " . count($errors) . "<br>";

// === 7. VALIDATION reCAPTCHA ===
echo "Début validation reCAPTCHA...<br>";
echo "g-recaptcha-response présent: " . (isset($_POST['g-recaptcha-response']) ? "OUI" : "NON") . "<br>";

if (isset($_POST['g-recaptcha-response'])) {
    $token = $_POST['g-recaptcha-response'];
    
    if (empty($token)) {
        $errors[] = "Token reCAPTCHA vide";
    } else {
        echo "Token reCAPTCHA reçu: " . substr($token, 0, 30) . "...<br>";
        $secretKey = $_ENV['RECAPTCHA_SECRET_KEY'] ?? getenv('RECAPTCHA_SECRET_KEY');
        
        if (empty($secretKey)) {
            $errors[] = "Clé secrète reCAPTCHA non configurée";
        } else {
            echo "Appel API Google reCAPTCHA...<br>";
            flush();
            
            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $data = [
                'secret' => $secretKey,
                'response' => $token
            ];
            
            $options = [
                'http' => [
                    'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($data),
                    'timeout' => 10
                ]
            ];
            
            $context = stream_context_create($options);
            $result = @file_get_contents($url, false, $context);
            
            echo "Réponse Google: " . ($result ? "reçue" : "ERREUR") . "<br>";
            
            if ($result === false) {
                $errors[] = "Impossible de contacter le serveur reCAPTCHA";
            } else {
                $response = json_decode($result);
                echo "Success: " . ($response->success ? "true" : "false") . "<br>";
                
                if (!$response || empty($response->success)) {
                    if (isset($response->{'error-codes'}) && in_array('timeout-or-duplicate', $response->{'error-codes'}, true)) {
                        $errors[] = "Token reCAPTCHA expiré. Rechargez la page et réessayez.";
                    } else {
                        $errors[] = "Validation reCAPTCHA échouée. Réessayez.";
                    }
                } elseif (isset($response->score) && $response->score < 0.5) {
                    $errors[] = "Score reCAPTCHA trop bas (" . $response->score . "). Réessayez.";
                }
            }
        }
    }
} else {
    $errors[] = "Validation reCAPTCHA manquante";
}

echo "Validation reCAPTCHA terminée<br>";
echo "Total erreurs: " . count($errors) . "<br>";

// === 8. SI ERREURS, RETOUR AU FORMULAIRE ===
if (!empty($errors)) {
    echo "<h3>Erreurs détectées:</h3><ul>";
    foreach ($errors as $error) {
        echo "<li>" . htmlspecialchars($error) . "</li>";
    }
    echo "</ul>";
    echo "<p><a href='../../views/frontend/security/signup.php'>Retour au formulaire</a></p>";
    
    $_SESSION['errors'] = $errors;
    $_SESSION['old_data'] = $_POST;
    exit;
}

echo "=== INSERTION EN BASE ===<br>";

// === 9. CRYPTAGE DU MOT DE PASSE ===
$passMemb_hashed = password_hash($passMemb, PASSWORD_DEFAULT);
echo "Mot de passe hashé OK<br>";

// === 10. GÉNÉRATION DU NUMÉRO DE MEMBRE ===
global $DB;
$sql = "SELECT MAX(numMemb) as max FROM MEMBRE";
$stmt = $DB->query($sql);
$result = $stmt->fetch();
$numMemb = ($result['max'] ?? 0) + 1;
echo "Numéro membre: $numMemb<br>";

// === 11. INSERTION EN BASE ===
$data = [
    'numMemb' => $numMemb,
    'prenomMemb' => $prenomMemb,
    'nomMemb' => $nomMemb,
    'pseudoMemb' => $pseudoMemb,
    'eMailMemb' => $eMailMemb,
    'passMemb' => $passMemb_hashed,
    'dtCreaMemb' => date('Y-m-d H:i:s'),
    'dtMajMemb' => null,
    'accordMemb' => 1,
    'numStat' => 1
];

echo "Données à insérer préparées<br>";

try {
    $columns = implode(', ', array_keys($data));
    $placeholders = ':' . implode(', :', array_keys($data));
    
    $sql = "INSERT INTO MEMBRE ($columns) VALUES ($placeholders)";
    echo "SQL: $sql<br>";
    
    $stmt = $DB->prepare($sql);
    
    foreach ($data as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    
    echo "Exécution INSERT...<br>";
    $result = $stmt->execute();
    
    if ($result) {
        echo "<h3 style='color:green'>INSCRIPTION RÉUSSIE !</h3>";
        echo "<a href='../../views/frontend/security/login.php'>Se connecter</a>";
        $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        exit;
    } else {
        echo "ERREUR: execute() a retourné false<br>";
        echo "Erreur PDO: ";
        print_r($stmt->errorInfo());
        echo "<br>";
    }
} catch (Exception $e) {
    echo "<h3 style='color:red'>ERREUR: " . $e->getMessage() . "</h3>";
    $_SESSION['errors'] = ["Erreur lors de l'inscription : " . $e->getMessage()];
    header('Location: ../../views/frontend/security/signup.php');
    exit;
}
?>