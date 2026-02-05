<?php
session_start();
include 'includes/cookie-consent.php';
require_once '../../functions/csrf.php';
$pageTitle = 'Acteurs - Millésime Blog\'Art';
include 'includes/header.php';
?>

<div class="container mt-5">
    <h1>Acteurs</h1>
    <p class="lead">Découvrez les principaux acteurs du blog.</p>
</div>

<?php
include 'includes/footer.php';
?> 