<?php
/**
 * ============================================================
 * DELETE.PHP - Suppression logique d'un article (Soft Delete)
 * ============================================================
 * 
 * RÔLE : Déplace un article dans la "corbeille" sans le supprimer
 *        réellement de la base de données.
 * 
 * SUPPRESSION LOGIQUE (Soft Delete) vs SUPPRESSION PHYSIQUE (Hard Delete) :
 * - Soft Delete : on met delLogiq = 1 et on enregistre la date (dtDelLogArt)
 *   → L'article reste en BDD mais n'apparaît plus dans list.php
 *   → Il est visible dans trash.php et peut être restauré
 * - Hard Delete : suppression définitive (voir permanent-delete.php)
 *   → L'article est supprimé de la BDD, irrécupérable
 * 
 * POURQUOI le Soft Delete ?
 * - Protection contre les suppressions accidentelles
 * - Possibilité de restaurer (undo)
 * - Traçabilité (on sait quand l'article a été supprimé)
 * - Conforme aux bonnes pratiques de gestion de contenu
 * 
 * FONCTIONNEMENT :
 * 1. Vérifie les droits admin
 * 2. Récupère l'ID depuis GET (?id=X)
 * 3. UPDATE : met delLogiq = 1 et dtDelLogArt = NOW()
 * 4. Redirige vers list.php avec un message de succès
 * 
 * NOTE : Ce fichier est appelé via un lien GET (pas un formulaire POST)
 *        car c'est un simple clic sur le bouton corbeille dans list.php.
 *        Idéalement, il faudrait un token CSRF même pour les GET de suppression.
 * 
 * FLUX :
 *   list.php → delete.php?id=X → list.php (avec message succès)
 * ============================================================
 */

/* --- Chargement de la configuration et vérification admin --- */
require_once __DIR__ . '/../../../config.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;

/* --- Vérification de l'ID : si absent, redirection vers la liste --- */
if (!isset($_GET['id'])) {
    header('Location: ' . ROOT_URL . '/views/backend/articles/list.php');
    exit;
}

/* --- Cast en entier pour la sécurité (évite l'injection SQL) --- */
$id = (int)$_GET['id'];

/* 
 * --- Soft Delete : mise à jour au lieu de suppression ---
 * delLogiq = 1 : marque l'article comme "dans la corbeille"
 * dtDelLogArt = NOW() : enregistre la date/heure de la suppression logique
 *   → Utilisé dans trash.php pour calculer "expire dans X jours"
 */
$stmt = $DB->prepare("UPDATE ARTICLE SET delLogiq = 1, dtDelLogArt = NOW() WHERE numArt = ?");
$stmt->execute([$id]);

/* --- Flash message + redirection vers la liste --- */
$_SESSION['success'] = "Article déplacé dans la corbeille";
header('Location: ' . ROOT_URL . '/views/backend/articles/list.php');
exit;
