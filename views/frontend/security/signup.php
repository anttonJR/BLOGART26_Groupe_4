<?php
session_start();
require_once '../../../config.php';
require_once '../../../functions/csrf.php';
include '../includes/cookie-consent.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Millésime Blog'Art</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js?render=<?= getenv('RECAPTCHA_SITE_KEY') ?>"></script>
    <style>
        :root {
            --beige-light: #f4f1ea;
            --beige-medium: #e8e3d6;
            --bordeaux: #800000;
            --black: #12120c;
            --gold: #8f7f5e;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, var(--beige-light) 0%, var(--beige-medium) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(128,0,0,0.1) 0%, transparent 70%);
            animation: float 8s ease-in-out infinite;
        }

        body::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -15%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(143,127,94,0.15) 0%, transparent 70%);
            animation: float 10s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(-30px, -30px) rotate(5deg); }
        }

        .signup-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 550px;
            margin: 40px 0;
        }

        .signup-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 15px 60px rgba(0,0,0,0.15);
            padding: 50px;
            position: relative;
            overflow: hidden;
            border-top: 4px solid var(--bordeaux);
        }

        .signup-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--bordeaux) 0%, var(--gold) 100%);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--bordeaux) 0%, var(--black) 100%);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin-bottom: 20px;
            box-shadow: 0 8px 25px rgba(128,0,0,0.3);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .logo-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--black);
            margin-bottom: 5px;
        }

        .logo-subtitle {
            color: var(--gold);
            font-size: 0.9rem;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .form-label {
            font-weight: 500;
            color: var(--black);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label i {
            color: var(--bordeaux);
        }

        .form-control {
            border: 2px solid var(--beige-medium);
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--bordeaux);
            box-shadow: 0 0 0 0.25rem rgba(128,0,0,0.15);
        }

        .form-text {
            color: var(--gold);
            font-size: 0.8rem;
            margin-top: 5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--bordeaux) 0%, var(--black) 100%);
            border: none;
            border-radius: 8px;
            padding: 14px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
            color: white;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--black) 0%, var(--gold) 100%);
            transition: left 0.4s ease;
            z-index: -1;
        }

        .btn-primary:hover::before {
            left: 0;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(128,0,0,0.4);
            color: white;
        }

        .btn-link {
            color: var(--gold);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-link:hover {
            color: var(--bordeaux);
            transform: translateX(5px);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 30px 0;
            color: var(--gold);
            font-size: 0.9rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--beige-medium);
        }

        .divider span {
            padding: 0 15px;
        }

        .alert {
            border-radius: 8px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            animation: slideDown 0.5s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert i {
            font-size: 1.2rem;
            margin-top: 2px;
        }

        .back-link {
            text-align: center;
            margin-top: 25px;
        }

        .back-link a {
            color: var(--gold);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-link a:hover {
            color: var(--bordeaux);
        }

        .form-check {
            margin-bottom: 10px;
        }

        .form-check-input:checked {
            background-color: var(--bordeaux);
            border-color: var(--bordeaux);
        }

        .form-check-label {
            font-size: 0.9rem;
        }

        .rgpd-section {
            background: var(--beige-light);
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid var(--gold);
        }

        .rgpd-section h6 {
            color: var(--bordeaux);
            font-family: 'Cormorant Garamond', serif;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .password-requirements {
            background: var(--beige-light);
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            font-size: 0.85rem;
        }

        .password-requirements ul {
            margin: 0;
            padding-left: 20px;
        }

        .password-requirements li {
            color: var(--gold);
            margin-bottom: 5px;
        }

        @media (max-width: 576px) {
            .signup-card {
                padding: 30px 25px;
            }

            .logo-title {
                font-size: 1.6rem;
            }

            .logo-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="signup-card">
            <div class="logo-section">
                <div class="logo-icon">
                    <i class="bi bi-person-plus"></i>
                </div>
                <h1 class="logo-title">Inscription</h1>
                <p class="logo-subtitle">Rejoignez Millésime Blog'Art</p>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>
                        <strong>Félicitations !</strong><br>
                        <?= htmlspecialchars($_SESSION['success']) ?>
                    </div>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span><?= htmlspecialchars($_SESSION['error']) ?></span>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['errors'])): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <p class="mb-1"><?= htmlspecialchars($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>

            <form method="POST" action="/BLOGART26/api/members/create.php" id="form-recaptcha">
                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                
                <!-- Pseudo -->
                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-at"></i>
                        Pseudo
                    </label>
                    <input type="text" 
                           name="pseudoMemb" 
                           class="form-control" 
                           placeholder="Choisissez un pseudo unique"
                           required 
                           minlength="6" 
                           maxlength="70"
                           pattern="[a-zA-Z0-9_-]{6,70}"
                           title="6 à 70 caractères (lettres, chiffres, _ et -)">
                    <small class="form-text">6 à 70 caractères (lettres, chiffres, _ et -)</small>
                </div>

                <div class="row">
                    <!-- Prénom -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="bi bi-person"></i>
                            Prénom
                        </label>
                        <input type="text" 
                               name="prenomMemb" 
                               class="form-control" 
                               placeholder="Votre prénom"
                               required 
                               maxlength="70">
                    </div>
                    
                    <!-- Nom -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="bi bi-person-badge"></i>
                            Nom
                        </label>
                        <input type="text" 
                               name="nomMemb" 
                               class="form-control" 
                               placeholder="Votre nom"
                               required 
                               maxlength="70">
                    </div>
                </div>
                
                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-envelope"></i>
                        Email
                    </label>
                    <input type="email" 
                           name="eMailMemb" 
                           class="form-control" 
                           placeholder="votre@email.com"
                           required 
                           maxlength="100">
                </div>
                
                <!-- Confirmation Email -->
                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-envelope-check"></i>
                        Confirmer l'email
                    </label>
                    <input type="email" 
                           name="eMailMemb_confirm" 
                           class="form-control" 
                           placeholder="Confirmez votre email"
                           required 
                           maxlength="100">
                </div>
                
                <!-- Password -->
                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-lock"></i>
                        Mot de passe
                    </label>
                    <input type="password" 
                           name="passMemb" 
                           class="form-control" 
                           placeholder="Créez un mot de passe sécurisé"
                           required 
                           pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,15}$"
                           title="8-15 caractères, 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial">
                    <div class="password-requirements">
                        <strong>Le mot de passe doit contenir :</strong>
                        <ul>
                            <li>8 à 15 caractères</li>
                            <li>Au moins 1 majuscule</li>
                            <li>Au moins 1 minuscule</li>
                            <li>Au moins 1 chiffre</li>
                            <li>Au moins 1 caractère spécial (@$!%*?&)</li>
                        </ul>
                    </div>
                </div>
                
                <!-- Confirmation Password -->
                <div class="mb-4">
                    <label class="form-label">
                        <i class="bi bi-lock-fill"></i>
                        Confirmer le mot de passe
                    </label>
                    <input type="password" 
                           name="passMemb_confirm" 
                           class="form-control" 
                           placeholder="Confirmez votre mot de passe"
                           required>
                </div>
                
                <!-- RGPD -->
                <div class="rgpd-section">
                    <h6><i class="bi bi-shield-check me-2"></i>Protection des données (RGPD)</h6>
                    
                    <div class="form-check">
                        <input type="radio" 
                               name="accordMemb" 
                               value="1" 
                               id="rgpd_oui" 
                               class="form-check-input" 
                               required>
                        <label class="form-check-label" for="rgpd_oui">
                            <strong>J'accepte</strong> le stockage et le traitement de mes données personnelles
                        </label>
                    </div>
                    
                    <div class="form-check">
                        <input type="radio" 
                               name="accordMemb" 
                               value="0" 
                               id="rgpd_non" 
                               class="form-check-input">
                        <label class="form-check-label" for="rgpd_non">
                            Je refuse
                        </label>
                    </div>
                    
                    <small class="form-text d-block mt-2">
                        <i class="bi bi-info-circle me-1"></i>
                        En acceptant, vous consentez au traitement de vos données conformément au RGPD.
                        <a href="/BLOGART26/views/frontend/rgpd/rgpd.php" class="text-decoration-underline">En savoir plus</a>
                    </small>
                </div>
                
                <!-- reCAPTCHA v3 (invisible) -->
                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                
                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="bi bi-person-plus me-2"></i>
                    Créer mon compte
                </button>

                <div class="divider">
                    <span>OU</span>
                </div>

                <div class="text-center">
                    <a href="login.php" class="btn-link">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Déjà inscrit ? Se connecter
                    </a>
                </div>
            </form>

            <div class="back-link">
                <a href="/BLOGART26/index.php">
                    <i class="bi bi-arrow-left"></i>
                    Retour à l'accueil
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // reCAPTCHA v3 - Le script est à la fin, pas besoin d'attendre DOMContentLoaded
        const form = document.querySelector('form');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (typeof grecaptcha === 'undefined') {
                    console.error('❌ grecaptcha non chargé');
                    return;
                }
                
                grecaptcha.ready(function() {
                    grecaptcha.execute('<?= getenv('RECAPTCHA_SITE_KEY') ?>', {action: 'signup'}).then(function(token) {
                        document.getElementById('g-recaptcha-response').value = token;
                        form.submit();
                    });
                });
            });
        }
    </script>
</body>
</html>
