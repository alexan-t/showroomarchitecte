<?php


//Role à l'inscription
function add_custom_roles() {
    add_role('professionnel', 'Professionnel', array('read' => true));
    add_role('particulier', 'Particulier', array('read' => true));
}
add_action('init', 'add_custom_roles');


//Email pour mot de passe inscription
function custom_email_content($message, $key, $user_login, $user_data) {
    $reset_key = get_password_reset_key($user);
    $reset_link = add_query_arg([
        'action' => 'rp',
        'key'    => $reset_key,
        'login'  => rawurlencode($user->user_login),
    ], home_url('/templates/password'));
    return "Bonjour,\n\nCliquez sur le lien ci-dessous pour définir votre mot de passe :\n\n$reset_link\n\nCordialement,\nL'équipe Showroom Architecte.";
}
add_filter('retrieve_password_message', 'custom_email_content', 10, 4);


function handle_register_user() {
    // Vérifiez le nonce
    if (!check_ajax_referer('user_register_nonce', 'security', false)) {
        wp_send_json_error(['message' => 'Nonce invalide.']);
        return;
    }

    // Récupérez les données POST
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';

    // Validation des données
    if (empty($email) || !is_email($email)) {
        wp_send_json_error(['message' => 'Adresse e-mail invalide.']);
        return;
    }

    if (empty($type) || !in_array($type, ['professionnel', 'particulier'])) {
        wp_send_json_error(['message' => 'Type d\'utilisateur invalide.']);
        return;
    }

    if (email_exists($email)) {
        $user = get_user_by('email', $email);
    
        // Vérifiez si l'utilisateur a défini un mot de passe
        $password_not_set = get_user_meta($user->ID, 'password_not_set', true);
    
        if ($password_not_set) {
            wp_send_json_error([
                'message' => 'Un compte existe déjà avec cette adresse e-mail, mais le mot de passe n\'a pas encore été défini. <br> 
                <a href="#" class="resend-email underline italic color-white" data-user-id="' . esc_attr($user->ID) . '" data-security="' . esc_attr(wp_create_nonce('resend_email_nonce')) . '">Créer un mot de passe</a>'
            ]);
        } else {
            wp_send_json_error([
                'message' => 'Un compte existe déjà avec cette adresse e-mail. <br>'
            ]);
        }
    
        return;
    }
    

    // Créez un utilisateur WordPress
    $username = explode('@', $email)[0];
    $user_id = wp_create_user($username, wp_generate_password(), $email);

    if (!is_wp_error($user_id)) {
        // Ajoutez un rôle spécifique
        $user = new WP_User($user_id);
        if ($type === 'professionnel') {
            $user->add_role('professionnel');
        } else {
            $user->add_role('particulier');
        }

        // Enregistrez le type dans les métadonnées utilisateur
        update_user_meta($user_id, 'user_type', $type);
        update_user_meta($user_id, 'password_not_set', true);

        // Générez un lien de réinitialisation de mot de passe
        $reset_key = get_password_reset_key($user);
        $reset_link = add_query_arg([
            'action' => 'rp',
            'key'    => $reset_key,
            'login'  => rawurlencode($user->user_login),
        ], home_url('/password'));

        // Envoyez l'e-mail à l'utilisateur
        $subject = 'Créer votre mot de passe';
        $message = "Bonjour,\n\nCliquez sur le lien suivant pour définir votre mot de passe :\n\n$reset_link\n\nCordialement,\nL'équipe de Showroom Architecte.";

        wp_mail($email, $subject, $message);

         // Retourner le message avec le lien pour renvoyer l'email
         wp_send_json_success([
            'message' => 'Inscription réussie. <br> Un e-mail vous a été envoyé pour définir votre mot de passe. <br> 
            <a href="#" class="resend-email underline italic color-white" data-user-id="' . esc_attr($user_id) . '" data-security="' . esc_attr(wp_create_nonce('resend_email_nonce')) . '">Renvoyer l\'e-mail</a>',
        ]);
        
    } else {
        wp_send_json_error(['message' => 'Erreur lors de la création du compte.']);
    }
}
add_action('wp_ajax_register_user', 'handle_register_user');
add_action('wp_ajax_nopriv_register_user', 'handle_register_user');


//Fonction pour renvoyer l'email
function handle_resend_email() {
    // Vérifiez le nonce
    if (!check_ajax_referer('resend_email_nonce', 'security', false)) {
        wp_send_json_error(['message' => 'Nonce invalide.']);
        return;
    }

    // Récupérez l'ID utilisateur via $_POST
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $user = get_user_by('ID', $user_id);

    if (!$user) {
        wp_send_json_error(['message' => 'Utilisateur introuvable.']);
        return;
    }

    // Générez un nouveau lien de réinitialisation de mot de passe
    $reset_key = get_password_reset_key($user);
    $reset_link = add_query_arg([
        'action' => 'rp',
        'key'    => $reset_key,
        'login'  => rawurlencode($user->user_login),
    ], home_url('/password'));

    // Envoyez l'e-mail
    $subject = '[Re]Créer votre mot de passe';
    $message = "Bonjour,\n\nCliquez sur le lien suivant pour définir votre mot de passe :\n\n$reset_link\n\nCordialement,\nL'équipe de Showroom Architecte.";

    if (wp_mail($user->user_email, $subject, $message)) {
        wp_send_json_success(['message' => 'L\'e-mail a été renvoyé avec succès.']);
    } else {
        wp_send_json_error(['message' => 'Erreur lors de l\'envoi de l\'e-mail.']);
    }
}
add_action('wp_ajax_resend_password_email', 'handle_resend_email');
add_action('wp_ajax_nopriv_resend_password_email', 'handle_resend_email');






function enqueue_register_script() {
    if (is_page_template('templates/connexion.php')) {
        wp_enqueue_script(
            'form-register',
            get_template_directory_uri() . '/assets/src/js/ajax/formRegister.js',
            ['jquery'],
            null,
            true
        );

        // Utilisation correcte de wp_localize_script
        wp_localize_script('form-register', 'formRegister', [
            'ajaxurl' => admin_url('admin-ajax.php'),
        ]);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_register_script');

// add_action('after_password_reset', function($user) {
//     delete_user_meta($user->ID, 'password_not_set');
// });