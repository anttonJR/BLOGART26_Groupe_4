<?php
$pageTitle = 'Membres';
require_once __DIR__ . '/../includes/header.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;

$stmt = $DB->query("
    SELECT m.*, s.libStat 
    FROM MEMBRE m 
    LEFT JOIN STATUT s ON m.numStat = s.numStat 
    ORDER BY m.numMemb
");
$membres = $stmt->fetchAll();
?>

<div class="page-header">
    <h1><i class="bi bi-people me-2"></i>Membres</h1>
    <a href="<?= ROOT_URL ?>/views/backend/members/create.php" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nouveau membre
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
                    <th>Nom</th>
                    <th>Pseudo</th>
                    <th>Email</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($membres)): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">Aucun membre</td></tr>
                <?php else: ?>
                    <?php foreach ($membres as $m): ?>
                        <tr>
                            <td><?= $m['numMemb'] ?></td>
                            <td><strong><?= htmlspecialchars(($m['prenomMemb'] ?? '') . ' ' . ($m['nomMemb'] ?? '')) ?></strong></td>
                            <td><?= htmlspecialchars($m['pseudoMemb'] ?? '') ?></td>
                            <td><?= htmlspecialchars($m['eMailMemb'] ?? '') ?></td>
                            <td>
                                <?php 
                                $statClass = match($m['numStat'] ?? 3) {
                                    1 => 'bg-danger',
                                    2 => 'bg-warning',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?= $statClass ?>"><?= htmlspecialchars($m['libStat'] ?? 'Membre') ?></span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group-actions">
                                    <a href="<?= ROOT_URL ?>/views/backend/members/edit.php?id=<?= $m['numMemb'] ?>" class="btn btn-action btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                    <a href="<?= ROOT_URL ?>/views/backend/members/delete.php?id=<?= $m['numMemb'] ?>" class="btn btn-action btn-outline-danger" onclick="return confirm('Supprimer ?')"><i class="bi bi-trash"></i></a>
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
