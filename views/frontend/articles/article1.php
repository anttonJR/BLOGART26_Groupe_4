<?php
require_once '../../functions/bbcode.php';
require_once '../../functions/query/select.php';

$numArt = $_GET['id'] ?? null;
if (!$numArt) {
    header('Location: index.php');
    exit;
}

$art = selectOne('ARTICLE', 'numArt', $numArt);
if (!$art) {
    die('Article introuvable');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($art['libTltArt']) ?></title>
    <link rel="stylesheet" href="../../../assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <!-- Titre -->
        <h1><?= htmlspecialchars($art['libTltArt']) ?></h1>
        
        <!-- Chapô -->
        <p class="lead"><?= bbcode_to_html($art['libChapArt']) ?></p>
        
        <!-- Accroche -->
        <?php if ($art['libAccrochArt']): ?>
            <blockquote class="blockquote">
                <?= bbcode_to_html($art['libAccrochArt']) ?>
            </blockquote>
        <?php endif; ?>
        
        <!-- Paragraphe 1 -->
        <div class="mt-4">
            <?= bbcode_to_html($art['parag1Art']) ?>
        </div>
        
        <!-- Titre éditorial -->
        <?php if ($art['libSsTitl1Art']): ?>
            <h3 class="mt-4"><?= htmlspecialchars($art['libSsTitl1Art']) ?></h3>
        <?php endif; ?>
        
        <!-- Paragraphe 2 -->
        <?php if ($art['parag2Art']): ?>
            <div class="mt-3">
                <?= bbcode_to_html($art['parag2Art']) ?>
            </div>
        <?php endif; ?>
        
        <!-- Paragraphe 3 -->
        <?php if ($art['parag3Art']): ?>
            <div class="mt-3">
                <?= bbcode_to_html($art['parag3Art']) ?>
            </div>
        <?php endif; ?>
        
        <!-- Conclusion -->
        <?php if ($art['libConcArt']): ?>
            <div class="mt-4 alert alert-secondary">
                <strong>Conclusion :</strong>
                <?= bbcode_to_html($art['libConcArt']) ?>
            </div>
        <?php endif; ?>
        
        <hr>
        <p class="text-muted">
            Publié le <?= date('d/m/Y à H:i', strtotime($art['dtCreaArt'])) ?>
        </p>
    </div>
</body>
</html>