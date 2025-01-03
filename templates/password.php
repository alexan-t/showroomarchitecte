<?php
/* Template Name: Reset Password */

// Récupération des paramètres GET
$key   = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';
$login = isset($_GET['login']) ? sanitize_text_field($_GET['login']) : '';

if (empty($key) || empty($login)) {
    wp_die('Clé de réinitialisation invalide.');
}

// Valider la clé
$user = check_password_reset_key($key, $login);

if (is_wp_error($user)) {
    wp_die($user->get_error_message());
}

// Récupérer le type d'utilisateur à partir de la méta-clé
$user_type = get_user_meta($user->ID, 'user_type', true); // 'professionnel' ou 'particulier'
$class_type = $user_type === 'professionnel' ? 'pro-style' : 'particulier-style';
$userType = $user_type === 'professionnel' ? 'professionnel' : 'particulier';

// Initialisation des erreurs
$error = '';

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? sanitize_text_field($_POST['confirm_password']) : '';

    // Expression régulière pour valider le mot de passe
    $password_regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';

    if (empty($password) || empty($confirm_password)) {
        $error = 'Veuillez remplir tous les champs.';
    } elseif ($password !== $confirm_password) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif (!preg_match($password_regex, $password)) {
        $error = 'Le mot de passe doit comporter au moins 8 caractères, une majuscule, une minuscule et un chiffre.';
    } else {
        // Réinitialisation du mot de passe
        reset_password($user, $password);
        wp_redirect(home_url('/connexion/?password_reset=success'));
        exit;
    }
}

get_header(); ?>

<style>
header {
    display: none;
}
</style>

<section class="reset-password-page">
    <div class="header-simple py-3">
        <div class="container">
            <div class="flex gap-5 justify-between">
                <a class="logo col-md-6" href="<?php echo site_url('/'); ?>">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo.svg"
                        alt="Logo de Showroom d'Architecte">
                </a>
                <div class="p-relative col-md-6">
                    <img class="reset-password-page-shape"
                        src="<?php echo get_template_directory_uri(); ?>/assets/img/<?php echo $userType ; ?>-shape.svg"
                        alt="">
                    <div class="reset-password-page-shape-text"
                        style="background-color : <?php echo $user_type === 'professionnel' ? '#fc8f02' : '#3968a8' ;?>">
                        <h2>Inscription</h2>
                        <p class="text-md">compte
                            <?php echo $userType; ?> </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class=" py-5 bg-grey">
        <div class="container">
            <div class="font-bold uppercase text-xl">INSCRIPTION VALIDEE. Créez un mot de passe</div>
            <form method="post" class="inscription-form flex flex-col gap-1 pt-3">
                <?php if (!empty($error)) : ?>
                <div class="error-message"><?php echo esc_html($error); ?></div>
                <?php endif; ?>
                <input type="password" name="password" class="custom-input" placeholder="Mot de passe" required>
                <input type="password" name="confirm_password" class="custom-input" placeholder="Confirmer Mot de passe"
                    required>
                <div class="text-center">
                    <button type="submit"
                        class="register btn mt-3 uppercase <?php echo $user_type === 'professionnel' ? 'btn-outline-standard' : 'btn-outline-blue' ;?>">C’est
                        parti !</button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php get_footer(); ?>