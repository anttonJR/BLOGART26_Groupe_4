<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once dirname(__DIR__, 3) . '/config.php';
require_once ROOT . '/functions/auth.php';

// Vérifier les droits admin ou modérateur
if (!isLoggedIn()) {
    header('Location: ' . ROOT_URL . '/views/frontend/security/login.php');
    exit;
}

// Informations utilisateur
$userName = $_SESSION['user']['prenomMemb'] ?? 'Admin';
$userRole = isAdmin() ? 'Administrateur' : (isModerator() ? 'Modérateur' : 'Membre');

// Récupérer le nombre de commentaires en attente pour le badge
global $DB;
$stmtPending = $DB->query("SELECT COUNT(*) as pending FROM COMMENT WHERE attModOK = 0 OR attModOK IS NULL");
$pendingCount = $stmtPending->fetch()['pending'] ?? 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Administration' ?> - BlogArt Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= ROOT_URL ?>/src/css/admin.css" rel="stylesheet">
</head>
<body class="admin-body">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="<?= ROOT_URL ?>/views/backend/dashboard.php" class="sidebar-brand">
                <i class="bi bi-brush"></i>
                <span>BlogArt</span>
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-section">
                <a href="<?= ROOT_URL ?>/views/backend/dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title"><i class="bi bi-folder"></i> Contenu</div>
                <a href="<?= ROOT_URL ?>/views/backend/articles/list.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/articles/') !== false ? 'active' : '' ?>">
                    <i class="bi bi-file-earmark-text"></i><span>Articles</span>
                </a>
                <a href="<?= ROOT_URL ?>/views/backend/thematiques/list.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/thematiques/') !== false ? 'active' : '' ?>">
                    <i class="bi bi-tags"></i><span>Thématiques</span>
                </a>
                <a href="<?= ROOT_URL ?>/views/backend/keywords/list.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/keywords/') !== false ? 'active' : '' ?>">
                    <i class="bi bi-hash"></i><span>Mots-clés</span>
                </a>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title"><i class="bi bi-chat-dots"></i> Interactions</div>
                <a href="<?= ROOT_URL ?>/views/backend/comments/list.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/comments/') !== false && strpos($_SERVER['PHP_SELF'], '/moderation/') === false ? 'active' : '' ?>">
                    <i class="bi bi-chat-left-text"></i><span>Commentaires</span>
                </a>
                <a href="<?= ROOT_URL ?>/views/backend/moderation/comments.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/moderation/') !== false ? 'active' : '' ?>">
                    <i class="bi bi-shield-check"></i><span>Modération</span>
                    <?php if ($pendingCount > 0): ?><span class="badge bg-warning text-dark ms-auto"><?= $pendingCount ?></span><?php endif; ?>
                </a>
                <a href="<?= ROOT_URL ?>/views/backend/likes/list.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/likes/') !== false ? 'active' : '' ?>">
                    <i class="bi bi-heart"></i><span>Likes</span>
                </a>
            </div>
            
            <?php if (isAdmin()): ?>
            <div class="nav-section">
                <div class="nav-section-title"><i class="bi bi-gear"></i> Administration</div>
                <a href="<?= ROOT_URL ?>/views/backend/members/list.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/members/') !== false ? 'active' : '' ?>">
                    <i class="bi bi-people"></i><span>Membres</span>
                </a>
                <a href="<?= ROOT_URL ?>/views/backend/statuts/list.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/statuts/') !== false ? 'active' : '' ?>">
                    <i class="bi bi-person-badge"></i><span>Statuts</span>
                </a>
            </div>
            <?php endif; ?>
        </nav>
        
        <div class="sidebar-footer">
            <a href="<?= ROOT_URL ?>/index.php" class="nav-link">
                <i class="bi bi-box-arrow-left"></i><span>Retour au site</span>
            </a>
        </div>
    </div>
    
    <!-- Main Content Wrapper -->
    <div class="main-wrapper">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <button class="btn btn-link sidebar-toggle d-lg-none" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            
            <div class="navbar-actions ms-auto">
                <?php if ($pendingCount > 0): ?>
                <div class="dropdown me-3">
                    <button class="btn btn-link position-relative" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $pendingCount ?></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">Notifications</h6>
                        <a class="dropdown-item" href="<?= ROOT_URL ?>/views/backend/moderation/comments.php">
                            <i class="bi bi-chat-dots text-warning me-2"></i><?= $pendingCount ?> commentaire(s) en attente
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="dropdown">
                    <button class="btn btn-link user-dropdown" data-bs-toggle="dropdown">
                        <div class="user-avatar"><i class="bi bi-person-circle"></i></div>
                        <div class="user-info d-none d-md-block">
                            <span class="user-name"><?= htmlspecialchars($userName) ?></span>
                            <small class="user-role"><?= $userRole ?></small>
                        </div>
                        <i class="bi bi-chevron-down ms-1"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header"><?= htmlspecialchars($userName) ?></h6></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= ROOT_URL ?>/api/security/logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                        </a></li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <!-- Main Content -->
        <main class="main-content">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?= $_SESSION['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?= $_SESSION['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
