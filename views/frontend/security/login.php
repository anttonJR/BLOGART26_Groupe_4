<?php
session_start();
include '../includes/cookie-consent.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Millésime Blog'Art</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
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
            overflow: hidden;
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

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
        }

        .login-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 15px 60px rgba(0,0,0,0.15);
            padding: 50px;
            position: relative;
            overflow: hidden;
            border-top: 4px solid var(--bordeaux);
        }

        .login-card::before {
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
            align-items: center;
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

        @media (max-width: 576px) {
            .login-card {
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
    <div class="login-container">
        <div class="login-card">
            <div class="logo-section">
                <div class="logo-icon">
                    <i class="bi bi-person-circle"></i>
                </div>
                <h1 class="logo-title">Connexion</h1>
                <p class="logo-subtitle">Millésime Blog'Art</p>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    <span><?= htmlspecialchars($_SESSION['success']) ?></span>
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

            <form method="POST" action="/BLOGART26/api/security/login.php">
                <div class="mb-4">
                    <label class="form-label">
                        <i class="bi bi-person"></i>
                        Pseudo
                    </label>
                    <input type="text" 
                           name="pseudoMemb" 
                           class="form-control" 
                           placeholder="Entrez votre pseudo"
                           required 
                           minlength="6" 
                           maxlength="70"
                           autofocus>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">
                        <i class="bi bi-lock"></i>
                        Mot de passe
                    </label>
                    <input type="password" 
                           name="passMemb" 
                           class="form-control" 
                           placeholder="Entrez votre mot de passe"
                           required 
                           minlength="8" 
                           maxlength="15">
                </div>
                
                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    Se connecter
                </button>

                <div class="divider">
                    <span>OU</span>
                </div>

                <div class="text-center">
                    <a href="signup.php" class="btn-link">
                        <i class="bi bi-person-plus"></i>
                        Pas encore inscrit ? Créer un compte
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
</body>
</html>
