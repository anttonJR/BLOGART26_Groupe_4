<?php 
session_start();
include 'includes/cookie-consent.php';
require_once '../../functions/csrf.php';
$pageTitle = 'Événements - Millésime Blog\'Art';
include 'includes/header.php';
?>

<div class="container mt-5">
    <h1>Événements</h1>
    <p class="lead">Les derniers événements et actualités.</p>
</div>

<?php
include 'includes/footer.php';
?>