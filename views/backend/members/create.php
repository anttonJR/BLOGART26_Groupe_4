<?php
$pageTitle = 'Nouveau membre';
require_once __DIR__ . '/../includes/header.php';
require_once ROOT . '/functions/auth.php';
requireAdmin();

global $DB;
$error = '';

// Charger les statuts
$stmtStat = $DB->query("SELECT * FROM STATUT ORDER BY numStat");
$statuts = $stmtStat->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenomMemb = trim($_POST['prenomMemb'] ?? '');
    $nomMemb = trim($_POST['nomMemb'] ?? '');
    $pseudoMemb = trim($_POST['pseudoMemb'] ?? '');
    $eMailMemb = trim($_POST['eMailMemb'] ?? '');
    $wordsMemb = $_POST['wordsMemb'] ?? '';
    $numStat = (int)($_POST['numStat'] ?? 3);
    
    if (empty($prenomMemb) || empty($nomMemb) || empty($pseudoMemb) || empty($eMailMemb) || empty($wordsMemb)) {
        $error = "Tous les champs sont requis";
    } else {
        // Vérifier si le pseudo existe
        $check = $DB->prepare("SELECT numMemb FROM MEMBRE WHERE pseudoMemb = ?");
        $check->execute([$pseudoMemb]);
        if ($check->fetch()) {
            $error = "Ce pseudo est déjà utilisé";
        } else {
            $hashedPassword = password_hash($wordsMemb, PASSWORD_DEFAULT);
            $stmt = $DB->prepare("INSERT INTO MEMBRE (prenomMemb, nomMemb, pseudoMemb, wordsMemb, eMailMemb, numStat) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$prenomMemb, $nomMemb, $pseudoMemb, $hashedPassword, $eMailMemb, $numStat]);
            $_SESSION['success'] = "Membre créé avec succès";
            header('Location: ' . ROOT_URL . '/views/backend/members/list.php');
            exit;
        }
    }
}
?>

<div class="page-header">
    <h1><i class="bi bi-plus-lg me-2"></i>Nouveau membre</h1>
    <a href="<?= ROOT_URL ?>/views/backend/members/list.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<div class="admin-card">
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="prenomMemb" class="form-label">Prénom *</label>
                    <input type="text" class="form-control" id="prenomMemb" name="prenomMemb" required 
                           value="<?= htmlspecialchars($_POST['prenomMemb'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nomMemb" class="form-label">Nom *</label>
                    <input type="text" class="form-control" id="nomMemb" name="nomMemb" required 
                           value="<?= htmlspecialchars($_POST['nomMemb'] ?? '') ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="pseudoMemb" class="form-label">Pseudo *</label>
                    <input type="text" class="form-control" id="pseudoMemb" name="pseudoMemb" required 
                           value="<?= htmlspecialchars($_POST['pseudoMemb'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="eMailMemb" class="form-label">Email *</label>
                    <input type="email" class="form-control" id="eMailMemb" name="eMailMemb" required 
                           value="<?= htmlspecialchars($_POST['eMailMemb'] ?? '') ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="wordsMemb" class="form-label">Mot de passe *</label>
                    <input type="password" class="form-control" id="wordsMemb" name="wordsMemb" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="numStat" class="form-label">Statut *</label>
                    <select class="form-select" id="numStat" name="numStat" required>
                        <?php foreach ($statuts as $stat): ?>
                            <option value="<?= $stat['numStat'] ?>" <?= (($_POST['numStat'] ?? 3) == $stat['numStat']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($stat['libStat']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Créer
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
