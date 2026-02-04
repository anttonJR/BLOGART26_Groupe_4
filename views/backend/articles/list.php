<?php
$pageTitle = 'Articles';
require_once __DIR__ . '/../includes/header.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;

// Exclure les articles dans la corbeille
$stmt = $DB->query("
    SELECT a.*, t.libThem 
    FROM ARTICLE a 
    LEFT JOIN THEMATIQUE t ON a.numThem = t.numThem 
    WHERE a.delLogiq = 0 OR a.delLogiq IS NULL
    ORDER BY a.dtCreaArt DESC
");
$articles = $stmt->fetchAll();

// Compter les articles dans la corbeille
$stmtTrash = $DB->query("SELECT COUNT(*) as count FROM ARTICLE WHERE delLogiq = 1");
$trashCount = $stmtTrash->fetch()['count'];
?>

<div class="page-header">
    <h1><i class="bi bi-file-earmark-text me-2"></i>Articles</h1>
    <div class="d-flex gap-2">
        <?php if ($trashCount > 0): ?>
        <a href="<?= ROOT_URL ?>/views/backend/articles/trash.php" class="btn btn-outline-secondary">
            <i class="bi bi-trash me-1"></i>Corbeille <span class="badge bg-danger"><?= $trashCount ?></span>
        </a>
        <?php endif; ?>
        <a href="<?= ROOT_URL ?>/views/backend/articles/create.php" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nouvel article
        </a>
    </div>
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
                    <th>Titre</th>
                    <th>Thématique</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($articles)): ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">Aucun article</td></tr>
                <?php else: ?>
                    <?php foreach ($articles as $art): ?>
                        <tr>
                            <td><?= $art['numArt'] ?></td>
                            <td><strong><?= htmlspecialchars($art['libTitrArt'] ?? 'Sans titre') ?></strong></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($art['libThem'] ?? 'N/A') ?></span></td>
                            <td><?= isset($art['dtCreaArt']) ? date('d/m/Y', strtotime($art['dtCreaArt'])) : 'N/A' ?></td>
                            <td class="text-end">
                                <div class="btn-group-actions">
                                    <a href="<?= ROOT_URL ?>/views/backend/articles/edit.php?id=<?= $art['numArt'] ?>" class="btn btn-action btn-outline-primary" title="Modifier"><i class="bi bi-pencil"></i></a>
                                    <a href="<?= ROOT_URL ?>/views/backend/articles/delete.php?id=<?= $art['numArt'] ?>" class="btn btn-action btn-outline-warning" onclick="return confirm('Déplacer dans la corbeille ?')" title="Corbeille"><i class="bi bi-trash"></i></a>
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
