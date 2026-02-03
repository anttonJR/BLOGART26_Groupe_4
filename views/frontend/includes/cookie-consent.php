<?php
// Vérifier si le choix a déjà été fait
if (!isset($_COOKIE['cookie_consent'])) {
?>
<div id="cookie-consent" class="position-fixed bottom-0 start-0 w-100 bg-dark text-white p-3" style="z-index: 9999;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <p class="mb-0">
                    Ce site utilise des cookies pour améliorer votre expérience. 
                    En continuant à naviguer, vous acceptez notre utilisation des cookies.
                    <a href="/views/frontend/rgpd/rgpd.php" class="text-white">En savoir plus</a>
                </p>
            </div>
            <div class="col-md-4 text-end">
                <button onclick="acceptCookies()" class="btn btn-success me-2">Accepter</button>
                <button onclick="refuseCookies()" class="btn btn-secondary">Refuser</button>
            </div>
        </div>
    </div>
</div>

<script>
function acceptCookies() {
    document.cookie = "cookie_consent=accepted; max-age=31536000; path=/";
    document.getElementById('cookie-consent').style.display = 'none';
}

function refuseCookies() {
    document.cookie = "cookie_consent=refused; max-age=31536000; path=/";
    document.getElementById('cookie-consent').style.display = 'none';
}
</script>
<?php
}
?>