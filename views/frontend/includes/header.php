<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once dirname(__DIR__, 3) . '/config.php';
require_once ROOT . '/functions/auth.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'BlogArt' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= ROOT_URL ?>/src/css/style.css" rel="stylesheet">
    <style>
        :root {
            --beige-light: #f4f1ea;
            --beige: #e8e0d0;
            --bordeaux: #800000;
            --bordeaux-dark: #5c0000;
            --gold: #8f7f5e;
            --black: #12120c;
        }
        body { background-color: var(--beige-light); font-family: 'Montserrat', sans-serif; }
        .navbar-brand { font-family: 'Cormorant Garamond', serif; font-weight: 700; }
        .bg-bordeaux { background-color: var(--bordeaux) !important; }
        .text-bordeaux { color: var(--bordeaux) !important; }
        .btn-bordeaux { background-color: var(--bordeaux); color: white; border: none; }
        .btn-bordeaux:hover { background-color: var(--bordeaux-dark); color: white; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-bordeaux">
        <div class="container">
            <a class="navbar-brand fs-3" href="<?= ROOT_URL ?>/index.php">
                <i class="bi bi-brush me-2"></i>BlogArt
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= ROOT_URL ?>/index.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= ROOT_URL ?>/views/frontend/articles/recherche.php">Articles</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= ROOT_URL ?>/views/frontend/contact.php">Contact</a>
                    </li>
                    
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['user']['pseudoMemb'] ?? 'Utilisateur') ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if (isAdmin()): ?>
                                    <li><a class="dropdown-item" href="<?= ROOT_URL ?>/views/backend/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php elseif (isModerator()): ?>
                                    <li><a class="dropdown-item" href="<?= ROOT_URL ?>/views/backend/moderation/comments.php"><i class="bi bi-shield-check me-2"></i>Modération</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item text-danger" href="<?= ROOT_URL ?>/api/security/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Déconnexion</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= ROOT_URL ?>/views/frontend/security/login.php">Connexion</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-light text-bordeaux ms-2" href="<?= ROOT_URL ?>/views/frontend/security/signup.php">Inscription</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <main class="py-4">
        <div class="container">
