<?php
$pageTitle = "Conditions Générales d'Utilisation - BlogArt";
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm" style="background-color: white;">
            <div class="card-body p-5">
                <h1 class="text-center mb-4" style="font-family: 'Cormorant Garamond', serif; color: #800000;">
                    <i class="bi bi-file-text me-2"></i>Conditions Générales d'Utilisation
                </h1>
                
                <p class="text-muted text-center mb-5">Dernière mise à jour : <?= date('d/m/Y') ?></p>
                
                <section class="mb-5">
                    <h2 style="color: #800000;">1. Objet</h2>
                    <p>Les présentes Conditions Générales d'Utilisation (CGU) régissent l'accès et l'utilisation du site BlogArt, plateforme collaborative dédiée à l'art et à la culture.</p>
                    <p>En accédant au site, vous acceptez sans réserve les présentes CGU.</p>
                </section>
                
                <section class="mb-5">
                    <h2 style="color: #800000;">2. Accès au service</h2>
                    <p>L'accès au site est gratuit. Toutefois, certaines fonctionnalités nécessitent la création d'un compte utilisateur :</p>
                    <ul>
                        <li>Publication de commentaires</li>
                        <li>Publication d'articles (sous réserve d'approbation)</li>
                        <li>Gestion de votre profil</li>
                    </ul>
                    <p>Vous êtes responsable de la confidentialité de vos identifiants de connexion.</p>
                </section>
                
                <section class="mb-5">
                    <h2 style="color: #800000;">3. Propriété intellectuelle</h2>
                    <p>L'ensemble des contenus présents sur le site (textes, images, logos, etc.) sont protégés par le droit de la propriété intellectuelle.</p>
                    <p>Toute reproduction ou utilisation sans autorisation préalable est interdite.</p>
                </section>
                
                <section class="mb-5">
                    <h2 style="color: #800000;">4. Contenu utilisateur</h2>
                    <p>En publiant du contenu sur BlogArt, vous garantissez :</p>
                    <ul>
                        <li>Être l'auteur ou avoir les droits nécessaires sur le contenu publié</li>
                        <li>Que le contenu ne porte pas atteinte aux droits des tiers</li>
                        <li>Que le contenu est conforme aux lois en vigueur</li>
                    </ul>
                    <p>L'équipe de modération se réserve le droit de supprimer tout contenu jugé inapproprié.</p>
                </section>
                
                <section class="mb-5">
                    <h2 style="color: #800000;">5. Comportement des utilisateurs</h2>
                    <p>Sont strictement interdits :</p>
                    <ul>
                        <li>Les propos diffamatoires, injurieux ou discriminatoires</li>
                        <li>La publication de contenu illégal ou à caractère pornographique</li>
                        <li>Le spam et la publicité non sollicitée</li>
                        <li>Toute tentative de piratage ou d'accès non autorisé</li>
                    </ul>
                </section>
                
                <section class="mb-5">
                    <h2 style="color: #800000;">6. Responsabilité</h2>
                    <p>BlogArt ne peut être tenu responsable :</p>
                    <ul>
                        <li>Des interruptions temporaires du service</li>
                        <li>Du contenu publié par les utilisateurs</li>
                        <li>Des dommages directs ou indirects liés à l'utilisation du site</li>
                    </ul>
                </section>
                
                <section class="mb-5">
                    <h2 style="color: #800000;">7. Modification des CGU</h2>
                    <p>BlogArt se réserve le droit de modifier les présentes CGU à tout moment. Les utilisateurs seront informés des modifications par affichage sur le site.</p>
                </section>
                
                <section class="mb-5">
                    <h2 style="color: #800000;">8. Contact</h2>
                    <p>Pour toute question concernant les présentes CGU, vous pouvez nous contacter via notre <a href="<?= ROOT_URL ?>/views/frontend/contact.php" style="color: #800000;">formulaire de contact</a>.</p>
                </section>
                
                <div class="text-center mt-5">
                    <a href="<?= ROOT_URL ?>/index.php" class="btn btn-bordeaux">
                        <i class="bi bi-arrow-left me-2"></i>Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
