<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test reCAPTCHA v3</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js?render=<?= getenv('RECAPTCHA_SITE_KEY') ?>"></script>
    <style>
        body { background: #f5f5f5; }
        .container { margin-top: 50px; }
        .card { box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .result { margin-top: 30px; }
        .score-high { color: green; }
        .score-medium { color: orange; }
        .score-low { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">🔐 Test reCAPTCHA v3</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Info:</strong> Cette page teste le fonctionnement du captcha reCAPTCHA v3.
                            Le captcha est invisible et s'exécute automatiquement.
                        </div>

                        <form id="testForm" method="POST" action="api/test-recaptcha-verify.php">
                            <div class="mb-3">
                                <label class="form-label">Nom de test :</label>
                                <input type="text" name="testName" class="form-control" placeholder="Entrez un nom" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email :</label>
                                <input type="email" name="testEmail" class="form-control" placeholder="test@example.com" required>
                            </div>

                            <!-- Hidden field pour le token reCAPTCHA -->
                            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                Soumettre le test
                            </button>
                        </form>

                        <div class="result">
                            <h5>📋 Résultat du dernier test :</h5>
                            <?php if (isset($_SESSION['recaptcha_result'])): ?>
                                <div class="alert alert-success">
                                    <p><strong>Status :</strong> 
                                        <?php if ($_SESSION['recaptcha_result']['success']): ?>
                                            <span class="badge bg-success">✅ SUCCÈS</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">❌ ÉCHEC</span>
                                        <?php endif; ?>
                                    </p>
                                    
                                    <p><strong>Score :</strong> 
                                        <span class="<?= $_SESSION['recaptcha_result']['score'] >= 0.7 ? 'score-high' : ($_SESSION['recaptcha_result']['score'] >= 0.5 ? 'score-medium' : 'score-low') ?>">
                                            <?= $_SESSION['recaptcha_result']['score'] ?> 
                                            <?php if ($_SESSION['recaptcha_result']['score'] >= 0.7): ?>
                                                (Humain ✓)
                                            <?php elseif ($_SESSION['recaptcha_result']['score'] >= 0.5): ?>
                                                (Borderline ⚠️)
                                            <?php else: ?>
                                                (Bot ✗)
                                            <?php endif; ?>
                                        </span>
                                    </p>

                                    <p><strong>Action :</strong> <?= $_SESSION['recaptcha_result']['action'] ?></p>
                                    <p><strong>Challenge Timestamp :</strong> <?= $_SESSION['recaptcha_result']['challenge_ts'] ?></p>
                                    <p><strong>Hostname :</strong> <?= $_SESSION['recaptcha_result']['hostname'] ?></p>

                                    <?php if (!empty($_SESSION['recaptcha_result']['error-codes'])): ?>
                                        <p><strong>Erreurs :</strong> 
                                            <span class="badge bg-danger"><?= implode(', ', $_SESSION['recaptcha_result']['error-codes']) ?></span>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <?php unset($_SESSION['recaptcha_result']); ?>
                            <?php else: ?>
                                <p class="text-muted">Soumettez le formulaire pour voir le résultat du test.</p>
                            <?php endif; ?>
                        </div>

                        <div class="mt-4 pt-4 border-top">
                            <h5>🔍 Informations de configuration :</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Clé site chargée :</strong></td>
                                    <td>
                                        <?php if (getenv('RECAPTCHA_SITE_KEY')): ?>
                                            <span class="badge bg-success">✅ OUI</span> 
                                            <code><?= substr(getenv('RECAPTCHA_SITE_KEY'), 0, 10) ?>...</code>
                                        <?php else: ?>
                                            <span class="badge bg-danger">❌ NON</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Clé secrète chargée :</strong></td>
                                    <td>
                                        <?php if (getenv('RECAPTCHA_SECRET_KEY')): ?>
                                            <span class="badge bg-success">✅ OUI</span>
                                            <code><?= substr(getenv('RECAPTCHA_SECRET_KEY'), 0, 10) ?>...</code>
                                        <?php else: ?>
                                            <span class="badge bg-danger">❌ NON</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Seuil de score :</strong></td>
                                    <td><?= getenv('RECAPTCHA_SCORE_THRESHOLD') ?: '0.5 (défaut)' ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Mode DEBUG :</strong></td>
                                    <td>
                                        <?php if (getenv('APP_DEBUG') == 'true'): ?>
                                            <span class="badge bg-warning">⚠️ ACTIVÉ</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">DÉSACTIVÉ</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="mt-3">
                            <a href="/BLOGART26/index.php" class="btn btn-secondary">Retour à l'accueil</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        console.log('🧪 Page de test reCAPTCHA v3 chargée');
        console.log('🔐 Clé site:', '<?= getenv('RECAPTCHA_SITE_KEY') ? 'OK' : 'MANQUANTE' ?>');

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('testForm');
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('📤 Soumission du formulaire de test...');

                grecaptcha.ready(function() {
                    grecaptcha.execute('<?= getenv('RECAPTCHA_SITE_KEY') ?>', {action: 'test'}).then(function(token) {
                        console.log('✅ Token généré:', token.substring(0, 20) + '...');
                        document.getElementById('g-recaptcha-response').value = token;
                        form.submit();
                    });
                });
            });
        });
    </script>
</body>
</html>
