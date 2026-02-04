<?php 
require_once 'config.php';
require_once ROOT . '/functions/csrf.php';
require_once ROOT . '/functions/auth.php';

// Récupérer tous les articles avec leur nombre de likes et informations thématiques
$articles = sql_select(
    'ARTICLE a LEFT JOIN THEMATIQUE t ON a.numThem = t.numThem',
    'a.*, t.libThem, 
    (SELECT COUNT(*) FROM LIKEART l WHERE l.numArt = a.numArt AND l.likeA = 1) as nb_likes',
    '1=1',
    null,
    'a.dtCreaArt DESC',
    '6'
);

// Récupérer les statistiques globales
$stats_articles = sql_select('ARTICLE', 'COUNT(*) as total')[0]['total'] ?? 0;
$stats_comments = sql_select('COMMENT', 'COUNT(*) as total')[0]['total'] ?? 0;
$stats_membres = sql_select('MEMBRE', 'COUNT(*) as total')[0]['total'] ?? 0;
$stats_likes = sql_select('LIKEART', 'COUNT(*) as total', 'likeA = 1')[0]['total'] ?? 0;

// Récupérer toutes les thématiques avec comptage d'articles
$thematiques = sql_select(
    'THEMATIQUE t LEFT JOIN ARTICLE a ON t.numThem = a.numThem',
    't.numThem, t.libThem, COUNT(a.numArt) as nb_articles',
    null,
    't.numThem, t.libThem',
    't.libThem'
);

// Récupérer les mots-clés populaires (top 15)
$keywords = sql_select(
    'MOTCLE',
    'libMotCle',
    null,
    null,
    'libMotCle',
    '15'
);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Millésime - Blog du Domaine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="src/images/article1.png" />
    <style>
        :root {
            --beige-light: #f4f1ea;
            --beige-medium: #e8e3d6;
            --bordeaux: #800000;
            --black: #12120c;
            --gold: #8f7f5e;
        }

        * {
            transition: all 0.3s ease;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--beige-light);
            color: var(--black);
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" opacity="0.03"><text x="10" y="50" font-size="60" fill="%23000">🍇</text></svg>');
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 600;
        }

        html {
            scroll-behavior: smooth;
        }

        .navbar {
            background-color: rgba(232, 227, 214, 0.95);
            border-bottom: 3px solid var(--bordeaux);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
        }

        .navbar-brand {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--black) !important;
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.05);
        }

        .nav-link {
            color: var(--black) !important;
            font-weight: 500;
            position: relative;
            padding: 8px 16px !important;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background-color: var(--bordeaux);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 80%;
        }

        .nav-link:hover {
            color: var(--bordeaux) !important;
            transform: translateY(-2px);
        }

        .hero-section {
            background: linear-gradient(135deg, var(--beige-medium) 0%, var(--beige-light) 100%);
            padding: 100px 0;
            border-bottom: 1px solid var(--gold);
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(128,0,0,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        .hero-title {
            font-size: 4rem;
            color: var(--bordeaux);
            margin-bottom: 20px;
            font-weight: 700;
            animation: fadeInUp 0.8s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .badge-status {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
            margin: 5px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .badge-status::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .badge-status:hover::before {
            width: 300px;
            height: 300px;
        }

        .badge-admin {
            background-color: var(--bordeaux);
            color: white;
            box-shadow: 0 4px 15px rgba(128,0,0,0.3);
        }

        .badge-moderator {
            background-color: var(--black);
            color: white;
            box-shadow: 0 4px 15px rgba(18,18,12,0.3);
        }

        .badge-member {
            background-color: var(--gold);
            color: white;
            box-shadow: 0 4px 15px rgba(143,127,94,0.3);
        }

        .card {
            border: none;
            border-radius: 8px;
            background-color: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
            position: relative;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--bordeaux) 0%, var(--gold) 100%);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s ease;
        }

        .card:hover::before {
            transform: scaleX(1);
        }

        .card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 12px 35px rgba(128,0,0,0.2);
        }

        .article-card {
            margin-bottom: 30px;
            border-left: 4px solid var(--bordeaux);
        }

        .article-title {
            font-size: 1.8rem;
            color: var(--black);
            margin-bottom: 10px;
            transition: color 0.3s ease;
            cursor: pointer;
        }

        .article-title:hover {
            color: var(--bordeaux);
        }

        .article-meta {
            color: var(--gold);
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .article-meta i {
            transition: transform 0.3s ease;
        }

        .article-meta i:hover {
            transform: scale(1.2);
        }

        .tag {
            display: inline-block;
            background-color: var(--beige-medium);
            color: var(--black);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin: 3px;
            border: 1px solid var(--gold);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .tag::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: var(--bordeaux);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.5s, height 0.5s;
        }

        .tag:hover::before {
            width: 200px;
            height: 200px;
        }

        .tag:hover {
            color: white;
            border-color: var(--bordeaux);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(128,0,0,0.3);
        }

        .tag span {
            position: relative;
            z-index: 1;
        }

        .btn-primary {
            background-color: var(--bordeaux);
            border: none;
            border-radius: 4px;
            padding: 12px 35px;
            font-weight: 600;
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
            background: var(--black);
            transition: left 0.4s ease;
            z-index: -1;
        }

        .btn-primary:hover::before {
            left: 0;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(128,0,0,0.4);
        }

        .btn-outline {
            border: 2px solid var(--bordeaux);
            color: var(--bordeaux);
            background: transparent;
            border-radius: 4px;
            padding: 10px 30px;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .btn-outline::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background-color: var(--bordeaux);
            transition: width 0.4s ease;
            z-index: -1;
        }

        .btn-outline:hover::before {
            width: 100%;
        }

        .btn-outline:hover {
            color: white;
            transform: translateY(-2px);
        }

        .btn-like {
            position: relative;
            transition: all 0.3s ease;
        }

        .btn-like:active {
            transform: scale(0.9);
        }

        .btn-like.liked {
            color: var(--bordeaux) !important;
            border-color: var(--bordeaux) !important;
        }

        .btn-like.liked i {
            animation: heartBeat 0.6s ease;
        }

        @keyframes heartBeat {
            0%, 100% { transform: scale(1); }
            25% { transform: scale(1.3); }
            50% { transform: scale(1.1); }
            75% { transform: scale(1.2); }
        }

        .stats-box {
            text-align: center;
            padding: 40px 30px;
            background-color: white;
            border-top: 4px solid var(--bordeaux);
            border-radius: 8px;
            transition: all 0.4s ease;
            cursor: pointer;
        }

        .stats-box:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 15px 40px rgba(128,0,0,0.2);
            border-top-width: 6px;
        }

        .stats-number {
            font-size: 3rem;
            font-family: 'Cormorant Garamond', serif;
            color: var(--bordeaux);
            font-weight: 700;
            animation: countUp 1s ease-out;
        }

        @keyframes countUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .decorative-line {
            width: 60px;
            height: 3px;
            background-color: var(--bordeaux);
            margin: 20px auto;
            transition: width 0.4s ease;
        }

        .hero-section:hover .decorative-line {
            width: 120px;
        }

        .input-group input:focus,
        .form-select:focus {
            border-color: var(--bordeaux);
            box-shadow: 0 0 0 0.2rem rgba(128,0,0,0.25);
        }

        .footer {
            background-color: var(--black);
            color: white;
            padding: 60px 0 20px;
            margin-top: 80px;
        }

        .footer a {
            transition: all 0.3s ease;
        }

        .footer a:hover {
            color: var(--bordeaux) !important;
            transform: translateX(5px);
        }

        .icon-box {
            width: 60px;
            height: 60px;
            border: 2px solid var(--bordeaux);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: var(--bordeaux);
            transition: all 0.4s ease;
            border-radius: 50%;
        }

        .icon-box:hover {
            background-color: var(--bordeaux);
            color: white;
            transform: rotate(360deg) scale(1.1);
            box-shadow: 0 8px 20px rgba(128,0,0,0.3);
        }

        .scroll-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background-color: var(--bordeaux);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s ease;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(128,0,0,0.3);
        }

        .scroll-top.visible {
            opacity: 1;
            visibility: visible;
        }

        .scroll-top:hover {
            background-color: var(--black);
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(128,0,0,0.5);
        }

        .page-link {
            color: var(--bordeaux);
            border: 2px solid var(--beige-medium);
            margin: 0 5px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .page-link:hover {
            background-color: var(--bordeaux);
            color: white;
            border-color: var(--bordeaux);
            transform: translateY(-2px);
        }

        .page-item.active .page-link {
            background-color: var(--bordeaux);
            border-color: var(--bordeaux);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .stats-number {
                font-size: 2rem;
            }
            
            .article-title {
                font-size: 1.4rem;
            }
        }

        .like-count {
            color: var(--bordeaux);
            font-weight: 600;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <span style="border: 2px solid var(--black); padding: 5px 15px; margin-right: 10px;">BA</span>
                Millésime
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#articles">Articles</a></li>
                    <li class="nav-item"><a class="nav-link" href="#thematiques">Thématiques</a></li>
                    <li class="nav-item"><a class="nav-link" href="/BLOGART26/views/backend/dashboard.php"><i class="bi bi-pencil-square"></i> Admin</a></li>
                    <li class="nav-item"><a class="nav-link" href="/BLOGART26/views/frontend/security/login.php"><i class="bi bi-person-circle"></i> Connexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="hero-title">Millésime</h1>
            <div class="decorative-line"></div>
            <p class="lead" style="color: var(--gold); font-size: 1.2rem; font-family: 'Cormorant Garamond', serif;">
                L'art de partager notre passion viticole
            </p>
            <div class="mt-4">
                <span class="badge-status badge-admin">Administrateur</span>
                <span class="badge-status badge-moderator">Modérateur</span>
                <span class="badge-status badge-member">Membre</span>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="stats-box">
                        <div class="stats-number"><?= $stats_articles ?></div>
                        <p class="mb-0">Articles</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-box">
                        <div class="stats-number"><?= number_format($stats_comments) ?></div>
                        <p class="mb-0">Commentaires</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-box">
                        <div class="stats-number"><?= $stats_membres ?></div>
                        <p class="mb-0">Membres</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-box">
                        <div class="stats-number"><?= number_format($stats_likes) ?></div>
                        <p class="mb-0">Likes</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search and Filter Section -->
    <section class="py-4" style="background-color: var(--beige-medium);">
        <div class="container">
            <form action="/BLOGART26/views/frontend/articles/recherche.php" method="GET">
                <div class="row g-3">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text" style="background-color: white; border-radius: 0;">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="q" class="form-control" placeholder="Rechercher un article..." style="border-radius: 0;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="thematique" class="form-select" style="border-radius: 0;">
                            <option value="">Toutes les thématiques</option>
                            <?php foreach ($thematiques as $them): ?>
                                <option value="<?= $them['numThem'] ?>"><?= htmlspecialchars($them['libThem']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="order" class="form-select" style="border-radius: 0;">
                            <option value="recent">Plus récent</option>
                            <option value="ancien">Plus ancien</option>
                            <option value="populaire">Plus populaire</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">Go</button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Main Content -->
    <section id="articles" class="py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12">
                    <h2 class="mb-4" style="font-size: 2.5rem;">Articles récents</h2>
                </div>
            </div>

            <div class="row g-4">
                <?php if (empty($articles)): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Aucun article disponible pour le moment.
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($articles as $article): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="card article-card h-100">
                                <div class="card-body d-flex flex-column">
                                    <h3 class="article-title"><?= htmlspecialchars($article['libTitrArt']) ?></h3>
                                    <div class="article-meta">
                                        <i class="bi bi-calendar"></i> <?= date('d M Y', strtotime($article['dtCreaArt'])) ?>
                                        <?php if ($article['libThem']): ?>
                                            <span class="mx-2">•</span>
                                            <i class="bi bi-tag"></i> <?= htmlspecialchars($article['libThem']) ?>
                                        <?php endif; ?>
                                    </div>
                                    <p class="flex-grow-1"><?= htmlspecialchars(substr(strip_tags($article['libChapoArt']), 0, 150)) ?>...</p>
                                    <div class="mb-3">
                                        <?php if ($article['libThem']): ?>
                                            <span class="tag"><span><?= htmlspecialchars($article['libThem']) ?></span></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <div>
                                            <?php if (isLoggedIn()): ?>
                                                <form method="POST" action="<?= ROOT_URL ?>/api/likes/toggle.php" class="d-inline">
                                                    <?php csrfField(); ?>
                                                    <input type="hidden" name="numArt" value="<?= $article['numArt'] ?>">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                        <i class="bi bi-heart"></i> J'aime
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <a href="<?= ROOT_URL ?>/views/frontend/security/login.php" class="btn btn-outline-secondary btn-sm">
                                                    <i class="bi bi-heart"></i> J'aime
                                                </a>
                                            <?php endif; ?>
                                            <span class="ms-2"><strong><?= $article['nb_likes'] ?></strong> likes</span>
                                        </div>
                                        <a href="/BLOGART26/views/frontend/articles/article1.php?id=<?= $article['numArt'] ?>" class="btn btn-primary btn-sm">Lire</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <nav aria-label="Navigation des articles" class="mt-5">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </section>

    <!-- Thématiques Section -->
    <section id="thematiques" class="py-5" style="background-color: var(--beige-medium);">
        <div class="container">
            <h2 class="text-center mb-5" style="font-size: 2.5rem; color: var(--bordeaux);">Explorez par thématique</h2>
            <div class="row g-4">
                <?php 
                $icons = ['bi-sun', 'bi-droplet', 'bi-cup', 'bi-book', 'bi-newspaper', 'bi-calendar-event'];
                $iconIndex = 0;
                foreach ($thematiques as $them): 
                    $icon = $icons[$iconIndex % count($icons)];
                    $iconIndex++;
                ?>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <a href="/BLOGART26/views/frontend/articles/recherche.php?thematique=<?= $them['numThem'] ?>" class="text-decoration-none">
                            <div class="card text-center h-100" style="cursor: pointer;">
                                <div class="card-body">
                                    <div class="icon-box mx-auto mb-3">
                                        <i class="bi <?= $icon ?>"></i>
                                    </div>
                                    <h5><?= htmlspecialchars($them['libThem']) ?></h5>
                                    <p class="mb-0 text-muted"><?= $them['nb_articles'] ?> article<?= $them['nb_articles'] > 1 ? 's' : '' ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Tags populaires Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5" style="font-size: 2.5rem; color: var(--bordeaux);">Mots-clés populaires</h2>
            <div class="text-center">
                <?php 
                $sizes = ['1.1rem', '1rem', '1.2rem', '0.95rem', '1.15rem', '0.9rem', '1.05rem'];
                $sizeIndex = 0;
                foreach ($keywords as $kw): 
                    $size = $sizes[$sizeIndex % count($sizes)];
                    $sizeIndex++;
                ?>
                    <a href="/BLOGART26/views/frontend/articles/recherche.php?keyword=<?= urlencode($kw['libMotCle']) ?>" class="text-decoration-none">
                        <span class="tag" style="font-size: <?= $size ?>; padding: 8px 16px;">
                            <span><?= htmlspecialchars($kw['libMotCle']) ?></span>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="py-5" style="background-color: var(--beige-medium);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 style="font-size: 2.5rem; color: var(--bordeaux);">Restez informé</h2>
                    <p class="lead">Inscrivez-vous à notre newsletter pour recevoir nos derniers articles et actualités du domaine.</p>
                </div>
                <div class="col-md-6">
                    <form action="api/contact/send.php" method="POST">
                        <div class="input-group input-group-lg">
                            <input type="email" name="email" class="form-control" placeholder="Votre adresse email" style="border-radius: 0;" required>
                            <button class="btn btn-primary" type="submit">S'inscrire</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5 style="color: var(--bordeaux); font-family: 'Cormorant Garamond', serif;">Millésime - Blog'Art</h5>
                    <p class="text-light">Le blog de notre domaine viticole, où nous partageons notre passion et notre savoir-faire.</p>
                </div>
                <div class="col-md-4">
                    <h5 style="color: var(--bordeaux);">Navigation</h5>
                    <ul class="list-unstyled">
                        <li><a href="#articles" class="text-light text-decoration-none">Articles</a></li>
                        <li><a href="#thematiques" class="text-light text-decoration-none">Thématiques</a></li>
                        <li><a href="/BLOGART26/views/frontend/security/login.php" class="text-light text-decoration-none">Connexion</a></li>
                        <li><a href="/BLOGART26/views/backend/dashboard.php" class="text-light text-decoration-none">Administration</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 style="color: var(--bordeaux);">Informations</h5>
                    <ul class="list-unstyled">
                        <li><a href="/BLOGART26/views/frontend/contact.php" class="text-light text-decoration-none">Contact</a></li>
                        <li><a href="/BLOGART26/views/frontend/rgpd/cgu.php" class="text-light text-decoration-none">CGU</a></li>
                        <li><a href="/BLOGART26/views/frontend/rgpd/rgpd.php" class="text-light text-decoration-none">RGPD</a></li>
                    </ul>
                </div>
            </div>
            <hr style="border-color: var(--bordeaux); opacity: 0.3;" class="my-4">
            <div class="text-center text-light">
                <p>&copy; 2026 Millésime - Blog'Art. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to top button -->
    <div class="scroll-top">
        <i class="bi bi-arrow-up"></i>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Scroll to top button
        const scrollTopBtn = document.querySelector('.scroll-top');

        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollTopBtn.classList.add('visible');
            } else {
                scrollTopBtn.classList.remove('visible');
            }
        });

        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Like button functionality - now handled by form POST
        document.addEventListener('DOMContentLoaded', () => {
            // Smooth scroll for navigation links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');
                    if (href !== '#' && document.querySelector(href)) {
                        e.preventDefault();
                        document.querySelector(href).scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Animate stats on scroll
            const statsObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const statsNumber = entry.target.querySelector('.stats-number');
                        const finalNumber = parseInt(statsNumber.textContent.replace(/,/g, ''));
                        animateNumber(statsNumber, 0, finalNumber, 1500);
                        statsObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });

            document.querySelectorAll('.stats-box').forEach(box => {
                statsObserver.observe(box);
            });

            function animateNumber(element, start, end, duration) {
                const startTime = performance.now();
                
                function update(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    
                    const current = Math.floor(progress * (end - start) + start);
                    element.textContent = current.toLocaleString();
                    
                    if (progress < 1) {
                        requestAnimationFrame(update);
                    }
                }
                
                requestAnimationFrame(update);
            }

            // Navbar background on scroll
            const navbar = document.querySelector('.navbar');
            window.addEventListener('scroll', () => {
                if (window.pageYOffset > 50) {
                    navbar.style.backgroundColor = 'rgba(232, 227, 214, 0.98)';
                    navbar.style.boxShadow = '0 4px 30px rgba(0,0,0,0.15)';
                } else {
                    navbar.style.backgroundColor = 'rgba(232, 227, 214, 0.95)';
                    navbar.style.boxShadow = '0 2px 20px rgba(0,0,0,0.1)';
                }
            });

            // Fade-in animation on scroll
            const observerOptions = {
                root: null,
                threshold: 0.1
            };

            const fadeObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.card, .stats-box').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(30px)';
                el.style.transition = 'all 0.6s ease';
                fadeObserver.observe(el);
            });
        });
    </script>
</body>
</html>