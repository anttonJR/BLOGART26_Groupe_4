<?php
// CRUD Statuts : UPDATE (chargement + mise à jour)
// Logique AVANT l'inclusion du header (pour permettre les redirections)
require_once __DIR__ . '/../../../config.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;
$error = '';

// Récupérer le statut ciblé
if (!isset($_GET['id'])) {
    header('Location: ' . ROOT_URL . '/views/backend/statuts/list.php');
    exit;
}

$id = (int)$_GET['id'];
$stmt = $DB->prepare("SELECT * FROM STATUT WHERE numStat = ?");
$stmt->execute([$id]);
$statut = $stmt->fetch();

if (!$statut) {
    header('Location: ' . ROOT_URL . '/views/backend/statuts/list.php');
    exit;
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libStat = trim($_POST['libStat'] ?? '');
    
    if (empty($libStat)) {
        $error = "Le libellé est requis";
    } else {
        // Mise à jour du statut
        $stmt = $DB->prepare("UPDATE STATUT SET libStat = ? WHERE numStat = ?");
        $stmt->execute([$libStat, $id]);
        $_SESSION['success'] = "Statut modifié avec succès";
        header('Location: ' . ROOT_URL . '/views/backend/statuts/list.php');
        exit;
    }
}

// Inclusion du header APRÈS la logique de redirection
$pageTitle = 'Modifier statut';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-pencil me-2"></i>Modifier statut</h1>
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
                       value="<?= htmlspecialchars($_POST['libStat'] ?? $statut['libStat']) ?>">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Enregistrer
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
