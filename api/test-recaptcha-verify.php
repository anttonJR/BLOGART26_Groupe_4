<?php
session_start();
require_once '../../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../test-recaptcha.php');
    exit;
}

// Récupérer le token
$recaptcha_token = $_POST['g-recaptcha-response'] ?? '';

if (empty($recaptcha_token)) {
    $_SESSION['recaptcha_result'] = [
        'success' => false,
        'error' => 'Token vide'
    ];
    header('Location: ../../test-recaptcha.php');
    exit;
}

// Vérifier le token
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
    $_SESSION['recaptcha_result'] = [
        'success' => false,
        'error' => 'Erreur de connexion à Google'
    ];
    header('Location: ../../test-recaptcha.php');
    exit;
}

$recaptcha_result = json_decode($response, true);

// Stocker le résultat en session
$_SESSION['recaptcha_result'] = [
    'success' => $recaptcha_result['success'] ?? false,
    'score' => $recaptcha_result['score'] ?? 0,
    'action' => $recaptcha_result['action'] ?? 'N/A',
    'challenge_ts' => $recaptcha_result['challenge_ts'] ?? 'N/A',
    'hostname' => $recaptcha_result['hostname'] ?? 'N/A',
    'error-codes' => $recaptcha_result['error-codes'] ?? [],
    'threshold' => $recaptcha_threshold,
    'passed' => ($recaptcha_result['success'] && $recaptcha_result['score'] >= $recaptcha_threshold)
];

// Log
if (getenv('APP_DEBUG') == 'true') {
    error_log("reCAPTCHA TEST - Score: " . $recaptcha_result['score'] . ", Threshold: " . $recaptcha_threshold . ", Passed: " . ($recaptcha_result['score'] >= $recaptcha_threshold ? 'YES' : 'NO'));
}

header('Location: ../../test-recaptcha.php');
exit;
?>
