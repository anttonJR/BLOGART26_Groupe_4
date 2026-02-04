<?php
$pageTitle = 'Nouvel article';
require_once __DIR__ . '/../includes/header.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;
$error = '';

// Charger les thématiques
$stmtThem = $DB->query("SELECT * FROM THEMATIQUE ORDER BY libThem");
$thematiques = $stmtThem->fetchAll();

// Charger les mots-clés
$stmtMot = $DB->query("SELECT * FROM MOTCLE ORDER BY libMotCle");
$motcles = $stmtMot->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libTitrArt = trim($_POST['libTitrArt'] ?? '');
    $libChapoArt = trim($_POST['libChapoArt'] ?? '');
    $libAccrochArt = trim($_POST['libAccrochArt'] ?? '');
    $parag1Art = trim($_POST['parag1Art'] ?? '');
    $numThem = (int)($_POST['numThem'] ?? 0);
    $numMemb = $_SESSION['user']['numMemb'] ?? 1;
    $selectedMotcles = $_POST['motcles'] ?? [];
    
    if (empty($libTitrArt)) {
        $error = "Le titre est requis";
    } else {
        $stmt = $DB->prepare("INSERT INTO ARTICLE (libTitrArt, libChapoArt, libAccrochArt, parag1Art, dtCreaArt, numThem, numMemb) VALUES (?, ?, ?, ?, NOW(), ?, ?)");
        $stmt->execute([$libTitrArt, $libChapoArt, $libAccrochArt, $parag1Art, $numThem ?: null, $numMemb]);
        $numArt = $DB->lastInsertId();
        
        // Associer les mots-clés (table MOTCLEARTICLE)
        foreach ($selectedMotcles as $numMotCle) {
            $stmtMC = $DB->prepare("INSERT INTO MOTCLEARTICLE (numArt, numMotCle) VALUES (?, ?)");
            $stmtMC->execute([$numArt, $numMotCle]);
        }
        
        $_SESSION['success'] = "Article créé avec succès";
        header('Location: ' . ROOT_URL . '/views/backend/articles/list.php');
        exit;
    }
}
?>

<div class="page-header">
    <h1><i class="bi bi-plus-lg me-2"></i>Nouvel article</h1>
    <a href="<?= ROOT_URL ?>/views/backend/articles/list.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<div class="admin-card">
    <div class="card-body">
        <form method="POST">
            <div class="mb-3">
                <label for="libTitrArt" class="form-label">Titre *</label>
                <input type="text" class="form-control" id="libTitrArt" name="libTitrArt" required 
                       value="<?= htmlspecialchars($_POST['libTitrArt'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="libChapoArt" class="form-label">Chapô</label>
                <textarea class="form-control" id="libChapoArt" name="libChapoArt" rows="2"><?= htmlspecialchars($_POST['libChapoArt'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="libAccrochArt" class="form-label">Accroche</label>
                <textarea class="form-control" id="libAccrochArt" name="libAccrochArt" rows="2"><?= htmlspecialchars($_POST['libAccrochArt'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="parag1Art" class="form-label">Contenu</label>
                <textarea class="form-control" id="parag1Art" name="parag1Art" rows="10"><?= htmlspecialchars($_POST['parag1Art'] ?? '') ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="numThem" class="form-label">Thématique</label>
                    <select class="form-select" id="numThem" name="numThem">
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($thematiques as $them): ?>
                            <option value="<?= $them['numThem'] ?>" <?= (($_POST['numThem'] ?? '') == $them['numThem']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($them['libThem']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mots-clés</label>
                    <div class="border rounded p-3" style="max-height: 150px; overflow-y: auto;">
                        <?php foreach ($motcles as $mot): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="motcles[]" 
                                       value="<?= $mot['numMotCle'] ?>" id="mot<?= $mot['numMotCle'] ?>"
                                       <?= in_array($mot['numMotCle'], $_POST['motcles'] ?? []) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="mot<?= $mot['numMotCle'] ?>">
                                    <?= htmlspecialchars($mot['libMotCle']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Créer
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
