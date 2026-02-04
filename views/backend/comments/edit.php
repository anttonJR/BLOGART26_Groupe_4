<?php
$pageTitle = 'Modifier commentaire';
require_once __DIR__ . '/../includes/header.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;
$error = '';

if (!isset($_GET['id'])) {
    header('Location: ' . ROOT_URL . '/views/backend/comments/list.php');
    exit;
}

$id = (int)$_GET['id'];
$stmt = $DB->prepare("SELECT * FROM COMMENT WHERE numCom = ?");
$stmt->execute([$id]);
$com = $stmt->fetch();

if (!$com) {
    header('Location: ' . ROOT_URL . '/views/backend/comments/list.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libCom = trim($_POST['libCom'] ?? '');
    $attModOK = isset($_POST['attModOK']) ? 1 : 0;
    
    if (empty($libCom)) {
        $error = "Le commentaire est requis";
    } else {
        $stmt = $DB->prepare("UPDATE COMMENT SET libCom = ?, attModOK = ? WHERE numCom = ?");
        $stmt->execute([$libCom, $attModOK, $id]);
        $_SESSION['success'] = "Commentaire modifié avec succès";
        header('Location: ' . ROOT_URL . '/views/backend/comments/list.php');
        exit;
    }
}
?>

<div class="page-header">
    <h1><i class="bi bi-pencil me-2"></i>Modifier commentaire</h1>
    <a href="<?= ROOT_URL ?>/views/backend/comments/list.php" class="btn btn-outline-secondary">
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
                <label for="libCom" class="form-label">Commentaire *</label>
                <textarea class="form-control" id="libCom" name="libCom" rows="5" required><?= htmlspecialchars($_POST['libCom'] ?? $com['libCom']) ?></textarea>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="attModOK" name="attModOK" 
                       <?= (isset($_POST['attModOK']) || (!isset($_POST['libCom']) && $com['attModOK'] == 1)) ? 'checked' : '' ?>>
                <label class="form-check-label" for="attModOK">Approuvé</label>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Enregistrer
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
