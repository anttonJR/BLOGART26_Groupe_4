<?php
$pageTitle = "Politique de Confidentialité - BlogArt";
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm" style="background-color: white;">
            <div class="card-body p-5">
                <h1 class="text-center mb-4" style="font-family: 'Cormorant Garamond', serif; color: #800000;">
                    <i class="bi bi-shield-check me-2"></i>Politique de Confidentialité
                </h1>
                
                <p class="text-muted text-center mb-5">Dernière mise à jour : <?= date('d/m/Y') ?></p>
                
                <div class="alert alert-info mb-5">
                    <i class="bi bi-info-circle me-2"></i>
                    Cette politique de confidentialité est conforme au Règlement Général sur la Protection des Données (RGPD).
                </div>
                
                <section class="mb-5">
                    <h2 style="color: #800000;">1. Responsable du traitement</h2>
                    <p>Le responsable du traitement des données personnelles est :</p>
                    <ul>
                        <li><strong>BlogArt</strong></li>
                        <li>MMI28 - Projet étudiant</li>
                        <li>Email : contact@blogart.fr</li>
                    </ul>
                </section>
                
                <section class="mb-5">
                    <h2 style="color: #800000;">2. Données collectées</h2>
                    <p>Nous collectons les données suivantes :</p>
                    <table class="table table-bordered">
                        <thead style="background-color: #800000; color: white;">
                            <tr>
                                <th>Type de donnée</th>
                                <th>Finalité</th>
                                <th>Base légale</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Nom, prénom, pseudo</td>
                                <td>Identification de l'utilisateur</td>
                                <td>Exécution du contrat</td>
                            </tr>
                            <tr>
                                <td>Adresse email</td>
                                <td>Communication, authentification</td>
                                <td>Exécution du contrat</td>
                            </tr>
                            <tr>
                                <td>Mot de passe (haché)</td>
                                <td>Sécurisation du compte</td>
                                <td>Exécution du contrat</td>
                            </tr>
                            <tr>
                                <td>Adresse IP</td>
                                <td>Sécurité, statistiques</td>
                                <td>Intérêt légitime</td>
                            </tr>
                        </tbody>
                    </table>
                </section>
                
                <section class="mb-5">
                    <h2 style="color: #800000;">3. Durée de conservation</h2>
                    <p>Vos données sont conservées :</p>
                    <ul>
                        <li><strong>Données de compte :</strong> Pendant toute la durée de votre inscription + 3 ans après suppression</li>
                        <li><strong>Cookies :</strong> Maximum 13 mois</li>
                        <li><strong>Logs de connexion :</strong> 12 mois</li>
                    </ul>
                </section>
                
                <section class="mb-5">
                    <h2 style="color: #800000;">4. Vos droits</h2>
                    <p>Conformément au RGPD, vous disposez des droits suivants :</p>
                    <div class="row">
                        <div class="col-md-6">
                            <ul>
                                <li><i class="bi bi-check-circle text-success me-2"></i>Droit d'accès</li>
                                <li><i class="bi bi-check-circle text-success me-2"></i>Droit de rectification</li>
                                <li><i class="bi bi-check-circle text-success me-2"></i>Droit à l'effacement</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul>
                                <li><i class="bi bi-check-circle text-success me-2"></i>Droit à la portabilité</li>
                                <li><i class="bi bi-check-circle text-success me-2"></i>Droit d'opposition</li>
                                <li><i class="bi bi-check-circle text-success me-2"></i>Droit à la limitation</li>
                            </ul>
                        </div>
                    </div>
                    <p class="mt-3">Pour exercer vos droits, contactez-nous via notre <a href="<?= ROOT_URL ?>/views/frontend/contact.php" style="color: #800000;">formulaire de contact</a>.</p>
                </section>
                
                <section class="mb-5">
                    <h2 style="color: #800000;">5. Cookies</h2>
                    <p>Notre site utilise des cookies pour :</p>
                    <ul>
                        <li><strong>Cookies essentiels :</strong> Fonctionnement du site (session, authentification)</li>
                        <li><strong>Cookies analytiques :</strong> Mesure d'audience (anonymisés)</li>
                    </ul>
                    <p>Vous pouvez gérer vos préférences cookies à tout moment via les paramètres de votre navigateur.</p>
                </section>
                
                <section class="mb-5">
                    <h2 style="color: #800000;">6. Sécurité</h2>
                    <p>Nous mettons en œuvre les mesures suivantes pour protéger vos données :</p>
                    <ul>
                        <li>Chiffrement des mots de passe (bcrypt)</li>
                        <li>Connexion HTTPS sécurisée</li>
                        <li>Protection contre les attaques CSRF et XSS</li>
                        <li>Accès restreint aux données personnelles</li>
                    </ul>
                </section>
                
                <section class="mb-5">
                    <h2 style="color: #800000;">7. Réclamation</h2>
                    <p>En cas de réclamation, vous pouvez contacter la CNIL :</p>
                    <p>
                        Commission Nationale de l'Informatique et des Libertés<br>
                        3 Place de Fontenoy - TSA 80715<br>
                        75334 PARIS CEDEX 07<br>
                        <a href="https://www.cnil.fr" target="_blank" style="color: #800000;">www.cnil.fr</a>
                    </p>
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
