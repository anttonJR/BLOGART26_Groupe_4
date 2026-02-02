<?php
session_start();
?>
<?php
// Charger les thématiques
require_once '../../functions/query/select.php';
$thematiques = selectAll('THEMATIQUE', 'numThem');
?>

<!-- Thématique -->
<div class="mb-3">
    <label class="form-label">Thématique *</label>
    <select name="numThem" class="form-control" required>
        <option value="">Sélectionnez une thématique</option>
        <?php foreach ($thematiques as $them): ?>
            <option value="<?= $them['numThem'] ?>">
                <?= htmlspecialchars($them['libThem']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<!-- IMPORTANT : Ajouter enctype au formulaire -->
<form method="POST" action="../../api/articles/create.php" enctype="multipart/form-data">

<!-- Ajouter après le champ Thématique -->
<div class="mb-3">
    <label class="form-label">Image de l'article</label>
    <input type="file" 
           name="imageArt" 
           class="form-control" 
           accept="image/jpeg,image/png,image/gif">
    <small class="form-text text-muted">
        Formats acceptés : JPG, PNG, GIF - Taille max : 5 Mo
    </small>
</div>

<!DOCTYPE html>
<html>
<head>
    <title>Créer un article</title>
    <link rel="stylesheet" href="../../../assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Nouvel article</h1>
        
       <?php if (isset($_SESSION['errors'])): ?> <!-- Si la session contient des erreurs -->
            <div class="alert alert-danger"> <!-- Bloc d'alerte Bootstrap (style "erreur" rouge) -->
                <?php foreach ($_SESSION['errors'] as $error): ?> <!-- Parcourt chaque message d'erreur -->
                    <p><?= $error ?></p> <!-- Affiche l'erreur (raccourci de echo) -->
                <?php endforeach; ?> <!-- Fin de la boucle foreach -->
            </div>
            <?php unset($_SESSION['errors']); ?> <!-- Supprime les erreurs après affichage (message "flash") -->
        <?php endif; ?> <!-- Fin du if -->

        
        <form method="POST" action="../../api/articles/create.php">
            <!-- Numéro d'article -->
            <div class="mb-3">
                <label class="form-label">Numéro d'article *</label>
                <input type="number" 
                       name="numArt" 
                       class="form-control" 
                       required>
            </div>
            
            <!-- Titre -->
            <div class="mb-3">
                <label class="form-label">Titre de l'article *</label>
                <input type="text" 
                       name="libTltArt" 
                       class="form-control" 
                       maxlength="100" 
                       required>
            </div>
            
            <!-- Chapô -->
            <div class="mb-3">
                <label class="form-label">Chapô (introduction) *</label>
                <textarea name="libChapArt" 
                          class="form-control" 
                          rows="3" 
                          required></textarea>
            </div>
            
            <!-- Accroche -->
            <div class="mb-3">
                <label class="form-label">Accroche</label>
                <input type="text" 
                       name="libAccrochArt" 
                       class="form-control" 
                       maxlength="100">
            </div>
            
            <!-- Paragraphe 1 -->
            <div class="mb-3">
                <label class="form-label">Paragraphe 1 *</label>
                <textarea name="parag1Art" 
                          class="form-control" 
                          rows="5" 
                          required></textarea>
            </div>
            
            <!-- Titre éditorial -->
            <div class="mb-3">
                <label class="form-label">Titre éditorial (sous-titre)</label>
                <input type="text" 
                       name="libSsTitl1Art" 
                       class="form-control" 
                       maxlength="100">
            </div>
            
            <!-- Paragraphe 2 -->
            <div class="mb-3">
                <label class="form-label">Paragraphe 2</label>
                <textarea name="parag2Art" 
                          class="form-control" 
                          rows="5"></textarea>
            </div>
            
            <!-- Paragraphe 3 -->
            <div class="mb-3">
                <label class="form-label">Paragraphe 3</label>
                <textarea name="parag3Art" 
                          class="form-control" 
                          rows="5"></textarea>
            </div>
            
            <!-- Conclusion -->
            <div class="mb-3">
                <label class="form-label">Conclusion</label>
                <textarea name="libConcArt" 
                          class="form-control" 
                          rows="3"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Créer l'article</button>
            <a href="list.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>

    <!-- Aide BBCode -->
<div class="alert alert-info">
    <h5>Guide BBCode</h5>
    <ul>
        <li><code>[b]texte[/b]</code> → <strong>Gras</strong></li>
        <li><code>[i]texte[/i]</code> → <em>Italique</em></li>
        <li><code>[u]texte[/u]</code> → <u>Souligné</u></li>
        <li><code>[url=https://example.com]Cliquez` ici[/url]</code> → Lien</li>
        <li><code>[anchor]section1[/anchor]</code> → Ancre</li>
        <li><code>[goto=section1]Aller à section 1[/goto]</code> → Lien vers ancre</li>
        <li>Emojis : <code>:smile: :wink: :heart: :star:</code></li>
    </ul>
</div>
</body>
</html>