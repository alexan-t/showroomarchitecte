<?php


////////////////////////////////////////////// Fonction Connexion
function handle_login_user() {
    // Vérification du nonce
    if (!check_ajax_referer('user_login_nonce', 'security', false)) {
        wp_send_json_error(['message' => 'Nonce invalide.']);
        return;
    }

    // Récupération et validation des données
    $username_or_email = isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '';
    $password = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : '';

    if (empty($username_or_email) || empty($password)) {
        wp_send_json_error(['message' => 'Veuillez remplir tous les champs.']);
        return;
    }

    // Si l'entrée est un e-mail, récupérez le nom d'utilisateur associé
    if (is_email($username_or_email)) {
        $user = get_user_by('email', $username_or_email);
        if (!$user) {
            wp_send_json_error(['message' => 'Aucun compte trouvé avec cet e-mail.']);
            return;
        }
        $username = $user->user_login;
    } else {
        $username = $username_or_email;
    }

    // Connexion utilisateur
    $creds = [
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => true,
    ];

    $user = wp_signon($creds, false);

    if (is_wp_error($user)) {
        $error_code = $user->get_error_code();
        $error_message = '';

        switch ($error_code) {
            case 'invalid_username':
                $error_message = 'Nom d’utilisateur ou e-mail invalide.';
                break;
            case 'incorrect_password':
                $error_message = 'Mot de passe incorrect.';
                break;
            default:
                $error_message = $user->get_error_message();
                break;
        }

        wp_send_json_error(['message' => $error_message]);
    } else {
        // Connexion réussie
        wp_send_json_success([
            'message' => 'Connexion réussie. Redirection en cours...',
            'redirect_url' => home_url('/tableau-de-bord/')
        ]);
    }
}
add_action('wp_ajax_login_user', 'handle_login_user');
add_action('wp_ajax_nopriv_login_user', 'handle_login_user');


////////////////////////////////////////////// Fontion Pour Link le JS Ajax
function enqueue_login_script() {
    if (is_page_template('templates/connexion.php')) {
        wp_enqueue_script(
            'form-login',
            get_template_directory_uri() . '/assets/src/js/ajax/formLogin.js',
            ['jquery'],
            null,
            true
        );

        wp_localize_script('form-login', 'formLogin', [
            'ajaxurl' => admin_url('admin-ajax.php'),
        ]);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_login_script');


////////////////////////////////////////////// Fonction Mot de passe oublié
function handle_forgot_password() {
    // Vérifiez le nonce
    if (!check_ajax_referer('user_login_nonce', 'security', false)) {
        wp_send_json_error(['message' => 'Nonce invalide.']);
        return;
    }

    // Récupérez l'e-mail depuis la requête AJAX
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';

    if (empty($email) || !is_email($email)) {
        wp_send_json_error(['message' => 'Veuillez entrer une addresse e-mail valide.']);
        return;
    }

    // Vérifiez si un utilisateur avec cet e-mail existe
    $user = get_user_by('email', $email);

    if (!$user) {
        wp_send_json_error(['message' => 'Aucun utilisateur trouvé avec cette addresse e-mail.']);
        return;
    }

    // Générez un lien de réinitialisation de mot de passe
    $reset_key = get_password_reset_key($user);

    if (is_wp_error($reset_key)) {
        wp_send_json_error(['message' => 'Impossible de générer un lien de réinitialisation.']);
        return;
    }

    $reset_link = add_query_arg([
        'action' => 'rp',
        'key'    => $reset_key,
        'login'  => rawurlencode($user->user_login),
    ], wp_login_url());

    // Envoyez l'e-mail à l'utilisateur
    $subject = 'Réinitialisation de votre mot de passe';
    $message = "Bonjour,\n\nCliquez sur le lien suivant pour réinitialiser votre mot de passe :\n\n$reset_link\n\nCordialement,\nL'équipe de Showroom Architecte.";

    if (wp_mail($email, $subject, $message)) {
        wp_send_json_success(['message' => 'Un e-mail de réinitialisation a été envoyé.']);
    } else {
        wp_send_json_error(['message' => 'Erreur lors de l\'envoi de l\'e-mail.']);
    }
}

add_action('wp_ajax_forgot_password', 'handle_forgot_password');
add_action('wp_ajax_nopriv_forgot_password', 'handle_forgot_password');