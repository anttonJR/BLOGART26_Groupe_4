<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
require_once ROOT . '/functions/auth.php';

requireAdmin();

global $DB;

// Statistiques générales
$sqlStats = "SELECT 
    (SELECT COUNT(*) FROM ARTICLE) as nb_articles,
    (SELECT COUNT(*) FROM MEMBRE) as nb_membres,
    (SELECT COUNT(*) FROM COMMENT WHERE attModOK = 0 AND dtDelLogCom IS NULL) as nb_comments_pending,
    (SELECT COUNT(*) FROM COMMENT WHERE attModOK = 1 AND dtDelLogCom IS NULL) as nb_comments_approved,
    (SELECT COUNT(*) FROM LIKEART WHERE likeA = 1) as nb_likes_total,
    (SELECT COUNT(*) FROM THEMATIQUE) as nb_thematiques,
    (SELECT COUNT(*) FROM MOTCLE) as nb_motscles,
    (SELECT COUNT(*) FROM STATUT) as nb_statuts";

$stmtStats = $DB->query($sqlStats);
$stats = $stmtStats->fetch();

// Articles les plus likés
$sqlTopLiked = "SELECT a.numArt, a.libTitrArt, COUNT(l.numMemb) as nb_likes
                FROM ARTICLE a
                LEFT JOIN LIKEART l ON a.numArt = l.numArt AND l.likeA = 1
                GROUP BY a.numArt, a.libTitrArt
                ORDER BY nb_likes DESC
                LIMIT 5";

$stmtTopLiked = $DB->query($sqlTopLiked);
$topLiked = $stmtTopLiked->fetchAll();

// Derniers commentaires en attente
$sqlPendingComments = "SELECT c.*, m.pseudoMemb, a.libTitrArt
                       FROM COMMENT c
                       INNER JOIN MEMBRE m ON c.numMemb = m.numMemb
                       INNER JOIN ARTICLE a ON c.numArt = a.numArt
                       WHERE c.attModOK = 0 AND c.dtDelLogCom IS NULL
                       ORDER BY c.dtCreaCom DESC
                       LIMIT 5";

$stmtPending = $DB->query($sqlPendingComments);
$pendingComments = $stmtPending->fetchAll();

// Derniers articles
$sqlRecentArticles = "SELECT a.numArt, a.libTitrArt, a.dtCreaArt, t.libThem
                      FROM ARTICLE a
                      LEFT JOIN THEMATIQUE t ON a.numThem = t.numThem
                      ORDER BY a.dtCreaArt DESC
                      LIMIT 5";

$stmtRecent = $DB->query($sqlRecentArticles);
$recentArticles = $stmtRecent->fetchAll();
?>

<!-- Page Header -->
<div class="page-header">
    <h1><i class="bi bi-speedometer2 me-2"></i>Dashboard</h1>
    <div class="btn-group">
        <a href="<?= ROOT_URL ?>/views/backend/articles/create.php" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nouvel article
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card fade-in">
            <div class="stat-icon bg-primary-soft">
                <i class="bi bi-file-earmark-text"></i>
            </div>
            <div class="stat-value"><?= $stats['nb_articles'] ?></div>
            <div class="stat-label">Articles publiés</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card fade-in">
            <div class="stat-icon bg-success-soft">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-value"><?= $stats['nb_membres'] ?></div>
            <div class="stat-label">Membres inscrits</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card fade-in">
            <div class="stat-icon bg-warning-soft">
                <i class="bi bi-chat-dots"></i>
            </div>
            <div class="stat-value"><?= $stats['nb_comments_pending'] ?></div>
            <div class="stat-label">Commentaires en attente</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card fade-in">
            <div class="stat-icon bg-danger-soft">
                <i class="bi bi-heart"></i>
            </div>
            <div class="stat-value"><?= $stats['nb_likes_total'] ?></div>
            <div class="stat-label">Likes totaux</div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="admin-card mb-4">
    <div class="card-header">
        <h5><i class="bi bi-lightning-charge me-2"></i>Accès rapide</h5>
    </div>
    <div class="card-body">
        <div class="quick-links">
            <a href="<?= ROOT_URL ?>/views/backend/articles/list.php" class="quick-link-card">
                <i class="bi bi-file-earmark-text"></i>
                <h6>Articles</h6>
                <small><?= $stats['nb_articles'] ?> article(s)</small>
            </a>
            <a href="<?= ROOT_URL ?>/views/backend/thematiques/list.php" class="quick-link-card">
                <i class="bi bi-tags"></i>
                <h6>Thématiques</h6>
                <small><?= $stats['nb_thematiques'] ?> thématique(s)</small>
            </a>
            <a href="<?= ROOT_URL ?>/views/backend/keywords/list.php" class="quick-link-card">
                <i class="bi bi-hash"></i>
                <h6>Mots-clés</h6>
                <small><?= $stats['nb_motscles'] ?> mot(s)-clé(s)</small>
            </a>
            <a href="<?= ROOT_URL ?>/views/backend/comments/list.php" class="quick-link-card">
                <i class="bi bi-chat-left-text"></i>
                <h6>Commentaires</h6>
                <small><?= $stats['nb_comments_approved'] ?> validé(s)</small>
            </a>
            <a href="<?= ROOT_URL ?>/views/backend/moderation/comments.php" class="quick-link-card">
                <i class="bi bi-shield-check"></i>
                <h6>Modération</h6>
                <small><?= $stats['nb_comments_pending'] ?> en attente</small>
            </a>
            <a href="<?= ROOT_URL ?>/views/backend/likes/list.php" class="quick-link-card">
                <i class="bi bi-heart"></i>
                <h6>Likes</h6>
                <small><?= $stats['nb_likes_total'] ?> like(s)</small>
            </a>
            <a href="<?= ROOT_URL ?>/views/backend/members/list.php" class="quick-link-card">
                <i class="bi bi-people"></i>
                <h6>Membres</h6>
                <small><?= $stats['nb_membres'] ?> membre(s)</small>
            </a>
            <a href="<?= ROOT_URL ?>/views/backend/statuts/list.php" class="quick-link-card">
                <i class="bi bi-person-badge"></i>
                <h6>Statuts</h6>
                <small><?= $stats['nb_statuts'] ?> statut(s)</small>
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Commentaires en attente -->
    <div class="col-lg-6">
        <div class="admin-card h-100">
            <div class="card-header">
                <h5>
                    <i class="bi bi-chat-dots text-warning me-2"></i>
                    Commentaires en attente
                    <?php if ($stats['nb_comments_pending'] > 0): ?>
                        <span class="badge bg-warning text-dark ms-2"><?= $stats['nb_comments_pending'] ?></span>
                    <?php endif; ?>
                </h5>
                <a href="<?= ROOT_URL ?>/views/backend/moderation/comments.php" class="btn btn-sm btn-outline-primary">
                    Voir tout
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($pendingComments)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-check-circle fs-1 mb-2"></i>
                        <p>Aucun commentaire en attente</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($pendingComments as $com): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <strong class="text-truncate me-2"><?= htmlspecialchars($com['libTitrArt']) ?></strong>
                                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($com['dtCreaCom'])) ?></small>
                                </div>
                                <p class="mb-1 text-truncate-2 small"><?= htmlspecialchars(substr($com['libCom'], 0, 100)) ?>...</p>
                                <small class="text-muted">
                                    <i class="bi bi-person me-1"></i>@<?= htmlspecialchars($com['pseudoMemb']) ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Articles les plus likés -->
    <div class="col-lg-6">
        <div class="admin-card h-100">
            <div class="card-header">
                <h5><i class="bi bi-heart text-danger me-2"></i>Top articles likés</h5>
                <a href="<?= ROOT_URL ?>/views/backend/likes/list.php" class="btn btn-sm btn-outline-primary">
                    Voir tout
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($topLiked)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-heart fs-1 mb-2"></i>
                        <p>Aucun like pour le moment</p>
                    </div>
                <?php else: ?>
                    <table class="table admin-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Article</th>
                                <th class="text-end">Likes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topLiked as $index => $art): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-<?= $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'light text-dark') ?>">
                                            <?= $index + 1 ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($art['libTitrArt']) ?></td>
                                    <td class="text-end">
                                        <span class="badge bg-danger">
                                            <i class="bi bi-heart-fill me-1"></i><?= $art['nb_likes'] ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Derniers articles -->
<div class="admin-card mt-4">
    <div class="card-header">
        <h5><i class="bi bi-clock-history me-2"></i>Derniers articles</h5>
        <a href="<?= ROOT_URL ?>/views/backend/articles/list.php" class="btn btn-sm btn-outline-primary">
            Voir tout
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Thématique</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentArticles as $art): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($art['libTitrArt']) ?></strong></td>
                            <td>
                                <?php if ($art['libThem']): ?>
                                    <span class="badge bg-info"><?= htmlspecialchars($art['libThem']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td><small class="text-muted"><?= date('d/m/Y', strtotime($art['dtCreaArt'])) ?></small></td>
                            <td class="text-end">
                                <div class="btn-group-actions">
                                    <a href="<?= ROOT_URL ?>/views/backend/articles/edit.php?id=<?= $art['numArt'] ?>" class="btn btn-action btn-outline-primary" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
