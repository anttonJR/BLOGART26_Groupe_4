<?php
$pageTitle = 'Likes';
require_once __DIR__ . '/../includes/header.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;

$stmt = $DB->query("
    SELECT l.*, a.libTitrArt, m.prenomMemb, m.nomMemb 
    FROM LIKEART l 
    LEFT JOIN ARTICLE a ON l.numArt = a.numArt 
    LEFT JOIN MEMBRE m ON l.numMemb = m.numMemb 
    ORDER BY a.libTitrArt
");
$likes = $stmt->fetchAll();

// Stats par article
$stmtStats = $DB->query("
    SELECT a.numArt, a.libTitrArt, COUNT(l.numMemb) as nbLikes
    FROM ARTICLE a
    LEFT JOIN LIKEART l ON a.numArt = l.numArt
    GROUP BY a.numArt, a.libTitrArt
    ORDER BY nbLikes DESC
    LIMIT 10
");
$topArticles = $stmtStats->fetchAll();
?>

<div class="page-header">
    <h1><i class="bi bi-heart me-2"></i>Likes</h1>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="admin-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-trophy me-2"></i>Top articles</h5>
            </div>
            <div class="card-body p-0">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Article</th>
                            <th class="text-center">Likes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topArticles as $art): ?>
                            <tr>
                                <td><?= htmlspecialchars($art['libTitrArt'] ?? 'Sans titre') ?></td>
                                <td class="text-center">
                                    <span class="badge bg-danger"><?= $art['nbLikes'] ?> <i class="bi bi-heart-fill"></i></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="card-header">
        <h5 class="mb-0">Tous les likes</h5>
    </div>
    <div class="card-body p-0">
        <table class="table admin-table mb-0">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Membre</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($likes)): ?>
                    <tr><td colspan="3" class="text-center py-4 text-muted">Aucun like</td></tr>
                <?php else: ?>
                    <?php foreach ($likes as $like): ?>
                        <tr>
                            <td><?= htmlspecialchars($like['libTitrArt'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars(($like['prenomMemb'] ?? '') . ' ' . ($like['nomMemb'] ?? '')) ?></td>
                            <td class="text-end">
                                <a href="<?= ROOT_URL ?>/views/backend/likes/delete.php?art=<?= $like['numArt'] ?>&memb=<?= $like['numMemb'] ?>" class="btn btn-action btn-outline-danger" onclick="return confirm('Supprimer ?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
