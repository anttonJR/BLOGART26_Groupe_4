<?php
/**
 * ============================================================
 * RESTORE.PHP - Restauration d'un article depuis la corbeille
 * ============================================================
 * 
 * RÔLE : Remet un article supprimé logiquement dans la liste active.
 *        C'est l'inverse de delete.php (soft delete).
 * 
 * FONCTIONNEMENT :
 * - Remet delLogiq = 0 (article actif)
 * - Efface dtDelLogArt = NULL (plus de date de suppression)
 * - L'article réapparaît dans list.php
 * 
 * FLUX :
 *   trash.php → restore.php?id=X → trash.php (avec message succès)
 * ============================================================
 */

/* --- Chargement config + vérification admin --- */
require_once __DIR__ . '/../../../config.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;

/* --- Vérification de l'ID --- */
if (!isset($_GET['id'])) {
    header('Location: ' . ROOT_URL . '/views/backend/articles/trash.php');
    exit;
}

$id = (int)$_GET['id'];

/* 
 * --- Restauration : inverse du soft delete ---
 * delLogiq = 0 : l'article redevient actif (visible dans list.php)
 * dtDelLogArt = NULL : on efface la date de suppression
 */
$stmt = $DB->prepare("UPDATE ARTICLE SET delLogiq = 0, dtDelLogArt = NULL WHERE numArt = ?");
$stmt->execute([$id]);

/* --- Flash message + redirection vers la corbeille --- */
$_SESSION['success'] = "Article restauré avec succès";
header('Location: ' . ROOT_URL . '/views/backend/articles/trash.php');
exit;
