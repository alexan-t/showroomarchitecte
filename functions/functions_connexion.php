<?php
///Activation Compte 
function send_activation_email($user_id) {
    $user = get_user_by('ID', $user_id);
    if (!$user) {
        return false;
    }

    // Générer une clé d'activation si elle n'existe pas déjà
    $activation_key = get_user_meta($user_id, 'activation_key', true);
    if (empty($activation_key)) {
        $activation_key = wp_generate_password(20, false);
        update_user_meta($user_id, 'activation_key', $activation_key);
    }

    // Générer le lien d'activation
    $activation_url = add_query_arg([
        'action' => 'activate_account',
        'key' => $activation_key,
        'user' => $user_id,
    ], home_url('/connexion/'));

    // Créer le message
    $subject = 'Activation de votre compte';
    $message = "Bonjour,\n\n";
    $message .= "Merci de vous être inscrit sur Showroom Architecte.\n";
    $message .= "Veuillez cliquer sur ce lien pour activer votre compte :\n\n";
    $message .= "$activation_url\n\n";
    $message .= "Si vous n’avez pas fait cette demande, ignorez ce message.\n\n";
    $message .= "Cordialement,\nL’équipe de Showroom Architecte.";

    // Envoyer l'e-mail
    return wp_mail($user->user_email, $subject, $message);
}


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

    // Si l'entrée est un e-mail, récupérer le nom d'utilisateur associé
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

    // Tentative de connexion
    $remember = !empty($_POST['remember']) && $_POST['remember'] === '1';

    $creds = [
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => $remember,
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
    }

    // Vérification si le compte est activé
    $user_id = $user->ID;
    $is_active = get_user_meta($user_id, 'is_active', true);

    if ($is_active != 1) {
        send_activation_email($user_id);
    
        wp_send_json_error([
            'message' => 'Votre compte n’est pas encore activé. Un e-mail d’activation vous a été envoyé.',
            'need_activation' => true,
            'user_id' => $user_id
        ]);
        return;
    }

    // Connexion réussie et compte actif
    wp_send_json_success([
        'message' => 'Connexion réussie. Redirection en cours...',
        'redirect_url' => home_url('/tableau-de-bord/')
    ]);
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


        wp_enqueue_script('formForgotPassword', get_template_directory_uri() . '/assets/src/js/ajax/formForgotPassword.js', ['jquery'], null, true);

        wp_localize_script('form-login', 'formLogin', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('user_login_nonce'),
            'resetUrl' => wp_lostpassword_url(),
        ]);
        
    }
}
add_action('wp_enqueue_scripts', 'enqueue_login_script');


////////////////////////////////////////////// Fonction Mot de passe oublié
function handle_forgot_password() {
    if (!check_ajax_referer('user_login_nonce', 'security', false)) {
        wp_send_json_error(['message' => 'Nonce invalide.']);
        return;
    }

    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';

    if (empty($email) || !is_email($email)) {
        wp_send_json_error(['message' => 'Veuillez entrer une adresse e-mail valide.']);
        return;
    }

    $user = get_user_by('email', $email);
    if (!$user) {
        wp_send_json_error(['message' => 'Aucun utilisateur trouvé avec cette adresse e-mail.']);
        return;
    }

    $reset_key = get_password_reset_key($user);
    if (is_wp_error($reset_key)) {
        wp_send_json_error(['message' => 'Impossible de générer un lien de réinitialisation.']);
        return;
    }

    
    $reset_link = add_query_arg([
        'type' => 'forgot_password',
        'key'  => $reset_key,
        'login' => rawurlencode($user->user_login),
    ], site_url('/connexion/'));
    

    $subject = 'Réinitialisation de votre mot de passe';
    $message = "Bonjour,\n\n";
    $message .= "Cliquez sur le lien suivant pour réinitialiser votre mot de passe :\n\n$reset_link\n\n";
    $message .= "Si vous n’avez pas fait cette demande, vous pouvez ignorer ce message.\n\n";
    $message .= "Cordialement,\nL'équipe de Showroom Architecte.";

    if (wp_mail($email, $subject, $message)) {
        wp_send_json_success(['message' => 'Un e-mail de réinitialisation a été envoyé.']);
    } else {
        wp_send_json_error(['message' => 'Erreur lors de l\'envoi de l\'e-mail.']);
    }
}
add_action('wp_ajax_forgot_password', 'handle_forgot_password');
add_action('wp_ajax_nopriv_forgot_password', 'handle_forgot_password');


function block_inactive_users() {
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $is_active = get_user_meta($user_id, 'is_active', true);

        // Rediriger si l'utilisateur n'est pas activé et n'est pas déjà sur la page d'activation
        if ($is_active != 1 && !is_page('activation-compte')) {
            wp_logout();
            wp_redirect(home_url('/connexion/?erreur=activation'));
            exit;
        }
    }
}
add_action('template_redirect', 'block_inactive_users');

function handle_account_activation() {
    if (!isset($_GET['action']) || $_GET['action'] !== 'activate_account') return;

    $user_id = isset($_GET['user']) ? absint($_GET['user']) : 0;
    $key = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';

    if (!$user_id || !$key) return;

    $saved_key = get_user_meta($user_id, 'activation_key', true);

    if ($saved_key === $key) {
        update_user_meta($user_id, 'is_active', 1);
        delete_user_meta($user_id, 'activation_key');

        wp_redirect(home_url('/connexion/?activation=success'));
        exit;
    } else {
        wp_redirect(home_url('/connexion/?activation=failed'));
        exit;
    }
}
add_action('init', 'handle_account_activation');