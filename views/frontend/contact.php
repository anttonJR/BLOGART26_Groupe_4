<?php
session_start();
include 'includes/cookie-consent.php';
require_once '../../functions/csrf.php';
$pageTitle = 'Contact - BlogArt';
include 'includes/header.php';
?>

<div class="container mt-5">
    <h1>Contactez-nous</h1>
    <p class="lead">Une question, une suggestion ? N'hésitez pas à nous contacter !</p>
    
    <?php if (isset($_SESSION['contact_success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['contact_success'] ?>
        </div>
        <?php unset($_SESSION['contact_success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['contact_error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['contact_error'] ?>
        </div>
        <?php unset($_SESSION['contact_error']); ?>
    <?php endif; ?>
    
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="../api/contact/send.php">
                        <?php csrfField(); ?>
                        <div class="mb-3">
                            <label class="form-label">Nom *</label>
                            <input type="text" 
                                   name="nom" 
                                   class="form-control" 
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" 
                                   name="email" 
                                   class="form-control" 
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Sujet *</label>
                            <input type="text" 
                                   name="sujet" 
                                   class="form-control" 
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Message *</label>
                            <textarea name="message" 
                                      class="form-control" 
                                      rows="6" 
                                      required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Envoyer</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Informations</h5>
                    <p><strong>Email :</strong> contact@blogart.fr</p>
                    <p><strong>Réseaux sociaux :</strong></p>
                    <ul class="list-unstyled">
                        <li>Facebook : @blogart</li>
                        <li>Twitter : @blogart</li>
                        <li>Instagram : @blogart</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>