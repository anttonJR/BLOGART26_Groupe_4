<?php
// Logique de traitement AVANT l'inclusion du header
session_start();
require_once __DIR__ . '/../../../config.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;
$error = '';

if (!isset($_GET['id'])) {
    header('Location: ' . ROOT_URL . '/views/backend/articles/list.php');
    exit;
}

$id = (int)$_GET['id'];
$stmt = $DB->prepare("SELECT * FROM ARTICLE WHERE numArt = ?");
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article) {
    header('Location: ' . ROOT_URL . '/views/backend/articles/list.php');
    exit;
}

// Charger les thématiques
$stmtThem = $DB->query("SELECT * FROM THEMATIQUE ORDER BY libThem");
$thematiques = $stmtThem->fetchAll();

// Charger les mots-clés
$stmtMot = $DB->query("SELECT * FROM MOTCLE ORDER BY libMotCle");
$motcles = $stmtMot->fetchAll();

// Mots-clés actuels (table MOTCLEARTICLE)
$stmtCurrent = $DB->prepare("SELECT numMotCle FROM MOTCLEARTICLE WHERE numArt = ?");
$stmtCurrent->execute([$id]);
$currentMotcles = $stmtCurrent->fetchAll(PDO::FETCH_COLUMN);

// Traitement du formulaire POST AVANT l'affichage HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libTitrArt = trim($_POST['libTitrArt'] ?? '');
    $libChapoArt = trim($_POST['libChapoArt'] ?? '');
    $libAccrochArt = trim($_POST['libAccrochArt'] ?? '');
    $parag1Art = trim($_POST['parag1Art'] ?? '');
    $numThem = (int)($_POST['numThem'] ?? 0);
    $selectedMotcles = $_POST['motcles'] ?? [];
    
    if (empty($libTitrArt)) {
        $error = "Le titre est requis";
    } else {
        $stmt = $DB->prepare("UPDATE ARTICLE SET libTitrArt = ?, libChapoArt = ?, libAccrochArt = ?, parag1Art = ?, numThem = ? WHERE numArt = ?");
        $stmt->execute([$libTitrArt, $libChapoArt, $libAccrochArt, $parag1Art, $numThem ?: null, $id]);
        
        // Mettre à jour les mots-clés (table MOTCLEARTICLE)
        $stmtDel = $DB->prepare("DELETE FROM MOTCLEARTICLE WHERE numArt = ?");
        $stmtDel->execute([$id]);
        
        foreach ($selectedMotcles as $numMotCle) {
            $stmtMC = $DB->prepare("INSERT INTO MOTCLEARTICLE (numArt, numMotCle) VALUES (?, ?)");
            $stmtMC->execute([$id, $numMotCle]);
        }
        
        $_SESSION['success'] = "Article modifié avec succès";
        header('Location: ' . ROOT_URL . '/views/backend/articles/list.php');
        exit;
    }
}

// Maintenant on peut inclure le header (après tout traitement qui pourrait rediriger)
$pageTitle = 'Modifier article';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-pencil me-2"></i>Modifier article</h1>
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
                       value="<?= htmlspecialchars($_POST['libTitrArt'] ?? $article['libTitrArt']) ?>">
            </div>
            <div class="mb-3">
                <label for="libChapoArt" class="form-label">Chapô</label>
                <textarea class="form-control" id="libChapoArt" name="libChapoArt" rows="2"><?= htmlspecialchars($_POST['libChapoArt'] ?? $article['libChapoArt'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="libAccrochArt" class="form-label">Accroche</label>
                <textarea class="form-control" id="libAccrochArt" name="libAccrochArt" rows="2"><?= htmlspecialchars($_POST['libAccrochArt'] ?? $article['libAccrochArt'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="parag1Art" class="form-label">Contenu</label>
                <textarea class="form-control" id="parag1Art" name="parag1Art" rows="10"><?= htmlspecialchars($_POST['parag1Art'] ?? $article['parag1Art'] ?? '') ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="numThem" class="form-label">Thématique</label>
                    <select class="form-select" id="numThem" name="numThem">
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($thematiques as $them): ?>
                            <option value="<?= $them['numThem'] ?>" <?= (($_POST['numThem'] ?? $article['numThem']) == $them['numThem']) ? 'selected' : '' ?>>
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
                                       <?= in_array($mot['numMotCle'], $_POST['motcles'] ?? $currentMotcles) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="mot<?= $mot['numMotCle'] ?>">
                                    <?= htmlspecialchars($mot['libMotCle']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Enregistrer
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
