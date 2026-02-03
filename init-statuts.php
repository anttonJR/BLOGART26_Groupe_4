<?php
require_once 'config.php';

global $DB;

echo "<h1>Insertion des statuts par défaut</h1>";

// Vérifier si des statuts existent
$stmt = $DB->query("SELECT * FROM STATUT");
$statuts = $stmt->fetchAll();

echo "<h2>Statuts existants:</h2>";
if (empty($statuts)) {
    echo "<p>Aucun statut trouvé. Insertion des statuts par défaut...</p>";
    
    // Insérer les statuts de base
    $statutsDefaut = [
        ['libStat' => 'Membre'],
        ['libStat' => 'Modérateur'],
        ['libStat' => 'Administrateur']
    ];
    
    $sql = "INSERT INTO STATUT (libStat) VALUES (:libStat)";
    $stmt = $DB->prepare($sql);
    
    foreach ($statutsDefaut as $statut) {
        $stmt->execute($statut);
        echo "<p style='color:green'>✓ Statut '{$statut['libStat']}' inséré</p>";
    }
    
    echo "<h2>Statuts après insertion:</h2>";
    $stmt = $DB->query("SELECT * FROM STATUT");
    $statuts = $stmt->fetchAll();
}

echo "<table border='1'><tr><th>numStat</th><th>libStat</th><th>dtCreaStat</th></tr>";
foreach ($statuts as $s) {
    echo "<tr><td>{$s['numStat']}</td><td>{$s['libStat']}</td><td>{$s['dtCreaStat']}</td></tr>";
}
echo "</table>";

echo "<p><a href='views/frontend/security/signup.php'>Retour à l'inscription</a></p>";
?>
