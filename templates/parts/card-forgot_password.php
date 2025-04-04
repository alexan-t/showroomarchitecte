<?php
$login = isset($_GET['login']) ? sanitize_user($_GET['login']) : '';
$key   = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';

// Si login ou clé manquants
if (empty($login) || empty($key)) {
    echo '<div class="alert alert-danger text-center">Lien invalide ou expiré.</div>';
    return;
}

// Vérifie la validité du lien
$user = check_password_reset_key($key, $login);

if (is_wp_error($user)) {
    echo '<div class="alert alert-danger text-center">Ce lien de réinitialisation est invalide ou a expiré.</div>';
    return;
}
?>


<div class="row mt-5 card card-forgotPassword">
    <div
        class="col-md-6 bg-blue-dark color-white flex justify-center items-center rounded-top-left rounded-bottom-left">
        <div class="flex flex-col justify-center text-center">
            <div class="text-center">
                <ion-icon class="text-6xl " name="keypad-outline"></ion-icon>
            </div>
            <h3>Mot de passee oublié</h3>
            <p>Entrer vos nouveau informations d'identification</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="p-1">
            <div class="text-center">
                <ion-icon name="key-outline" class="text-6xl"></ion-icon><br>
                <h3 class="bold-500">Réinitialisation de Mot de Passe</h3>
            </div>
            <form method="post" action="" id="forgotPasswordForm" class="forgotPassword-form border-bottom">
                <input type="password" name="new_password" class="custom-input" placeholder="Mot de passe" required>
                <br />
                <input type="password" name="confirm_password" class="custom-input" placeholder="Confirmer Mot de passe"
                    required>

                <input type="hidden" name="reset_key" value="<?php echo esc_attr($_GET['key']); ?>">
                <input type="hidden" name="reset_login" value="<?php echo esc_attr($_GET['login']); ?>">
                <input type="hidden" name="reset_password_submit" value="1">

                <div class="row mt-1 items-center">
                    <div class="col-md-12 ">
                        <button class="btn justify-center rounded-5 w-100 btn-dark">Confirmer</button>
                    </div>
                </div>
            </form>
            <div class="text-center pt-2">
                <p class="text-sm color-dark"><span class="underline">
                        <a class="color-dark" href="#">Besoin d'aide ? Contactez-nous au plus vite</a></span></p>
            </div>
        </div>
    </div>
</div>