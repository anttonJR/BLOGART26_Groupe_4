<?php
$pageTitle = 'Corbeille - Articles';
require_once __DIR__ . '/../includes/header.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;

// Articles dans la corbeille
$stmt = $DB->query("
    SELECT a.*, t.libThem 
    FROM ARTICLE a 
    LEFT JOIN THEMATIQUE t ON a.numThem = t.numThem 
    WHERE a.delLogiq = 1
    ORDER BY a.dtDelLogArt DESC
");
$articles = $stmt->fetchAll();
?>

<div class="page-header">
    <h1><i class="bi bi-trash me-2"></i>Corbeille</h1>
    <div class="d-flex gap-2">
        <a href="<?= ROOT_URL ?>/views/backend/articles/list.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Retour aux articles
        </a>
        <?php if (!empty($articles)): ?>
        <a href="<?= ROOT_URL ?>/views/backend/articles/empty-trash.php" class="btn btn-danger" onclick="return confirm('Vider définitivement la corbeille ? Cette action est irréversible.')">
            <i class="bi bi-trash-fill me-1"></i>Vider la corbeille
        </a>
        <?php endif; ?>
    </div>
</div>

<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>
    Les articles dans la corbeille seront définitivement supprimés après 30 jours. Vous pouvez les restaurer à tout moment.
</div>

<div class="admin-card">
    <div class="card-body p-0">
        <table class="table admin-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Titre</th>
                    <th>Thématique</th>
                    <th>Supprimé le</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($articles)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-trash text-muted" style="font-size: 3rem;"></i>
                            <p class="mt-3 text-muted">La corbeille est vide</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($articles as $art): ?>
                        <tr>
                            <td><?= $art['numArt'] ?></td>
                            <td>
                                <strong class="text-muted"><?= htmlspecialchars($art['libTitrArt'] ?? 'Sans titre') ?></strong>
                            </td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($art['libThem'] ?? 'N/A') ?></span></td>
                            <td>
                                <?php if (isset($art['dtDelLogArt'])): ?>
                                    <?= date('d/m/Y H:i', strtotime($art['dtDelLogArt'])) ?>
                                    <br><small class="text-muted">
                                        <?php 
                                        $daysLeft = 30 - floor((time() - strtotime($art['dtDelLogArt'])) / 86400);
                                        echo $daysLeft > 0 ? "Expire dans $daysLeft jours" : "Expiré";
                                        ?>
                                    </small>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group-actions">
                                    <a href="<?= ROOT_URL ?>/views/backend/articles/restore.php?id=<?= $art['numArt'] ?>" class="btn btn-action btn-outline-success" title="Restaurer">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </a>
                                    <a href="<?= ROOT_URL ?>/views/backend/articles/permanent-delete.php?id=<?= $art['numArt'] ?>" class="btn btn-action btn-outline-danger" onclick="return confirm('Supprimer définitivement ? Cette action est irréversible.')" title="Supprimer définitivement">
                                        <i class="bi bi-x-lg"></i>
                                    </a>
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
