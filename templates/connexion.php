<?php
/* Template Name: Page de Connexion */

// Redirection des utilisateurs connectés
if (is_user_logged_in()) {
    wp_redirect("/tableau-de-bord");
    exit;
}

// Traiter la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '';
    $password = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : '';

    if ($username && $password) {
        $creds = [
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      => isset($_POST['remember']),
        ];

        $user = wp_signon($creds, false);

        if (is_wp_error($user)) {
            $error_message = $user->get_error_message();
        } else {
            wp_redirect(home_url('/tableau-de-bord/'));
            exit;
        }
    }
}


$type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : 'particulier';
if (!in_array($type, ['professionnel', 'particulier'])) {
    $type = 'particulier'; 
}



get_header(); ?>




<section class="connexion-page bg-blue-dark mb-1">
    <div class="container">
        <h1 class="sr-only">Connexion Showroom d'arcitecte - architectes vérifiés dans votre ville pour donner vie à vos
            projets - France</h1>
        <h2>Espace <?php echo $type === 'professionnel' ? 'Professionnel' : 'Particulier'; ?></h2>
        <div class="flex gap-2">
            <div class=" col-md-6 card card-connexion
            <?php echo $type === 'professionnel' ? 'pro-connexion' : 'particulier-connexion'; ?>">
                <div
                    class="text-2xl uppercase text-center <?php echo $type === 'professionnel' ? 'color-professionnel' : 'color-blue'; ?>">
                    J’ai déjà un compte</div>
                <form method="post" action="" class="connexion-form">
                    <input type="text" id="inputUsername" name="username" class="custom-input" placeholder="E-mail"
                        required>
                    <br />
                    <input type="password" name="password" class="custom-input" placeholder="Mot de passe" required>
                    <div class="mt-1 text-end">
                        <a href="#" id="forgotPasswordLink" class="fontroboto color-gray underline italic">
                            Mot de passe oublié
                        </a>
                    </div>
                    <div class="text-center">
                        <button type="submit" name="login_user"
                            class="btn btn-blue mt-3 uppercase signin">Connexion</button>
                    </div>
                    <!-- Sécurité WordPress -->
                    <?php wp_nonce_field('user_login_nonce', 'login_nonce'); ?>
                </form>
                <h3 class="login_msg color-white text-center"></h3>
                <div class="log"
                    style="background-color: <?php echo $type === 'professionnel' ? '#fc8f02' : '#3968a8'; ?>;">
                </div>
            </div>
            <div
                class="col-md-6 card card-inscription <?php echo $type === 'professionnel' ? 'pro-inscription' : 'particulier-inscription'; ?>">
                <div class="text-2xl uppercase text-center color-white">
                    Je n’ai pas de compte</div>
                <p class="text-center color-white bold fontlato py-3">Créez gratuitement et en un clic votre compte pour
                    contacter
                    l’architecte de
                    votre projet.</p>
                <form method="post" class="inscription-form">
                    <input type="email" name="email" class="custom-input" placeholder="Adresse e-mail" required>
                    <input type="hidden" name="type" value="<?php echo $type; ?>">
                    <?php wp_nonce_field('user_register_nonce', 'register_nonce'); ?>
                    <div class="text-center">
                        <button type="submit"
                            class="register btn mt-3 uppercase <?php echo $type === 'professionnel' ? 'btn-outline-professionnel' : 'btn-outline-blue'; ?>">C’est
                            parti !</button>
                    </div>
                </form>
                <h3 class="register_msg color-white text-center"></h3>
                <div class="reg"
                    style="background-color: <?php echo $type === 'professionnel' ? '#fc8f02' : '#3968a8'; ?>;">
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>