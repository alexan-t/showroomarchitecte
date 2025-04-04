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


$type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : 'connexion';
if (!in_array($type, ['inscription', 'connexion','forgot_password'])) {
    $type = 'connexion'; 
}



get_header(); ?>




<section class="connexion-page py-5">
    <div class="container">
        <?php 
        if($type === "connexion") {
            include_once( get_template_directory() . '/templates/parts/card-connexion.php' ); 
        } else if ($type === "inscription") {
            include_once( get_template_directory() . '/templates/parts/card-inscription.php' ); 
        } else if ($type === "forgot_password"){
            include_once( get_template_directory() . '/templates/parts/card-forgot_password.php' ); 
        }
        ?>
    </div>
</section>

<?php get_footer(); ?>