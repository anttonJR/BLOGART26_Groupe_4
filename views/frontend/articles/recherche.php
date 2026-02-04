<?php
include '../includes/cookie-consent.php';
require_once '../../../config.php';

// Récupération des paramètres de recherche
$searchQuery = $_GET['q'] ?? '';
$thematique = $_GET['thematique'] ?? '';
$keyword = $_GET['keyword'] ?? '';
$order = $_GET['order'] ?? 'recent';

// Construction de la requête SQL
$where = '1=1';
$params = [];

if ($searchQuery) {
    $where .= " AND (a.libTitrArt LIKE ? OR a.libChapoArt LIKE ?)";
    $params[] = '%' . $searchQuery . '%';
    $params[] = '%' . $searchQuery . '%';
}

if ($thematique) {
    $where .= " AND a.numThem = ?";
    $params[] = $thematique;
}

// Order by
$orderBy = match($order) {
    'ancien' => 'a.dtCreaArt ASC',
    'populaire' => 'nb_likes DESC',
    default => 'a.dtCreaArt DESC'
};

// Récupération des articles
if ($keyword) {
    // Recherche par mot-clé
    $sql = "SELECT a.*, t.libThem,
            (SELECT COUNT(*) FROM LIKEART l WHERE l.numArt = a.numArt AND l.likeA = 1) as nb_likes
            FROM ARTICLE a
            LEFT JOIN THEMATIQUE t ON a.numThem = t.numThem
            INNER JOIN MOTCLEARTICLE mca ON mca.numArt = a.numArt
            INNER JOIN MOTCLE mc ON mc.numMotCle = mca.numMotCle AND mc.libMotCle = ?
            WHERE " . $where . "
            ORDER BY " . $orderBy;
    array_unshift($params, $keyword);
} else {
    $sql = "SELECT a.*, t.libThem,
            (SELECT COUNT(*) FROM LIKEART l WHERE l.numArt = a.numArt AND l.likeA = 1) as nb_likes
            FROM ARTICLE a
            LEFT JOIN THEMATIQUE t ON a.numThem = t.numThem
            WHERE " . $where . "
            ORDER BY " . $orderBy;
}

global $DB;
$stmt = $DB->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll();

// Titre de la page
$pageTitle = 'Recherche';
if ($searchQuery) $pageTitle .= ' : "' . htmlspecialchars($searchQuery) . '"';
if ($keyword) $pageTitle = 'Mot-clé : ' . htmlspecialchars($keyword);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Millésime</title>
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

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--beige-light);
            color: var(--black);
        }

        h1, h2, h3 {
            font-family: 'Cormorant Garamond', serif;
            font-weight: 600;
        }

        .navbar {
            background-color: rgba(232, 227, 214, 0.95);
            border-bottom: 3px solid var(--bordeaux);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--black) !important;
        }

        .nav-link {
            color: var(--black) !important;
            font-weight: 500;
        }

        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-left: 4px solid var(--bordeaux);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(128,0,0,0.15);
        }

        .btn-primary {
            background-color: var(--bordeaux);
            border: none;
        }

        .btn-primary:hover {
            background-color: var(--black);
        }

        .page-header {
            background: linear-gradient(135deg, var(--beige-medium) 0%, var(--beige-light) 100%);
            padding: 60px 0;
            margin-bottom: 40px;
            border-bottom: 1px solid var(--gold);
        }

        .badge-theme {
            background-color: var(--bordeaux);
            padding: 8px 15px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="../../../index.php">
                <span style="border: 2px solid var(--black); padding: 5px 15px; margin-right: 10px;">BA</span>
                Millésime
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="../../../index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="../../../index.php#articles">Articles</a></li>
                    <li class="nav-item"><a class="nav-link" href="../../../index.php#thematiques">Thématiques</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 style="color: var(--bordeaux); font-size: 2.5rem;">
                <i class="bi bi-search"></i> <?= $pageTitle ?>
            </h1>
            <p class="lead mb-0"><?= count($articles) ?> article(s) trouvé(s)</p>
        </div>
    </div>

    <!-- Articles -->
    <div class="container mb-5">
        <?php if (empty($articles)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Aucun article ne correspond à votre recherche.
            </div>
            <a href="../../../index.php" class="btn btn-primary">
                <i class="bi bi-arrow-left"></i> Retour à l'accueil
            </a>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($articles as $article): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100">
                            <?php if (!empty($article['urlPhotArt'])): ?>
                                <img src="../../../src/uploads/<?= htmlspecialchars($article['urlPhotArt']) ?>" 
                                     class="card-img-top" 
                                     alt="<?= htmlspecialchars($article['libTitrArt'] ?? '') ?>"
                                     style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <?php if ($article['libThem']): ?>
                                    <span class="badge badge-theme mb-2"><?= htmlspecialchars($article['libThem']) ?></span>
                                <?php endif; ?>
                                <h3 class="h5"><?= htmlspecialchars($article['libTitrArt']) ?></h3>
                                <p class="text-muted small">
                                    <i class="bi bi-calendar"></i> <?= date('d M Y', strtotime($article['dtCreaArt'])) ?>
                                </p>
                                <p class="flex-grow-1"><?= htmlspecialchars(substr(strip_tags($article['libChapoArt'] ?? ''), 0, 120)) ?>...</p>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <span class="text-muted">
                                        <i class="bi bi-heart"></i> <?= $article['nb_likes'] ?> likes
                                    </span>
                                    <a href="article1.php?id=<?= $article['numArt'] ?>" class="btn btn-primary btn-sm">Lire</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-5 text-center">
                <a href="../../../index.php" class="btn btn-outline-dark">
                    <i class="bi bi-arrow-left"></i> Retour à l'accueil
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>