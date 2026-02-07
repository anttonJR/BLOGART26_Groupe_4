<?php
/**
 * ============================================================
 * TRASH.PHP - Corbeille des articles (Backend Admin)
 * ============================================================
 * 
 * RÔLE : Affiche les articles supprimés logiquement (delLogiq = 1).
 *        Permet de les restaurer ou de les supprimer définitivement.
 * 
 * FONCTIONNEMENT :
 * - Récupère les articles avec delLogiq = 1
 * - Affiche la date de suppression et un compte à rebours (30 jours)
 * - Propose 2 actions : Restaurer ou Supprimer définitivement
 * - Bouton "Vider la corbeille" pour tout supprimer d'un coup
 * 
 * CALCUL D'EXPIRATION :
 * - 30 jours après la suppression logique (dtDelLogArt)
 * - floor((time() - strtotime($dtDelLogArt)) / 86400) = nombre de jours écoulés
 * - 86400 = 60*60*24 = nombre de secondes dans un jour
 * 
 * FLUX :
 *   list.php → trash.php (voir la corbeille)
 *   trash.php → restore.php?id=X (restaurer un article)
 *   trash.php → permanent-delete.php?id=X (supprimer définitivement)
 *   trash.php → empty-trash.php (vider toute la corbeille)
 * ============================================================
 */

/* --- Inclusion du header + vérification admin --- */
$pageTitle = 'Corbeille - Articles';
require_once __DIR__ . '/../includes/header.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;

/* 
 * --- Récupérer les articles dans la corbeille ---
 * WHERE delLogiq = 1 : uniquement les articles "supprimés"
 * ORDER BY dtDelLogArt DESC : les plus récemment supprimés en premier
 */
$stmt = $DB->query("
    SELECT a.*, t.libThem 
    FROM ARTICLE a 
    LEFT JOIN THEMATIQUE t ON a.numThem = t.numThem 
    WHERE a.delLogiq = 1
    ORDER BY a.dtDelLogArt DESC
");
$articles = $stmt->fetchAll();
?>

<!-- ============================================ -->
<!-- EN-TÊTE : titre + boutons                     -->
<!-- ============================================ -->
<div class="page-header">
    <h1><i class="bi bi-trash me-2"></i>Corbeille</h1>
    <div class="d-flex gap-2">
        <!-- Bouton retour vers la liste des articles actifs -->
        <a href="<?= ROOT_URL ?>/views/backend/articles/list.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Retour aux articles
        </a>
        <!-- Bouton vider la corbeille : visible uniquement s'il y a des articles -->
        <!-- confirm() : demande une confirmation JS avant la suppression irréversible -->
        <?php if (!empty($articles)): ?>
        <a href="<?= ROOT_URL ?>/views/backend/articles/empty-trash.php" class="btn btn-danger" onclick="return confirm('Vider définitivement la corbeille ? Cette action est irréversible.')">
            <i class="bi bi-trash-fill me-1"></i>Vider la corbeille
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Message d'information sur la politique de rétention -->
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>
    Les articles dans la corbeille seront définitivement supprimés après 30 jours. Vous pouvez les restaurer à tout moment.
</div>

<!-- ============================================ -->
<!-- TABLEAU DES ARTICLES SUPPRIMÉS                -->
<!-- ============================================ -->
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
                <!-- Si la corbeille est vide : icône + message -->
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
                            <!-- Titre grisé pour montrer que l'article est inactif -->
                            <td>
                                <strong class="text-muted"><?= htmlspecialchars($art['libTitrArt'] ?? 'Sans titre') ?></strong>
                            </td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($art['libThem'] ?? 'N/A') ?></span></td>
                            <td>
                                <?php if (isset($art['dtDelLogArt'])): ?>
                                    <!-- Date de suppression formatée -->
                                    <?= date('d/m/Y H:i', strtotime($art['dtDelLogArt'])) ?>
                                    <br><small class="text-muted">
                                        <?php 
                                        /* 
                                         * Calcul du temps restant avant expiration :
                                         * time() - strtotime(dtDelLogArt) = secondes écoulées
                                         * / 86400 = conversion en jours
                                         * floor() = arrondi inférieur
                                         * 30 - jours écoulés = jours restants
                                         */
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
                                    <!-- Bouton restaurer : remet delLogiq = 0 (voir restore.php) -->
                                    <a href="<?= ROOT_URL ?>/views/backend/articles/restore.php?id=<?= $art['numArt'] ?>" class="btn btn-action btn-outline-success" title="Restaurer">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </a>
                                    <!-- Bouton supprimer définitivement (voir permanent-delete.php) -->
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
