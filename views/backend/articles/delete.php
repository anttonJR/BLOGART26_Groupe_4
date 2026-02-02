<?php
session_start();
require_once '../../functions/query/select.php';

$numArt = $_GET['id'] ?? null;
if (!$numArt) {
    header('Location: list.php');
    exit;
}

$art = selectOne('ARTICLE', 'numArt', $numArt);
if (!$art) {
    die('Article introuvable');
}

?>
<?php
// Charger l'article avec sa thématique
$sql = "SELECT a.*, t.libThem 
        FROM ARTICLE a 
        LEFT JOIN THEMATIQUE t ON a.numThem = t.numThem 
        WHERE a.numArt = ?";
$pdo = getConnection();
$stmt = $pdo->prepare($sql);
$stmt->execute([$numArt]);
$art = $stmt->fetch();
?>

<!-- Ajouter l'affichage -->
<dt class="col-sm-3">Thématique :</dt>
<dd class="col-sm-9"><?= htmlspecialchars($art['libThem'] ?? 'Aucune') ?></dd>
<!DOCTYPE html>
<html>
<head>
    <title>Supprimer un article</title>
    <link rel="stylesheet" href="../../../assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Confirmer la suppression</h1>
        
        <div class="alert alert-warning">
            <strong>Attention !</strong> Cette action est irréversible.
        </div>
        
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($art['libTltArt']) ?></h5>
                <p class="card-text"><?= htmlspecialchars(substr($art['libChapArt'], 0, 200)) ?>...</p>
                <dl class="row">
                    <dt class="col-sm-3">Numéro :</dt>
                    <dd class="col-sm-9"><?= $art['numArt'] ?></dd>
                    
                    <dt class="col-sm-3">Date création :</dt>
                    <dd class="col-sm-9"><?= date('d/m/Y H:i', strtotime($art['dtCreaArt'])) ?></dd>
                </dl>
            </div>
        </div>
        
        <form method="POST" action="../../api/articles/delete.php" class="mt-3">
            <input type="hidden" name="numArt" value="<?= $art['numArt'] ?>">
            
            <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
            <a href="list.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>
</html>