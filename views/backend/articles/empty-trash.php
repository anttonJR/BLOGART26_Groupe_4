<?php
/**
 * ============================================================
 * EMPTY-TRASH.PHP - Vider la corbeille (suppression massive)
 * ============================================================
 * 
 * RÔLE : Supprime définitivement TOUS les articles de la corbeille
 *        et leurs données associées. Action irréversible.
 * 
 * FONCTIONNEMENT :
 * 1. Récupère tous les ID des articles avec delLogiq = 1
 * 2. Pour CHAQUE article, supprime les données associées (CIR)
 *    - Likes (LIKEART)
 *    - Commentaires (COMMENT)
 *    - Mots-clés associés (MOTCLEARTICLE)
 * 3. Supprime tous les articles de la corbeille en une seule requête
 * 
 * POURQUOI UNE BOUCLE ?
 * On ne peut pas faire un simple "DELETE FROM ARTICLE WHERE delLogiq = 1"
 * à cause des contraintes d'intégrité référentielle (CIR).
 * Il faut d'abord supprimer les données enfants pour chaque article.
 * 
 * OPTIMISATION POSSIBLE :
 * On pourrait utiliser des requêtes avec sous-requête :
 * DELETE FROM LIKEART WHERE numArt IN (SELECT numArt FROM ARTICLE WHERE delLogiq = 1)
 * au lieu de boucler article par article.
 * 
 * FLUX :
 *   trash.php → empty-trash.php → list.php (avec message succès)
 * ============================================================
 */

/* --- Chargement config + vérification admin --- */
require_once __DIR__ . '/../../../config.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;

/* 
 * --- Récupérer les ID des articles dans la corbeille ---
 * FETCH_COLUMN : retourne un tableau simple [1, 5, 8] au lieu de
 * [['numArt'=>1], ['numArt'=>5], ...] → plus pratique pour la boucle
 */
$stmt = $DB->query("SELECT numArt FROM ARTICLE WHERE delLogiq = 1");
$articles = $stmt->fetchAll(PDO::FETCH_COLUMN);

/* 
 * --- Boucle : supprimer les données associées pour chaque article ---
 * On doit respecter l'ordre des CIR :
 * 1. LIKEART (FK → ARTICLE)
 * 2. COMMENT (FK → ARTICLE)
 * 3. MOTCLEARTICLE (FK → ARTICLE)
 */
foreach ($articles as $numArt) {
    /* Supprimer les likes de cet article */
    $stmtLike = $DB->prepare("DELETE FROM LIKEART WHERE numArt = ?");
    $stmtLike->execute([$numArt]);
    
    /* Supprimer les commentaires de cet article */
    $stmtCom = $DB->prepare("DELETE FROM COMMENT WHERE numArt = ?");
    $stmtCom->execute([$numArt]);
    
    /* Supprimer les associations mots-clés de cet article */
    $stmtMot = $DB->prepare("DELETE FROM MOTCLEARTICLE WHERE numArt = ?");
    $stmtMot->execute([$numArt]);
}

/* 
 * --- Suppression massive de tous les articles de la corbeille ---
 * Maintenant que les données enfants sont supprimées, on peut
 * supprimer tous les articles avec delLogiq = 1 en une seule requête
 */
$DB->exec("DELETE FROM ARTICLE WHERE delLogiq = 1");

/* --- Flash message + redirection vers la liste --- */
$_SESSION['success'] = "Corbeille vidée avec succès";
header('Location: ' . ROOT_URL . '/views/backend/articles/list.php');
exit;
