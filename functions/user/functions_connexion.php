<?php

function handle_login_user() {
    // Vérification du nonce
    if (!check_ajax_referer('user_login_nonce', 'security', false)) {
        wp_send_json_error(['message' => 'Nonce invalide.']);
        return;
    }

    // Récupération et validation des données
    $username = isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '';
    $password = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : '';

    if (empty($username) || empty($password)) {
        wp_send_json_error(['message' => 'Veuillez remplir tous les champs.']);
        return;
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