<?php
echo "<h1>Test de diagnostic</h1>";

echo "<h2>1. Session</h2>";
session_start();
echo "Session OK<br>";

echo "<h2>2. Config</h2>";
try {
    require_once '../../config.php';
    echo "Config chargé OK<br>";
} catch (Exception $e) {
    echo "ERREUR Config: " . $e->getMessage() . "<br>";
}

echo "<h2>3. Base de données</h2>";
global $DB;
if ($DB) {
    echo "Connexion DB OK<br>";
} else {
    echo "ERREUR: Pas de connexion DB<br>";
}

echo "<h2>4. CSRF</h2>";
try {
    require_once '../../functions/csrf.php';
    echo "CSRF chargé OK<br>";
    $token = generateCSRFToken();
    echo "Token généré: " . substr($token, 0, 20) . "...<br>";
} catch (Exception $e) {
    echo "ERREUR CSRF: " . $e->getMessage() . "<br>";
}

echo "<h2>5. Variables ENV</h2>";
echo "RECAPTCHA_SECRET_KEY: " . (isset($_ENV['RECAPTCHA_SECRET_KEY']) ? "défini" : "NON DÉFINI") . "<br>";
echo "RECAPTCHA_SITE_KEY: " . (isset($_ENV['RECAPTCHA_SITE_KEY']) ? "défini" : "NON DÉFINI") . "<br>";

echo "<h2>6. POST Data</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<h2>7. Test formulaire</h2>";
?>
<form method="POST" action="test-signup.php">
    <input type="hidden" name="test" value="1">
    <button type="submit">Envoyer test</button>
</form>
