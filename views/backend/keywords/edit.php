<?php
$pageTitle = 'Modifier mot-clé';
require_once __DIR__ . '/../includes/header.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;
$error = '';

if (!isset($_GET['id'])) {
    header('Location: ' . ROOT_URL . '/views/backend/keywords/list.php');
    exit;
}

$id = (int)$_GET['id'];
$stmt = $DB->prepare("SELECT * FROM MOTCLE WHERE numMotCle = ?");
$stmt->execute([$id]);
$mot = $stmt->fetch();

if (!$mot) {
    header('Location: ' . ROOT_URL . '/views/backend/keywords/list.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libMotCle = trim($_POST['libMotCle'] ?? '');
    
    if (empty($libMotCle)) {
        $error = "Le libellé est requis";
    } else {
        $stmt = $DB->prepare("UPDATE MOTCLE SET libMotCle = ? WHERE numMotCle = ?");
        $stmt->execute([$libMotCle, $id]);
        $_SESSION['success'] = "Mot-clé modifié avec succès";
        header('Location: ' . ROOT_URL . '/views/backend/keywords/list.php');
        exit;
    }
}
?>

<div class="page-header">
    <h1><i class="bi bi-pencil me-2"></i>Modifier mot-clé</h1>
    <a href="<?= ROOT_URL ?>/views/backend/keywords/list.php" class="btn btn-outline-secondary">
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
                <label for="libMotCle" class="form-label">Libellé *</label>
                <input type="text" class="form-control" id="libMotCle" name="libMotCle" required 
                       value="<?= htmlspecialchars($_POST['libMotCle'] ?? $mot['libMotCle']) ?>">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Enregistrer
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
