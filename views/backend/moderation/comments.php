<?php
$pageTitle = 'Modération';
require_once __DIR__ . '/../includes/header.php';
require_once ROOT . '/functions/auth.php';
requireModerator();

global $DB;

// Traitement des actions de modération
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'])) {
    $id = (int)$_POST['id'];
    $action = $_POST['action'];
    
    if ($action === 'approve') {
        $stmt = $DB->prepare("UPDATE COMMENT SET attModOK = 1 WHERE numCom = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "Commentaire approuvé";
    } elseif ($action === 'reject') {
        $stmt = $DB->prepare("DELETE FROM COMMENT WHERE numCom = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "Commentaire supprimé";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$stmt = $DB->query("
    SELECT c.*, a.libTitrArt, m.prenomMemb, m.nomMemb 
    FROM COMMENT c 
    LEFT JOIN ARTICLE a ON c.numArt = a.numArt 
    LEFT JOIN MEMBRE m ON c.numMemb = m.numMemb 
    WHERE c.attModOK = 0 OR c.attModOK IS NULL
    ORDER BY c.dtCreaCom DESC
");
$pendingComments = $stmt->fetchAll();
?>

<div class="page-header">
    <h1><i class="bi bi-shield-check me-2"></i>Modération des commentaires</h1>
    <span class="badge bg-warning fs-6"><?= count($pendingComments) ?> en attente</span>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<div class="admin-card">
    <div class="card-body p-0">
        <?php if (empty($pendingComments)): ?>
            <div class="text-center py-5">
                <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                <h4 class="mt-3">Aucun commentaire en attente</h4>
                <p class="text-muted">Tous les commentaires ont été modérés.</p>
            </div>
        <?php else: ?>
            <?php foreach ($pendingComments as $com): ?>
                <div class="p-4 border-bottom">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <strong><?= htmlspecialchars(($com['prenomMemb'] ?? 'Anonyme') . ' ' . ($com['nomMemb'] ?? '')) ?></strong>
                            <span class="text-muted">sur</span>
                            <em><?= htmlspecialchars($com['libTitrArt'] ?? 'Article inconnu') ?></em>
                        </div>
                        <small class="text-muted"><?= isset($com['dtCreaCom']) ? date('d/m/Y H:i', strtotime($com['dtCreaCom'])) : '' ?></small>
                    </div>
                    <div class="bg-light p-3 rounded mb-3">
                        <?= nl2br(htmlspecialchars($com['libCom'] ?? '')) ?>
                    </div>
                    <div class="d-flex gap-2">
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="id" value="<?= $com['numCom'] ?>">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="bi bi-check-lg me-1"></i>Approuver
                            </button>
                        </form>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce commentaire ?')">
                            <input type="hidden" name="id" value="<?= $com['numCom'] ?>">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="bi bi-trash me-1"></i>Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
