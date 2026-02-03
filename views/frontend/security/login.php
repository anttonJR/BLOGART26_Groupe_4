<?php
session_start();
include 'includes/cookie-consent.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Connexion - BlogArt</title>
    <link rel="stylesheet" href="../../../assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <h1>Connexion</h1>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?= $_SESSION['success'] ?>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION['error'] ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <form method="POST" action="../../api/security/login.php">
                    <!-- Pseudo -->
                    <div class="mb-3">
                        <label class="form-label">Pseudo</label>
                        <input type="text" 
                               name="pseudoMemb" 
                               class="form-control" 
                               required 
                               minlength="6" 
                               maxlength="70">
                    </div>
                    
                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label">Mot de passe</label>
                        <input type="password" 
                               name="passMemb" 
                               class="form-control" 
                               required 
                               minlength="8" 
                               maxlength="15">
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                    
                    <a href="signup.php" class="btn btn-link w-100">Pas encore inscrit ? S'inscrire</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>