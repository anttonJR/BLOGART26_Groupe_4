<?php
$pageTitle = 'Nouvelle thématique';
require_once __DIR__ . '/../includes/header.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libThem = trim($_POST['libThem'] ?? '');
    
    if (empty($libThem)) {
        $error = "Le libellé est requis";
    } else {
        $stmt = $DB->prepare("INSERT INTO THEMATIQUE (libThem) VALUES (?)");
        $stmt->execute([$libThem]);
        $_SESSION['success'] = "Thématique créée avec succès";
        header('Location: ' . ROOT_URL . '/views/backend/thematiques/list.php');
        exit;
    }
}
?>

<div class="page-header">
    <h1><i class="bi bi-plus-lg me-2"></i>Nouvelle thématique</h1>
    <a href="<?= ROOT_URL ?>/views/backend/thematiques/list.php" class="btn btn-outline-secondary">
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
                <label for="libThem" class="form-label">Libellé *</label>
                <input type="text" class="form-control" id="libThem" name="libThem" required 
                       value="<?= htmlspecialchars($_POST['libThem'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Créer
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
