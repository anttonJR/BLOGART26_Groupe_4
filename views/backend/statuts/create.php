<?php
// CRUD Statuts : CREATE (formulaire + insertion)
$pageTitle = 'Nouveau statut';
require_once __DIR__ . '/../includes/header.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;
$error = '';

// Traitement du formulaire de création
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libStat = trim($_POST['libStat'] ?? '');
    
    if (empty($libStat)) {
        $error = "Le libellé est requis";
    } else {
        // Insertion du nouveau statut en base
        $stmt = $DB->prepare("INSERT INTO STATUT (libStat) VALUES (?)");
        $stmt->execute([$libStat]);
        $_SESSION['success'] = "Statut créé avec succès";
        header('Location: ' . ROOT_URL . '/views/backend/statuts/list.php');
        exit;
    }
}
?>

<div class="page-header">
    <h1><i class="bi bi-plus-lg me-2"></i>Nouveau statut</h1>
    <a href="<?= ROOT_URL ?>/views/backend/statuts/list.php" class="btn btn-outline-secondary">
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
                <label for="libStat" class="form-label">Libellé *</label>
                <input type="text" class="form-control" id="libStat" name="libStat" required 
                       value="<?= htmlspecialchars($_POST['libStat'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Créer
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
