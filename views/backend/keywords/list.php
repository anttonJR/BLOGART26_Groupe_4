<?php
$pageTitle = 'Mots-clés';
require_once __DIR__ . '/../includes/header.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;

$stmt = $DB->query("SELECT * FROM MOTCLE ORDER BY numMotCle");
$motcles = $stmt->fetchAll();
?>

<div class="page-header">
    <h1><i class="bi bi-key me-2"></i>Mots-clés</h1>
    <a href="<?= ROOT_URL ?>/views/backend/keywords/create.php" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nouveau mot-clé
    </a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<div class="admin-card">
    <div class="card-body p-0">
        <table class="table admin-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Libellé</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($motcles)): ?>
                    <tr><td colspan="3" class="text-center py-4 text-muted">Aucun mot-clé</td></tr>
                <?php else: ?>
                    <?php foreach ($motcles as $mot): ?>
                        <tr>
                            <td><?= $mot['numMotCle'] ?></td>
                            <td><strong><?= htmlspecialchars($mot['libMotCle']) ?></strong></td>
                            <td class="text-end">
                                <div class="btn-group-actions">
                                    <a href="<?= ROOT_URL ?>/views/backend/keywords/edit.php?id=<?= $mot['numMotCle'] ?>" class="btn btn-action btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                    <a href="<?= ROOT_URL ?>/views/backend/keywords/delete.php?id=<?= $mot['numMotCle'] ?>" class="btn btn-action btn-outline-danger" onclick="return confirm('Supprimer ?')"><i class="bi bi-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
