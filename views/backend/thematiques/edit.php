<?php
$pageTitle = 'Modifier thématique';
require_once __DIR__ . '/../includes/header.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;
$error = '';

if (!isset($_GET['id'])) {
    header('Location: ' . ROOT_URL . '/views/backend/thematiques/list.php');
    exit;
}

$id = (int)$_GET['id'];
$stmt = $DB->prepare("SELECT * FROM THEMATIQUE WHERE numThem = ?");
$stmt->execute([$id]);
$them = $stmt->fetch();

if (!$them) {
    header('Location: ' . ROOT_URL . '/views/backend/thematiques/list.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libThem = trim($_POST['libThem'] ?? '');
    
    if (empty($libThem)) {
        $error = "Le libellé est requis";
    } else {
        $stmt = $DB->prepare("UPDATE THEMATIQUE SET libThem = ? WHERE numThem = ?");
        $stmt->execute([$libThem, $id]);
        $_SESSION['success'] = "Thématique modifiée avec succès";
        header('Location: ' . ROOT_URL . '/views/backend/thematiques/list.php');
        exit;
    }
}
?>

<div class="page-header">
    <h1><i class="bi bi-pencil me-2"></i>Modifier thématique</h1>
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
                       value="<?= htmlspecialchars($_POST['libThem'] ?? $them['libThem']) ?>">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Enregistrer
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
