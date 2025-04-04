<?php
//JS Inscription 
function enqueue_register_script() {
    if (is_page_template('templates/connexion.php')) {
        wp_enqueue_script('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', [], null, true);

        wp_enqueue_script(
            'showroom-register',
            get_template_directory_uri() . '/assets/src/js/ajax/formInscription.js',
            ['jquery'],
            null,
            true
        );

        wp_localize_script('showroom-register', 'showroom_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
        ]);

    }
}
add_action('wp_enqueue_scripts', 'enqueue_register_script');



// Fonction qui gère l'inscription

function showroom_handle_ajax_registration() {
    $response = ['success' => false, 'message' => 'Une erreur est survenue.'];

    // Vérification des champs obligatoires
    if (!isset($_POST['username'], $_POST['password'], $_POST['user_type'], $_POST['first_name'], $_POST['last_name'], $_POST['terms'])) {
        $response['message'] = "Merci de remplir tous les champs obligatoires de l'étape 1.";
        wp_send_json($response);
    }

    // Sanitize des champs
    $email       = sanitize_email($_POST['username']);
    $first_name  = sanitize_text_field($_POST['first_name']);
    $last_name   = sanitize_text_field($_POST['last_name']);
    $password    = $_POST['password'];
    $user_type   = sanitize_text_field($_POST['user_type']);

    // Vérification email
    if (!is_email($email)) {
        $response['message'] = "Adresse e-mail invalide.";
        wp_send_json($response);
    }
    if (email_exists($email)) {
        $response['message'] = "Un compte avec cet e-mail existe déjà.";
        wp_send_json($response);
    }

    // Vérification mot de passe
    if (
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[^a-zA-Z0-9]/', $password)
    ) {
        $response['message'] = "Mot de passe trop faible. Il doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
        wp_send_json($response);
    }

    // Création de l'utilisateur
    $user_id = wp_create_user($email, $password, $email);
    if (is_wp_error($user_id)) {
        $response['message'] = "Erreur lors de la création du compte.";
        wp_send_json($response);
    }

    // Mise à jour des infos de base
    wp_update_user([
        'ID' => $user_id,
        'first_name' => $first_name,
        'last_name'  => $last_name
    ]);

    $user = new WP_User($user_id);
    $user->set_role($user_type);
    update_user_meta($user_id, 'user_type', $user_type);
    update_user_meta($user_id, 'is_active', 0); // Compte inactif par défaut

    // Données supplémentaires
    $fields = [
        'telephone'   => 'telephone',
        'birthdate'   => 'birthdate',
        'address'     => 'address',
        'city'        => 'city',
        'postalcode'  => 'postalcode',
    ];
    foreach ($fields as $postKey => $metaKey) {
        $value = isset($_POST[$postKey]) ? sanitize_text_field($_POST[$postKey]) : '';
        update_user_meta($user_id, $metaKey, $value);
    }

    // Géolocalisation
    $latitude = null;
    $longitude = null;
    if (!empty($_POST['city'])) {
        $city_encoded = urlencode($_POST['city']);
        $api_url = "https://nominatim.openstreetmap.org/search?q={$city_encoded}&format=json&limit=1";

        $geo_response = wp_remote_get($api_url, [
            'timeout' => 10,
            'headers' => ['User-Agent' => 'WordPress/Showroom Architecte']
        ]);

        if (!is_wp_error($geo_response) && wp_remote_retrieve_response_code($geo_response) === 200) {
            $data = json_decode(wp_remote_retrieve_body($geo_response), true);
            if (!empty($data[0]['lat']) && !empty($data[0]['lon'])) {
                $latitude = floatval($data[0]['lat']);
                $longitude = floatval($data[0]['lon']);
            }
        }
    }
    update_user_meta($user_id, 'latitude', $latitude);
    update_user_meta($user_id, 'longitude', $longitude);

    // Photo de profil
    if (!empty($_FILES['profile_image']['name'])) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        $upload = wp_handle_upload($_FILES['profile_image'], ['test_form' => false]);
        if (!isset($upload['error'])) {
            update_user_meta($user_id, 'profile_image', esc_url($upload['url']));
        }
    }

    // Champs entreprise si professionnel
    if ($user_type === 'professionnel') {
        $pro_fields = ['company_name', 'siren', 'ape'];
        foreach ($pro_fields as $field) {
            $value = isset($_POST[$field]) ? sanitize_text_field($_POST[$field]) : '';
            update_user_meta($user_id, $field, $value);
        }

        // KBIS upload
        if (!empty($_FILES['kbis_file']['name'])) {
            $upload = wp_handle_upload($_FILES['kbis_file'], ['test_form' => false]);
            if (!isset($upload['error'])) {
                update_user_meta($user_id, 'kbis_file', esc_url($upload['url']));
            }
        }
    }

    // ✅ Génération de la clé d’activation
    $activation_key = wp_generate_password(32, false);
    update_user_meta($user_id, 'activation_key', $activation_key);

    $activation_link = add_query_arg([
        'action' => 'activate_account',
        'key'    => $activation_key,
        'user'   => $user_id
    ], site_url('/connexion/'));

    // Envoi de l’e-mail
    $subject = 'Activation de votre compte';
    $message = "Bonjour $first_name,\n\n";
    $message .= "Merci pour votre inscription sur Showroom Architecte.\n";
    $message .= "Veuillez cliquer sur ce lien pour activer votre compte :\n$activation_link\n\n";
    $message .= "Cordialement,\nL’équipe Showroom Architecte";

    wp_mail($email, $subject, $message);

    // ✅ Réponse JSON pour JS
    $response['success'] = true;
    $response['message'] = "Compte créé. Vérifiez vos e-mails pour l'activer.";
    $response['redirect'] = site_url('/connexion/') . '?type=connexion';
    wp_send_json($response);
}
add_action('wp_ajax_showroom_handle_registration', 'showroom_handle_ajax_registration');
add_action('wp_ajax_nopriv_showroom_handle_registration', 'showroom_handle_ajax_registration');



add_action('wp_ajax_get_sirene_info', 'showroom_get_sirene_info');
add_action('wp_ajax_nopriv_get_sirene_info', 'showroom_get_sirene_info');

function showroom_get_sirene_info() {
    $siren = sanitize_text_field($_POST['siren'] ?? '');

    if (empty($siren) || !preg_match('/^\d{9}$/', $siren)) {
        wp_send_json_error(['message' => 'Numéro SIREN invalide.']);
    }

    $api_key = 'a36c371e-a3a2-4c82-ac37-1ea3a28c82e1'; // Remplace par ta clé

    $response = wp_remote_get("https://api.insee.fr/api-sirene/3.11/siren/{$siren}", [
        'headers' => [
            'X-INSEE-Api-Key-Integration' => $api_key,
            'Accept' => 'application/json',
        ],
        'timeout' => 10,
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Erreur API INSEE.']);
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (empty($data)) {
        wp_send_json_error(['message' => 'Entreprise non trouvée.']);
    }

    wp_send_json_success($data);
}