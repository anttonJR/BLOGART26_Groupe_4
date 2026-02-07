<?php
/**
 * ============================================================
 * LIST.PHP - Liste des articles (Backend Admin)
 * ============================================================
 * 
 * RÔLE : Affiche la liste de tous les articles actifs (non supprimés)
 *        dans un tableau avec les actions possibles (modifier, supprimer).
 * 
 * FONCTIONNEMENT :
 * - Récupère tous les articles qui NE SONT PAS dans la corbeille (delLogiq = 0 ou NULL)
 * - Joint la table THEMATIQUE pour afficher le nom de la thématique associée
 * - Affiche un compteur de la corbeille si elle contient des articles
 * - Permet d'accéder à la création, modification et suppression (soft delete)
 * 
 * SÉCURITÉ :
 * - Vérifie que l'utilisateur est admin via requireAdmin()
 * - Les données sont échappées avec htmlspecialchars() pour éviter les XSS
 * 
 * FLUX : 
 *   list.php → edit.php (modifier) 
 *   list.php → delete.php (corbeille = soft delete)
 *   list.php → create.php (nouveau)
 *   list.php → trash.php (voir la corbeille)
 * ============================================================
 */

/* --- Inclusion du header backend (contient session_start, config.php, navbar admin) --- */
$pageTitle = 'Articles';
require_once __DIR__ . '/../includes/header.php';

/* --- Vérification des droits administrateur --- */
/* Si l'utilisateur n'est pas admin, requireAdmin() le redirige vers la page de login */
require_once ROOT . '/functions/auth.php';
requireAdmin();

/* --- Accès à la connexion PDO globale (définie dans config.php) --- */
global $DB;

/* 
 * --- Requête : récupérer les articles actifs ---
 * LEFT JOIN THEMATIQUE : on veut aussi les articles sans thématique (NULL)
 * WHERE delLogiq = 0 OR delLogiq IS NULL : exclut les articles dans la corbeille
 * ORDER BY dtCreaArt DESC : les plus récents d'abord
 */
$stmt = $DB->query("
    SELECT a.*, t.libThem 
    FROM ARTICLE a 
    LEFT JOIN THEMATIQUE t ON a.numThem = t.numThem 
    WHERE a.delLogiq = 0 OR a.delLogiq IS NULL
    ORDER BY a.dtCreaArt DESC
");
$articles = $stmt->fetchAll();

/* 
 * --- Compteur corbeille ---
 * Compte le nombre d'articles avec delLogiq = 1 (suppression logique)
 * pour afficher un badge sur le bouton "Corbeille"
 */
$stmtTrash = $DB->query("SELECT COUNT(*) as count FROM ARTICLE WHERE delLogiq = 1");
$trashCount = $stmtTrash->fetch()['count'];
?>

<!-- ============================================ -->
<!-- EN-TÊTE DE PAGE : titre + boutons d'action   -->
<!-- ============================================ -->
<div class="page-header">
    <h1><i class="bi bi-file-earmark-text me-2"></i>Articles</h1>
    <div class="d-flex gap-2">
        <!-- Bouton corbeille : affiché uniquement s'il y a des articles supprimés -->
        <?php if ($trashCount > 0): ?>
        <a href="<?= ROOT_URL ?>/views/backend/articles/trash.php" class="btn btn-outline-secondary">
            <i class="bi bi-trash me-1"></i>Corbeille <span class="badge bg-danger"><?= $trashCount ?></span>
        </a>
        <?php endif; ?>
        <!-- Bouton création d'un nouvel article -->
        <a href="<?= ROOT_URL ?>/views/backend/articles/create.php" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nouvel article
        </a>
    </div>
</div>

<!-- ============================================ -->
<!-- MESSAGE DE SUCCÈS (après création/modif/suppression) -->
<!-- On lit $_SESSION['success'], on l'affiche, puis on le supprime -->
<!-- pour qu'il ne s'affiche qu'une seule fois (pattern Flash Message) -->
<!-- ============================================ -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<!-- ============================================ -->
<!-- TABLEAU DES ARTICLES                          -->
<!-- ============================================ -->
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
                <!-- Si aucun article, on affiche un message -->
                <?php if (empty($articles)): ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">Aucun article</td></tr>
                <?php else: ?>
                    <!-- Boucle sur chaque article pour créer une ligne du tableau -->
                    <?php foreach ($articles as $art): ?>
                        <tr>
                            <!-- Numéro (clé primaire) -->
                            <td><?= $art['numArt'] ?></td>
                            <!-- Titre échappé contre les XSS -->
                            <td><strong><?= htmlspecialchars($art['libTitrArt'] ?? 'Sans titre') ?></strong></td>
                            <!-- Thématique associée (peut être NULL si pas de jointure) -->
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($art['libThem'] ?? 'N/A') ?></span></td>
                            <!-- Date de création formatée en français -->
                            <td><?= isset($art['dtCreaArt']) ? date('d/m/Y', strtotime($art['dtCreaArt'])) : 'N/A' ?></td>
                            <td class="text-end">
                                <div class="btn-group-actions">
                                    <!-- Bouton modifier : envoie l'ID en paramètre GET -->
                                    <a href="<?= ROOT_URL ?>/views/backend/articles/edit.php?id=<?= $art['numArt'] ?>" class="btn btn-action btn-outline-primary" title="Modifier"><i class="bi bi-pencil"></i></a>
                                    <!-- Bouton corbeille : suppression logique (soft delete) avec confirmation JS -->
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
