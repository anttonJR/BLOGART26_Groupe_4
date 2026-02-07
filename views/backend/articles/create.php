<?php
/**
 * ============================================================
 * CREATE.PHP - Formulaire de création d'un article (Backend Admin)
 * ============================================================
 * 
 * RÔLE : Affiche le formulaire de création ET traite la soumission.
 *        Ce fichier gère à la fois le GET (affichage du formulaire)
 *        et le POST (traitement de la soumission).
 * 
 * FONCTIONNEMENT :
 * 1. Charge les thématiques et mots-clés depuis la BDD pour peupler les champs
 * 2. Si le formulaire est soumis (POST) :
 *    - Récupère et nettoie les données (trim pour enlever espaces)
 *    - Valide que le titre n'est pas vide
 *    - INSERT dans la table ARTICLE avec la date de création auto (NOW())
 *    - Récupère le dernier ID inséré (lastInsertId) pour les mots-clés
 *    - INSERT dans la table de jointure MOTCLEARTICLE pour chaque mot-clé coché
 *    - Redirige vers list.php avec un message de succès en session
 * 
 * TABLES UTILISÉES :
 * - ARTICLE : insertion de l'article principal
 * - THEMATIQUE : lecture pour remplir le select
 * - MOTCLE : lecture pour remplir les checkboxes
 * - MOTCLEARTICLE : table de jointure N:N entre ARTICLE et MOTCLE
 * 
 * SÉCURITÉ :
 * - requireAdmin() : seuls les admins peuvent créer des articles
 * - htmlspecialchars() : échappement XSS dans les champs du formulaire
 * - ?? '' (null coalescing) : évite les erreurs si les champs POST n'existent pas
 * 
 * FLUX :
 *   list.php → create.php (GET: formulaire)
 *   create.php → create.php (POST: traitement)
 *   create.php → list.php (redirection après succès)
 * ============================================================
 */

/* --- Inclusion du header backend --- */
$pageTitle = 'Nouvel article';
require_once __DIR__ . '/../includes/header.php';

/* --- Contrôle d'accès admin --- */
require_once ROOT . '/functions/auth.php';
requireAdmin();

/* --- Connexion BDD --- */
global $DB;
$error = '';

/* 
 * --- Charger les thématiques ---
 * Nécessaire pour remplir le <select> dans le formulaire
 * ORDER BY libThem : tri alphabétique pour faciliter la recherche
 */
$stmtThem = $DB->query("SELECT * FROM THEMATIQUE ORDER BY libThem");
$thematiques = $stmtThem->fetchAll();

/* 
 * --- Charger les mots-clés ---
 * Nécessaire pour remplir la liste de checkboxes
 * Chaque mot-clé coché sera inséré dans MOTCLEARTICLE après la création
 */
$stmtMot = $DB->query("SELECT * FROM MOTCLE ORDER BY libMotCle");
$motcles = $stmtMot->fetchAll();

/* 
 * ============================================
 * TRAITEMENT DU FORMULAIRE (POST)
 * ============================================
 * On ne rentre ici que si le formulaire a été soumis.
 * En GET (premier affichage), on saute directement au HTML.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /* 
     * Récupération des données POST avec nettoyage :
     * - trim() : supprime les espaces en début/fin
     * - ?? '' : si le champ n'existe pas dans POST, valeur par défaut vide
     * - (int) : cast en entier pour numThem (sécurité : évite l'injection)
     */
    $libTitrArt = trim($_POST['libTitrArt'] ?? '');
    $libChapoArt = trim($_POST['libChapoArt'] ?? '');
    $libAccrochArt = trim($_POST['libAccrochArt'] ?? '');
    $parag1Art = trim($_POST['parag1Art'] ?? '');
    $numThem = (int)($_POST['numThem'] ?? 0);
    /* numMemb : ID du membre connecté, récupéré depuis la session */
    $numMemb = $_SESSION['user']['numMemb'] ?? 1;
    /* motcles : tableau des numMotCle cochés (checkboxes name="motcles[]") */
    $selectedMotcles = $_POST['motcles'] ?? [];
    
    /* --- Validation : le titre est le seul champ obligatoire --- */
    if (empty($libTitrArt)) {
        $error = "Le titre est requis";
    } else {
        /* 
         * --- Insertion de l'article dans la table ARTICLE ---
         * NOW() : la date de création est générée par MySQL au moment de l'insertion
         * numThem ?: null : si numThem vaut 0 (pas de sélection), on met NULL
         *                   car la FK vers THEMATIQUE n'est pas obligatoire
         */
        $stmt = $DB->prepare("INSERT INTO ARTICLE (libTitrArt, libChapoArt, libAccrochArt, parag1Art, dtCreaArt, numThem, numMemb) VALUES (?, ?, ?, ?, NOW(), ?, ?)");
        $stmt->execute([$libTitrArt, $libChapoArt, $libAccrochArt, $parag1Art, $numThem ?: null, $numMemb]);
        
        /* 
         * --- Récupérer l'ID auto-incrémenté de l'article créé ---
         * lastInsertId() retourne le dernier ID généré par AUTO_INCREMENT
         * On en a besoin pour créer les associations dans MOTCLEARTICLE
         */
        $numArt = $DB->lastInsertId();
        
        /* 
         * --- Associer les mots-clés sélectionnés ---
         * MOTCLEARTICLE est une table de jointure (N:N) entre ARTICLE et MOTCLE
         * Pour chaque mot-clé coché, on insère une ligne (numArt, numMotCle)
         */
        foreach ($selectedMotcles as $numMotCle) {
            $stmtMC = $DB->prepare("INSERT INTO MOTCLEARTICLE (numArt, numMotCle) VALUES (?, ?)");
            $stmtMC->execute([$numArt, $numMotCle]);
        }
        
        /* --- Flash message de succès et redirection vers la liste --- */
        $_SESSION['success'] = "Article créé avec succès";
        header('Location: ' . ROOT_URL . '/views/backend/articles/list.php');
        exit; // IMPORTANT : exit après header() pour arrêter l'exécution
    }
}
?>

<!-- ============================================ -->
<!-- EN-TÊTE + bouton retour                      -->
<!-- ============================================ -->
<div class="page-header">
    <h1><i class="bi bi-plus-lg me-2"></i>Nouvel article</h1>
    <a href="<?= ROOT_URL ?>/views/backend/articles/list.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

<!-- Affichage de l'erreur de validation si elle existe -->
<?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<!-- ============================================ -->
<!-- FORMULAIRE DE CRÉATION                        -->
<!-- method="POST" : les données sont envoyées dans le corps de la requête -->
<!-- Pas d'attribut action : le formulaire s'envoie à lui-même (create.php) -->
<!-- ============================================ -->
<div class="admin-card">
    <div class="card-body">
        <form method="POST">
            <!-- 
                TITRE (obligatoire) :
                - required : validation HTML5 côté navigateur
                - value="htmlspecialchars($_POST[...])" : si le formulaire échoue (erreur de validation),
                  on ré-affiche la valeur saisie pour ne pas perdre les données
                - ?? '' : si $_POST n'existe pas (premier affichage GET), valeur vide
            -->
            <div class="mb-3">
                <label for="libTitrArt" class="form-label">Titre *</label>
                <input type="text" class="form-control" id="libTitrArt" name="libTitrArt" required 
                       value="<?= htmlspecialchars($_POST['libTitrArt'] ?? '') ?>">
            </div>
            <!-- CHAPÔ : résumé court de l'article (facultatif) -->
            <div class="mb-3">
                <label for="libChapoArt" class="form-label">Chapô</label>
                <textarea class="form-control" id="libChapoArt" name="libChapoArt" rows="2"><?= htmlspecialchars($_POST['libChapoArt'] ?? '') ?></textarea>
            </div>
            <!-- ACCROCHE : phrase d'accroche de l'article (facultatif) -->
            <div class="mb-3">
                <label for="libAccrochArt" class="form-label">Accroche</label>
                <textarea class="form-control" id="libAccrochArt" name="libAccrochArt" rows="2"><?= htmlspecialchars($_POST['libAccrochArt'] ?? '') ?></textarea>
            </div>
            <!-- CONTENU : paragraphe principal de l'article (facultatif) -->
            <div class="mb-3">
                <label for="parag1Art" class="form-label">Contenu</label>
                <textarea class="form-control" id="parag1Art" name="parag1Art" rows="10"><?= htmlspecialchars($_POST['parag1Art'] ?? '') ?></textarea>
            </div>
            <div class="row">
                <!-- 
                    THÉMATIQUE (select) :
                    - Boucle sur les thématiques chargées depuis la BDD
                    - 'selected' si la valeur POST correspond (conservation après erreur)
                -->
                <div class="col-md-6 mb-3">
                    <label for="numThem" class="form-label">Thématique</label>
                    <select class="form-select" id="numThem" name="numThem">
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($thematiques as $them): ?>
                            <option value="<?= $them['numThem'] ?>" <?= (($_POST['numThem'] ?? '') == $them['numThem']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($them['libThem']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- 
                    MOTS-CLÉS (checkboxes) :
                    - name="motcles[]" : le [] crée un tableau PHP dans $_POST['motcles']
                    - in_array() : re-coche les mots-clés après une erreur de soumission
                    - Conteneur scrollable (max-height + overflow-y) si beaucoup de mots-clés
                -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mots-clés</label>
                    <div class="border rounded p-3" style="max-height: 150px; overflow-y: auto;">
                        <?php foreach ($motcles as $mot): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="motcles[]" 
                                       value="<?= $mot['numMotCle'] ?>" id="mot<?= $mot['numMotCle'] ?>"
                                       <?= in_array($mot['numMotCle'], $_POST['motcles'] ?? []) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="mot<?= $mot['numMotCle'] ?>">
                                    <?= htmlspecialchars($mot['libMotCle']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <!-- Bouton de soumission du formulaire -->
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Créer
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
