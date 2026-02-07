<?php
/**
 * ============================================================
 * EDIT.PHP - Formulaire de modification d'un article (Backend Admin)
 * ============================================================
 * 
 * RÔLE : Affiche un formulaire pré-rempli avec les données de l'article
 *        existant et traite la mise à jour après soumission.
 * 
 * DIFFÉRENCE AVEC CREATE.PHP :
 * - Le traitement POST est fait AVANT l'inclusion du header.
 *   Pourquoi ? Parce que si la mise à jour réussit, on fait un header('Location: ...')
 *   et header() ne fonctionne que si AUCUN HTML n'a été envoyé au navigateur.
 *   Dans create.php, le header est inclus avant le POST car la logique
 *   est plus simple. Ici, c'est fait proprement avec le traitement en premier.
 * 
 * FONCTIONNEMENT :
 * 1. Vérifie que l'ID est passé en GET (?id=X), sinon redirige
 * 2. Charge l'article depuis la BDD, redirige s'il n'existe pas
 * 3. Charge les thématiques, mots-clés, et mots-clés actuels de l'article
 * 4. Si POST : met à jour l'article + ré-associe les mots-clés
 * 5. Affiche le formulaire pré-rempli
 * 
 * GESTION DES MOTS-CLÉS (table MOTCLEARTICLE) :
 * Stratégie "Delete & Re-insert" :
 * - On supprime TOUTES les anciennes associations (DELETE FROM MOTCLEARTICLE WHERE numArt = ?)
 * - On insère les nouvelles (INSERT pour chaque mot-clé coché)
 * - C'est plus simple que de comparer les anciens et nouveaux mots-clés
 * 
 * TABLES UTILISÉES :
 * - ARTICLE : lecture + mise à jour
 * - THEMATIQUE : lecture pour le select
 * - MOTCLE : lecture pour les checkboxes
 * - MOTCLEARTICLE : lecture des associations actuelles + delete/insert
 * 
 * SÉCURITÉ :
 * - requireAdmin() : accès admin uniquement
 * - (int)$_GET['id'] : cast en entier pour éviter l'injection SQL
 * - Requêtes préparées (prepare/execute) contre l'injection SQL
 * - htmlspecialchars() : protection XSS dans le formulaire
 * 
 * FLUX :
 *   list.php → edit.php?id=X (GET: formulaire pré-rempli)
 *   edit.php → edit.php (POST: traitement mise à jour)
 *   edit.php → list.php (redirection après succès)
 * ============================================================
 */

/* 
 * --- Traitement AVANT le header ---
 * session_start() et config.php sont nécessaires ici car on ne passe pas
 * par le header backend (qui les inclut normalement)
 */
session_start();
require_once __DIR__ . '/../../../config.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;
$error = '';

/* 
 * --- Vérification de l'ID ---
 * L'ID de l'article doit être passé en paramètre GET (?id=X)
 * Si absent, on redirige vers la liste (sécurité + UX)
 */
if (!isset($_GET['id'])) {
    header('Location: ' . ROOT_URL . '/views/backend/articles/list.php');
    exit;
}

/* 
 * --- Chargement de l'article ---
 * (int) cast en entier : si quelqu'un passe "abc" dans l'URL, ça devient 0
 * Requête préparée avec ? : protection contre l'injection SQL
 */
$id = (int)$_GET['id'];
$stmt = $DB->prepare("SELECT * FROM ARTICLE WHERE numArt = ?");
$stmt->execute([$id]);
$article = $stmt->fetch();

/* Si l'article n'existe pas en BDD, redirection vers la liste */
if (!$article) {
    header('Location: ' . ROOT_URL . '/views/backend/articles/list.php');
    exit;
}

/* --- Charger les thématiques (pour le select) --- */
$stmtThem = $DB->query("SELECT * FROM THEMATIQUE ORDER BY libThem");
$thematiques = $stmtThem->fetchAll();

/* --- Charger tous les mots-clés (pour les checkboxes) --- */
$stmtMot = $DB->query("SELECT * FROM MOTCLE ORDER BY libMotCle");
$motcles = $stmtMot->fetchAll();

/* 
 * --- Charger les mots-clés actuellement associés à cet article ---
 * FETCH_COLUMN : retourne un tableau simple [1, 3, 5] au lieu de
 * [['numMotCle'=>1], ['numMotCle'=>3], ...] → plus facile pour in_array()
 */
$stmtCurrent = $DB->prepare("SELECT numMotCle FROM MOTCLEARTICLE WHERE numArt = ?");
$stmtCurrent->execute([$id]);
$currentMotcles = $stmtCurrent->fetchAll(PDO::FETCH_COLUMN);

/* 
 * ============================================
 * TRAITEMENT DU FORMULAIRE (POST)
 * ============================================
 * Exécuté AVANT le HTML pour pouvoir faire header('Location: ...')
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /* Récupération et nettoyage des données POST */
    $libTitrArt = trim($_POST['libTitrArt'] ?? '');
    $libChapoArt = trim($_POST['libChapoArt'] ?? '');
    $libAccrochArt = trim($_POST['libAccrochArt'] ?? '');
    $parag1Art = trim($_POST['parag1Art'] ?? '');
    $numThem = (int)($_POST['numThem'] ?? 0);
    $selectedMotcles = $_POST['motcles'] ?? [];
    
    /* Validation : le titre est obligatoire */
    if (empty($libTitrArt)) {
        $error = "Le titre est requis";
    } else {
        /* 
         * --- UPDATE de l'article ---
         * On met à jour tous les champs textuels + la thématique
         * numThem ?: null : si 0 (pas de sélection), on stocke NULL
         */
        $stmt = $DB->prepare("UPDATE ARTICLE SET libTitrArt = ?, libChapoArt = ?, libAccrochArt = ?, parag1Art = ?, numThem = ? WHERE numArt = ?");
        $stmt->execute([$libTitrArt, $libChapoArt, $libAccrochArt, $parag1Art, $numThem ?: null, $id]);
        
        /* 
         * --- Mise à jour des mots-clés (stratégie Delete & Re-insert) ---
         * Étape 1 : Supprimer TOUTES les anciennes associations
         * Étape 2 : Insérer les nouvelles associations cochées
         * Pourquoi cette stratégie ? C'est plus simple que de comparer
         * les anciens et nouveaux pour savoir quoi ajouter/supprimer
         */
        $stmtDel = $DB->prepare("DELETE FROM MOTCLEARTICLE WHERE numArt = ?");
        $stmtDel->execute([$id]);
        
        foreach ($selectedMotcles as $numMotCle) {
            $stmtMC = $DB->prepare("INSERT INTO MOTCLEARTICLE (numArt, numMotCle) VALUES (?, ?)");
            $stmtMC->execute([$id, $numMotCle]);
        }
        
        /* Flash message + redirection */
        $_SESSION['success'] = "Article modifié avec succès";
        header('Location: ' . ROOT_URL . '/views/backend/articles/list.php');
        exit;
    }
}

/* 
 * --- Inclusion du header APRÈS le traitement POST ---
 * Si on arrive ici, c'est soit un GET, soit un POST avec erreur de validation
 * Le header envoie du HTML, donc on ne peut plus faire header() après
 */
$pageTitle = 'Modifier article';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- ============================================ -->
<!-- EN-TÊTE + bouton retour                      -->
<!-- ============================================ -->
<div class="page-header">
    <h1><i class="bi bi-pencil me-2"></i>Modifier article</h1>
    <a href="<?= ROOT_URL ?>/views/backend/articles/list.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

<!-- Affichage de l'erreur de validation -->
<?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<!-- ============================================ -->
<!-- FORMULAIRE DE MODIFICATION                    -->
<!-- 
    DIFFÉRENCE AVEC CREATE.PHP : 
    Les valeurs par défaut viennent de $article (BDD) au lieu de '' :
    - $_POST['libTitrArt'] ?? $article['libTitrArt']
    - Si POST existe (soumission avec erreur) → on affiche la valeur saisie
    - Sinon → on affiche la valeur actuelle en BDD
-->
<!-- ============================================ -->
<div class="admin-card">
    <div class="card-body">
        <form method="POST">
            <!-- TITRE : pré-rempli avec la valeur BDD ou POST -->
            <div class="mb-3">
                <label for="libTitrArt" class="form-label">Titre *</label>
                <input type="text" class="form-control" id="libTitrArt" name="libTitrArt" required 
                       value="<?= htmlspecialchars($_POST['libTitrArt'] ?? $article['libTitrArt']) ?>">
            </div>
            <!-- CHAPÔ -->
            <div class="mb-3">
                <label for="libChapoArt" class="form-label">Chapô</label>
                <textarea class="form-control" id="libChapoArt" name="libChapoArt" rows="2"><?= htmlspecialchars($_POST['libChapoArt'] ?? $article['libChapoArt'] ?? '') ?></textarea>
            </div>
            <!-- ACCROCHE -->
            <div class="mb-3">
                <label for="libAccrochArt" class="form-label">Accroche</label>
                <textarea class="form-control" id="libAccrochArt" name="libAccrochArt" rows="2"><?= htmlspecialchars($_POST['libAccrochArt'] ?? $article['libAccrochArt'] ?? '') ?></textarea>
            </div>
            <!-- CONTENU -->
            <div class="mb-3">
                <label for="parag1Art" class="form-label">Contenu</label>
                <textarea class="form-control" id="parag1Art" name="parag1Art" rows="10"><?= htmlspecialchars($_POST['parag1Art'] ?? $article['parag1Art'] ?? '') ?></textarea>
            </div>
            <div class="row">
                <!-- 
                    THÉMATIQUE :
                    $_POST['numThem'] ?? $article['numThem'] :
                    Priorise la valeur POST (après erreur), sinon la valeur BDD
                -->
                <div class="col-md-6 mb-3">
                    <label for="numThem" class="form-label">Thématique</label>
                    <select class="form-select" id="numThem" name="numThem">
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($thematiques as $them): ?>
                            <option value="<?= $them['numThem'] ?>" <?= (($_POST['numThem'] ?? $article['numThem']) == $them['numThem']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($them['libThem']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- 
                    MOTS-CLÉS :
                    $_POST['motcles'] ?? $currentMotcles :
                    - Si POST (après erreur) → utilise les mots-clés cochés par l'utilisateur
                    - Sinon → utilise $currentMotcles (les associations actuelles en BDD)
                -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mots-clés</label>
                    <div class="border rounded p-3" style="max-height: 150px; overflow-y: auto;">
                        <?php foreach ($motcles as $mot): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="motcles[]" 
                                       value="<?= $mot['numMotCle'] ?>" id="mot<?= $mot['numMotCle'] ?>"
                                       <?= in_array($mot['numMotCle'], $_POST['motcles'] ?? $currentMotcles) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="mot<?= $mot['numMotCle'] ?>">
                                    <?= htmlspecialchars($mot['libMotCle']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Enregistrer
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
