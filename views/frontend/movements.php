<?php 
session_start();
include 'includes/cookie-consent.php';
require_once '../../functions/csrf.php';
$pageTitle = 'Mouvements - Millésime Blog\'Art';
include 'includes/header.php';
?>

<div class="container mt-5">
    <h1>Mouvements</h1>
    <p class="lead">Explorez les différents mouvements artistiques.</p>
</div>

<?php
include 'includes/footer.php';
?> 