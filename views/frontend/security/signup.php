<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Inscription - BlogArt</title>
    <link rel="stylesheet" href="../../../assets/css/bootstrap.min.css">
    <script src="https://www.google.com/recaptcha/api.js"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h1>Inscription</h1>
                
                <?php if (isset($_SESSION['errors'])): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <p><?= $error ?></p>
                        <?php endforeach; ?>
                    </div>
                    <?php unset($_SESSION['errors']); ?>
                <?php endif; ?>
                
                <form method="POST" action="../../api/members/create.php" id="form-recaptcha">
                    <!-- Pseudo -->
                    <div class="mb-3">
                        <label class="form-label">Pseudo *</label>
                        <input type="text" 
                               name="pseudoMemb" 
                               class="form-control" 
                               required 
                               minlength="6" 
                               maxlength="70"
                               pattern="^[a-zA-Z0-9_-]{6,70}$"
                               title="6 à 70 caractères (lettres, chiffres, _ et -)">
                        <small class="form-text text-muted">
                            Minimum 6 caractères, maximum 70
                        </small>
                    </div>
                    
                    <!-- Prénom -->
                    <div class="mb-3">
                        <label class="form-label">Prénom *</label>
                        <input type="text" 
                               name="prenomMemb" 
                               class="form-control" 
                               required 
                               maxlength="70">
                    </div>
                    
                    <!-- Nom -->
                    <div class="mb-3">
                        <label class="form-label">Nom *</label>
                        <input type="text" 
                               name="nomMemb" 
                               class="form-control" 
                               required 
                               maxlength="70">
                    </div>
                    
                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" 
                               name="eMailMemb" 
                               class="form-control" 
                               required 
                               maxlength="100">
                    </div>
                    
                    <!-- Confirmation Email -->
                    <div class="mb-3">
                        <label class="form-label">Confirmer l'email *</label>
                        <input type="email" 
                               name="eMailMemb_confirm" 
                               class="form-control" 
                               required 
                               maxlength="100">
                    </div>
                    
                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label">Mot de passe *</label>
                        <input type="password" 
                               name="passMemb" 
                               class="form-control" 
                               required 
                               pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,15}$"
                               title="8-15 caractères, 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial">
                        <small class="form-text text-muted">
                            8 à 15 caractères avec au moins :<br>
                            - 1 majuscule<br>
                            - 1 minuscule<br>
                            - 1 chiffre<br>
                            - 1 caractère spécial (@$!%*?&)
                        </small>
                    </div>
                    
                    <!-- Confirmation Password -->
                    <div class="mb-3">
                        <label class="form-label">Confirmer le mot de passe *</label>
                        <input type="password" 
                               name="passMemb_confirm" 
                               class="form-control" 
                               required>
                    </div>
                    
                    <!-- RGPD -->
                    <div class="mb-3 form-check">
                        <input type="radio" 
                               name="accordMemb" 
                               value="1" 
                               id="rgpd_oui" 
                               class="form-check-input" 
                               required>
                        <label class="form-check-label" for="rgpd_oui">
                            J'accepte le stockage de mes données personnelles *
                        </label>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="radio" 
                               name="accordMemb" 
                               value="0" 
                               id="rgpd_non" 
                               class="form-check-input">
                        <label class="form-check-label" for="rgpd_non">
                            Je refuse
                        </label>
                    </div>
                    
                    <small class="form-text text-muted mb-3 d-block">
                        En acceptant, vous consentez au traitement de vos données conformément au RGPD.
                    </small>
                    
                    <!-- Bouton reCAPTCHA -->
                    <button class="g-recaptcha btn btn-primary" 
                            data-sitekey="VOTRE_CLE_SITE"
                            data-callback='onSubmit' 
                            data-action='submit'>
                        S'inscrire
                    </button>
                    
                    <a href="login.php" class="btn btn-link">Déjà inscrit ? Se connecter</a>
                </form>
            </div>
        </div>
    </div>
    
    <script>
    function onSubmit(token) {
        document.getElementById("form-recaptcha").submit();
    }
    </script>
</body>
</html>