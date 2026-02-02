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
// Charger les thématiques
$thematiques = selectAll('THEMATIQUE', 'numThem');
?>

<!-- Thématique -->
<div class="mb-3">
    <label class="form-label">Thématique *</label>
    <select name="numThem" class="form-control" required>
        <option value="">Sélectionnez une thématique</option>
        <?php foreach ($thematiques as $them): ?>
            <option value="<?= $them['numThem'] ?>" 
                    <?= $them['numThem'] == $art['numThem'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($them['libThem']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
<!DOCTYPE html>
<html>
<head>
    <title>Modifier un article</title>
    <link rel="stylesheet" href="../../../assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Modifier l'article</h1>
        
        <form method="POST" action="../../api/articles/update.php">
            <input type="hidden" name="numArt" value="<?= $art['numArt'] ?>">
            
            <!-- Titre -->
            <div class="mb-3">
                <label class="form-label">Titre de l'article *</label>
                <input type="text" 
                       name="libTltArt" 
                       class="form-control" 
                       value="<?= htmlspecialchars($art['libTltArt']) ?>" 
                       maxlength="100" 
                       required>
            </div>
            
            <!-- Chapô -->
            <div class="mb-3">
                <label class="form-label">Chapô *</label>
                <textarea name="libChapArt" 
                          class="form-control" 
                          rows="3" 
                          required><?= htmlspecialchars($art['libChapArt']) ?></textarea>
            </div>
            
            <!-- Accroche -->
            <div class="mb-3">
                <label class="form-label">Accroche</label>
                <input type="text" 
                       name="libAccrochArt" 
                       class="form-control" 
                       value="<?= htmlspecialchars($art['libAccrochArt'] ?? '') ?>" 
                       maxlength="100">
            </div>
            
            <!-- Paragraphe 1 -->
            <div class="mb-3">
                <label class="form-label">Paragraphe 1 *</label>
                <textarea name="parag1Art" 
                          class="form-control" 
                          rows="5" 
                          required><?= htmlspecialchars($art['parag1Art']) ?></textarea>
            </div>
            
            <!-- Titre éditorial -->
            <div class="mb-3">
                <label class="form-label">Titre éditorial</label>
                <input type="text" 
                       name="libSsTitl1Art" 
                       class="form-control" 
                       value="<?= htmlspecialchars($art['libSsTitl1Art'] ?? '') ?>" 
                       maxlength="100">
            </div>
            
            <!-- Paragraphe 2 -->
            <div class="mb-3">
                <label class="form-label">Paragraphe 2</label>
                <textarea name="parag2Art" 
                          class="form-control" 
                          rows="5"><?= htmlspecialchars($art['parag2Art'] ?? '') ?></textarea>
            </div>
            
            <!-- Paragraphe 3 -->
            <div class="mb-3">
                <label class="form-label">Paragraphe 3</label>
                <textarea name="parag3Art" 
                          class="form-control" 
                          rows="5"><?= htmlspecialchars($art['parag3Art'] ?? '') ?></textarea>
            </div>
            
            <!-- Conclusion -->
            <div class="mb-3">
                <label class="form-label">Conclusion</label>
                <textarea name="libConcArt" 
                          class="form-control" 
                          rows="3"><?= htmlspecialchars($art['libConcArt'] ?? '') ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
            <a href="list.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>
</html>