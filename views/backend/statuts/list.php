<?php
// CRUD Statuts : READ (liste des statuts)
$pageTitle = 'Statuts';
require_once __DIR__ . '/../includes/header.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;

$stmt = $DB->query("SELECT * FROM STATUT ORDER BY numStat"); // Lecture des statuts
$statuts = $stmt->fetchAll();
?>

<div class="page-header">
    <h1><i class="bi bi-shield me-2"></i>Statuts</h1>
    <a href="<?= ROOT_URL ?>/views/backend/statuts/create.php" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nouveau statut
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
                    <th>Permissions</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($statuts)): ?>
                    <tr><td colspan="4" class="text-center py-4 text-muted">Aucun statut</td></tr>
                <?php else: ?>
                    <?php foreach ($statuts as $stat): ?>
                        <tr>
                            <td><?= $stat['numStat'] ?></td>
                            <td>
                                <!-- Badge couleur selon le niveau de rôle -->
                                <?php 
                                $badgeClass = match($stat['numStat']) {
                                    1 => 'bg-danger',
                                    2 => 'bg-warning',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($stat['libStat']) ?></span>
                            </td>
                            <td>
                                <?php if ($stat['numStat'] == 1): ?>
                                    <small class="text-muted">Accès complet (Dashboard, CRUD, Modération)</small>
                                <?php elseif ($stat['numStat'] == 2): ?>
                                    <small class="text-muted">Modération des commentaires</small>
                                <?php else: ?>
                                    <small class="text-muted">Lecture, commentaires</small>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group-actions">
                                    <a href="<?= ROOT_URL ?>/views/backend/statuts/edit.php?id=<?= $stat['numStat'] ?>" class="btn btn-action btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                    <?php if ($stat['numStat'] > 3): ?>
                                        <a href="<?= ROOT_URL ?>/views/backend/statuts/delete.php?id=<?= $stat['numStat'] ?>" class="btn btn-action btn-outline-danger" onclick="return confirm('Supprimer ?')"><i class="bi bi-trash"></i></a>
                                    <?php endif; ?>
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
