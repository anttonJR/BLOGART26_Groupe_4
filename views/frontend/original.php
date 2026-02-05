<?php 
session_start();
include 'includes/cookie-consent.php';
require_once '../../functions/csrf.php';
$pageTitle = 'Insolite - Millésime Blog\'Art';
include 'includes/header.php';
?>

<div class="container mt-5">
    <h1>Insolite</h1>
    <p class="lead">Découvrez les histoires insolites et surprenantes.</p>
</div>

<?php
include 'includes/footer.php';
?> 
