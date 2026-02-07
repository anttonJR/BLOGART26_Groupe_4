<?php
/**
 * ============================================================
 * PERMANENT-DELETE.PHP - Suppression définitive d'un article
 * ============================================================
 * 
 * RÔLE : Supprime un article et TOUTES ses données associées
 *        de manière irréversible (Hard Delete).
 * 
 * POURQUOI SUPPRIMER LES DONNÉES ASSOCIÉES D'ABORD ?
 * La BDD utilise des contraintes d'intégrité référentielle (CIR)
 * avec ON DELETE RESTRICT. Cela signifie que MySQL REFUSE de
 * supprimer un article si des données liées existent encore.
 * 
 * ORDRE DE SUPPRESSION (respect des CIR) :
 * 1. LIKEART     → les likes sur cet article (FK vers ARTICLE)
 * 2. COMMENT     → les commentaires sur cet article (FK vers ARTICLE)
 * 3. MOTCLEARTICLE → les associations mot-clé/article (FK vers ARTICLE)
 * 4. ARTICLE     → l'article lui-même (enfin possible car plus de FK pointant dessus)
 * 
 * Si on essayait de supprimer ARTICLE en premier, MySQL renverrait :
 * "Cannot delete or update a parent row: a foreign key constraint fails"
 * 
 * FLUX :
 *   trash.php → permanent-delete.php?id=X → trash.php (avec message succès)
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
 * --- Étape 1 : Supprimer les likes associés ---
 * Table LIKEART : contient les likes des membres sur les articles
 * FK : numArt → ARTICLE(numArt) avec ON DELETE RESTRICT
 * → On doit les supprimer AVANT l'article
 */
$stmt = $DB->prepare("DELETE FROM LIKEART WHERE numArt = ?");
$stmt->execute([$id]);

/* 
 * --- Étape 2 : Supprimer les commentaires associés ---
 * Table COMMENT : contient les commentaires des membres sur les articles
 * FK : numArt → ARTICLE(numArt) avec ON DELETE RESTRICT
 */
$stmt = $DB->prepare("DELETE FROM COMMENT WHERE numArt = ?");
$stmt->execute([$id]);

/* 
 * --- Étape 3 : Supprimer les associations mots-clés ---
 * Table MOTCLEARTICLE : table de jointure N:N entre ARTICLE et MOTCLE
 * FK : numArt → ARTICLE(numArt) avec ON DELETE RESTRICT
 * Note : on ne supprime PAS les mots-clés eux-mêmes (table MOTCLE),
 *        seulement les associations avec cet article
 */
$stmt = $DB->prepare("DELETE FROM MOTCLEARTICLE WHERE numArt = ?");
$stmt->execute([$id]);

/* 
 * --- Étape 4 : Supprimer l'article définitivement ---
 * Maintenant que toutes les FK pointant vers cet article sont supprimées,
 * MySQL accepte la suppression de la ligne dans ARTICLE
 */
$stmt = $DB->prepare("DELETE FROM ARTICLE WHERE numArt = ?");
$stmt->execute([$id]);

/* --- Flash message + redirection vers la corbeille --- */
$_SESSION['success'] = "Article supprimé définitivement";
header('Location: ' . ROOT_URL . '/views/backend/articles/trash.php');
exit;
