<?php
$pageTitle = 'Commentaires';
require_once __DIR__ . '/../includes/header.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;

$stmt = $DB->query("
    SELECT c.*, a.libTitrArt, m.prenomMemb, m.nomMemb 
    FROM COMMENT c 
    LEFT JOIN ARTICLE a ON c.numArt = a.numArt 
    LEFT JOIN MEMBRE m ON c.numMemb = m.numMemb 
    ORDER BY c.dtCreaCom DESC
");
$comments = $stmt->fetchAll();
?>

<div class="page-header">
    <h1><i class="bi bi-chat-dots me-2"></i>Commentaires</h1>
    <a href="<?= ROOT_URL ?>/views/backend/moderation/comments.php" class="btn btn-warning">
        <i class="bi bi-shield-check me-1"></i>Modération
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
                    <th>Commentaire</th>
                    <th>Article</th>
                    <th>Auteur</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($comments)): ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">Aucun commentaire</td></tr>
                <?php else: ?>
                    <?php foreach ($comments as $com): ?>
                        <tr>
                            <td><?= $com['numCom'] ?></td>
                            <td><?= htmlspecialchars(substr($com['libCom'] ?? '', 0, 50)) ?>...</td>
                            <td><?= htmlspecialchars($com['libTitrArt'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars(($com['prenomMemb'] ?? '') . ' ' . ($com['nomMemb'] ?? '')) ?></td>
                            <td><?= isset($com['dtCreaCom']) ? date('d/m/Y', strtotime($com['dtCreaCom'])) : 'N/A' ?></td>
                            <td>
                                <?php if (isset($com['attModOK']) && $com['attModOK'] == 1): ?>
                                    <span class="badge bg-success">Approuvé</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">En attente</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group-actions">
                                    <a href="<?= ROOT_URL ?>/views/backend/comments/edit.php?id=<?= $com['numCom'] ?>" class="btn btn-action btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                    <a href="<?= ROOT_URL ?>/views/backend/comments/delete.php?id=<?= $com['numCom'] ?>" class="btn btn-action btn-outline-danger" onclick="return confirm('Supprimer ?')"><i class="bi bi-trash"></i></a>
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
