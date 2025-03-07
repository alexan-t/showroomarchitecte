<?php 

// Fonction pour traiter la soumission du formulaire via AJAX
function handle_update_mes_informations_ajax() {
    // Vérifier le nonce pour la sécurité
    $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
    if (!wp_verify_nonce($nonce, 'update_mes_informations_nonce')) {
        wp_send_json_error(array('message' => 'Sécurité non valide.'));
    }

    // Vérifier si l'utilisateur est connecté
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Vous devez être connecté pour effectuer cette action.'));
    }

    // Récupérer l'ID de l'utilisateur connecté
    $user_id = get_current_user_id();

    // Vérifier les capacités de l'utilisateur
    if (!current_user_can('edit_user', $user_id)) {
        wp_send_json_error(array('message' => 'Vous n\'avez pas les permissions nécessaires pour effectuer cette action.'));
    }

    // Récupérer et sécuriser les données du formulaire
    $first_name  = isset($_POST['firstname']) ? sanitize_text_field($_POST['firstname']) : '';
    $last_name   = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $email       = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $telephone   = isset($_POST['telephone']) ? sanitize_text_field($_POST['telephone']) : '';
    $address     = isset($_POST['address']) ? sanitize_text_field($_POST['address']) : '';
    $city        = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
    $postalcode  = isset($_POST['postalcode']) ? sanitize_text_field($_POST['postalcode']) : '';
    $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';

    // Validation supplémentaire
    if (!is_email($email)) {
        wp_send_json_error(array('message' => 'Adresse email invalide.'));
    }

    $existing_user = email_exists($email);
    if ($existing_user && $existing_user != $user_id) {
        wp_send_json_error(array('message' => 'Cette adresse email est déjà utilisée.'));
    }

    // Mettre à jour les informations de base de l'utilisateur sans toucher à 'user_login'
    $update_user = wp_update_user(array(
        'ID'           => $user_id,
        'user_email'   => $email,
        'first_name'   => $first_name,
        'last_name'    => $last_name,
        'display_name' => $first_name . ' ' . $last_name,
    ));

    if (is_wp_error($update_user)) {
        wp_send_json_error(array('message' => 'Erreur lors de la mise à jour de l\'utilisateur.'));
    }

    // Vérifier si la ville a changé
    $old_city = get_user_meta($user_id, 'city', true);
    $latitude = get_user_meta($user_id, 'latitude', true);
    $longitude = get_user_meta($user_id, 'longitude', true);

    if ($city !== $old_city) {
        // Récupérer les nouvelles coordonnées GPS en fonction de la ville
        $city_encoded = urlencode($city);
        $api_url = "https://nominatim.openstreetmap.org/search?q={$city_encoded}&format=json&limit=1";

        $response = wp_remote_get($api_url, ['timeout' => 10]);

        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                $latitude = floatval($data[0]['lat']);
                $longitude = floatval($data[0]['lon']);
            }
        }
    }

    // Mettre à jour les métadonnées utilisateur
    update_user_meta($user_id, 'telephone', $telephone);
    update_user_meta($user_id, 'address', $address);
    update_user_meta($user_id, 'city', $city);
    update_user_meta($user_id, 'postalcode', $postalcode);
    update_user_meta($user_id, 'description', $description);
    update_user_meta($user_id, 'latitude', $latitude);
    update_user_meta($user_id, 'longitude', $longitude);

    // Gérer le téléchargement de la photo de profil
    $profile_image_url = '';
    if (isset($_FILES['profile_image']) && !empty($_FILES['profile_image']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');

        $uploadedfile = $_FILES['profile_image'];
        $upload_overrides = array('test_form' => false);

        // Limiter les types de fichiers autorisés
        $allowed_mime_types = array('image/jpeg', 'image/png', 'image/jpg');
        $file_info = wp_check_filetype($uploadedfile['name']);
        $file_mime_type = $file_info['type'];

        if (!in_array($file_mime_type, $allowed_mime_types)) {
            wp_send_json_error(array('message' => 'Type de fichier non autorisé. Veuillez télécharger une image JPG ou PNG.'));
        }

        // Limiter la taille des fichiers (2MB)
        $max_size = 2 * 1024 * 1024;
        if ($uploadedfile['size'] > $max_size) {
            wp_send_json_error(array('message' => 'La taille du fichier dépasse la limite autorisée de 2MB.'));
        }

        // Téléchargement du fichier
        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

        if ($movefile && !isset($movefile['error'])) {
            update_user_meta($user_id, 'profile_image', esc_url($movefile['url']));
            $profile_image_url = esc_url($movefile['url']);
        } else {
            wp_send_json_error(array('message' => 'Erreur lors du téléchargement de la photo de profil.'));
        }
    }

    // Envoyer la réponse de succès
    wp_send_json_success(array(
        'message'           => 'Vos informations ont été mises à jour avec succès.',
        'profile_image_url' => $profile_image_url,
        'latitude'          => $latitude,
        'longitude'         => $longitude,
    ));
}
add_action('wp_ajax_update_mes_informations', 'handle_update_mes_informations_ajax');

// Il est préférable de ne pas ajouter 'nopriv' si seuls les utilisateurs connectés peuvent modifier leurs informations
// add_action( 'wp_ajax_nopriv_update_mes_informations', 'handle_update_mes_informations_ajax' );


/// Fomulaire Infos Professionnelles
function update_pro_infos_callback() {
    // Vérification du nonce pour la sécurité
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'update_pro_infos_nonce')) {
        wp_send_json_error(['message' => 'Nonce invalide.']);
        exit;
    }

    $user_id = get_current_user_id();

    if (!$user_id) {
        wp_send_json_error(['message' => 'Utilisateur non connecté.']);
    }

    // Validation des données reçues
    $diplome = sanitize_text_field($_POST['diplome_principal']);
    $experience = intval($_POST['annees_experience']);
    $budget = floatval($_POST['budget_moyen_chantiers']);
    $motivation = sanitize_textarea_field($_POST['motivation_metier']);
    $architect_types = isset($_POST['architecte_type']) ? array_map('sanitize_text_field', $_POST['architecte_type']) : [];

    // Mise à jour des métadonnées de l'utilisateur
    update_user_meta($user_id, 'diplome_principal', $diplome);
    update_user_meta($user_id, 'annees_experience', $experience);
    update_user_meta($user_id, 'budget_moyen_chantiers', $budget);
    update_user_meta($user_id, 'motivation_metier', $motivation);
    update_user_meta($user_id, 'architecte_type', $architect_types);

    wp_send_json_success(['message' => 'Informations mises à jour avec succès !']);
}
add_action('wp_ajax_update_pro_infos', 'update_pro_infos_callback');


//Creation de la table wp_userinfos
function create_userinfos_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'userinfos';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) {
        return;
    }

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) UNSIGNED NOT NULL UNIQUE,
        user_type VARCHAR(50) NOT NULL,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        email VARCHAR(255) NOT NULL,
        telephone VARCHAR(20) NULL,
        address TEXT NULL,
        city VARCHAR(100) NULL,
        postalcode VARCHAR(20) NULL,
        description TEXT NULL,
        latitude DECIMAL(10,8) NULL,
        longitude DECIMAL(11,8) NULL,
        diplome_principal VARCHAR(255) NULL,
        annees_experience INT NULL,
        budget_moyen_chantiers FLOAT NULL,
        motivation_metier TEXT NULL,
        architecte_type TEXT NULL, -- Stocké en JSON
        is_page_public TINYINT(1) NOT NULL DEFAULT 0, -- BOOLÉEN
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->users}(ID) ON DELETE CASCADE
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
add_action('after_setup_theme', 'create_userinfos_table');





//Quand user_meta est mis à jour, synchroniser wp_userinfos
function sync_user_meta_with_table($meta_id, $user_id, $meta_key, $meta_value) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'userinfos';

    $allowed_keys = [
        'user_type', 'first_name', 'last_name', 'email', 'telephone', 'address', 
        'city', 'postalcode', 'description', 'latitude', 'longitude', 
        'diplome_principal', 'annees_experience', 'budget_moyen_chantiers', 'motivation_metier', 'architecte_type', 'is_page_public'
    ];

    if (in_array($meta_key, $allowed_keys)) {
        if ($meta_key === 'architecte_type') {
            $meta_value = json_encode($meta_value);
        }

        // Conversion du booléen pour MySQL
        if ($meta_key === 'is_page_public') {
            $meta_value = ($meta_value == '1') ? 1 : 0;
        }

        $wpdb->update(
            $table_name,
            [$meta_key => $meta_value],
            ['user_id' => $user_id],
            ['%s'],
            ['%d']
        );
    }
}
add_action('updated_user_meta', 'sync_user_meta_with_table', 10, 4);


//Quand wp_userinfos est modifié, mettre à jour user_meta
function sync_user_meta_from_userinfos($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'userinfos';

    $user_info = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $user_id));

    if ($user_info) {
        $meta_keys = [
            'user_type', 'first_name', 'last_name', 'email', 'telephone', 'address', 
            'city', 'postalcode', 'description', 'latitude', 'longitude', 
            'diplome_principal', 'annees_experience', 'budget_moyen_chantiers', 'motivation_metier', 'architecte_type', 'is_page_public'
        ];

        foreach ($meta_keys as $key) {
            $value = $user_info->$key;

            if ($key === 'architecte_type') {
                $value = json_decode($value, true);
            }

            // Convertir les valeurs MySQL en booléen
            if ($key === 'is_page_public') {
                $value = ($value == 1) ? true : false;
            }

            update_user_meta($user_id, $key, $value);
        }
    }
}



function sync_user_meta_on_page_load() {
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        sync_user_meta_from_userinfos($user_id);
    }
}
add_action('wp', 'sync_user_meta_on_page_load');


function update_wp_userinfos_from_meta() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'userinfos';

    // Récupérer tous les utilisateurs
    $users = get_users();

    foreach ($users as $user) {
        $user_id = $user->ID;

        // Récupérer les métadonnées utilisateur
        $user_type   = get_user_meta($user_id, 'user_type', true);
        $first_name  = get_user_meta($user_id, 'first_name', true);
        $last_name   = get_user_meta($user_id, 'last_name', true);
        $email       = get_userdata($user_id)->user_email;
        $telephone   = get_user_meta($user_id, 'telephone', true);
        $address     = get_user_meta($user_id, 'address', true);
        $city        = get_user_meta($user_id, 'city', true);
        $postalcode  = get_user_meta($user_id, 'postalcode', true);
        $description = get_user_meta($user_id, 'description', true);
        $latitude    = get_user_meta($user_id, 'latitude', true);
        $longitude   = get_user_meta($user_id, 'longitude', true);
        $diplome_principal     = get_user_meta($user_id, 'diplome_principal', true);
        $annees_experience  = get_user_meta($user_id, 'annees_experience', true);
        $budget_moyen_chantiers      = get_user_meta($user_id, 'budget_moyen_chantiers', true);
        $motivation_metier  = get_user_meta($user_id, 'motivation_metier', true);
        $architecte_type = get_user_meta($user_id, 'architecte_type', true);
        $architecte_type = json_encode($architecte_type); // Stocké en JSON

        // Récupérer `is_page_public` en tant que booléen
        $is_page_public = get_user_meta($user_id, 'is_page_public', true);
        $is_page_public = ($is_page_public == '1') ? 1 : 0; // Convertir pour MySQL

        // Vérifier si l'utilisateur existe déjà dans `wp_userinfos`
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE user_id = %d", $user_id));

        if ($exists) {
            // Mettre à jour l'utilisateur existant
            $wpdb->update(
                $table_name,
                [
                    'user_type'   => $user_type,
                    'first_name'  => $first_name,
                    'last_name'   => $last_name,
                    'email'       => $email,
                    'telephone'   => $telephone,
                    'address'     => $address,
                    'city'        => $city,
                    'postalcode'  => $postalcode,
                    'description' => $description,
                    'latitude'    => $latitude,
                    'longitude'   => $longitude,
                    'diplome_principal'     => $diplome_principal,
                    'annees_experience'  => $annees_experience,
                    'budget_moyen_chantiers'      => $budget_moyen_chantiers,
                    'motivation_metier'  => $motivation_metier,
                    'architecte_type' => $architecte_type,
                    'is_page_public'  => $is_page_public
                ],
                ['user_id' => $user_id],
                ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%s', '%d', '%f', '%s', '%s', '%d'],
                ['%d']
            );
        } else {
            // Insérer un nouvel utilisateur
            $wpdb->insert(
                $table_name,
                [
                    'user_id'     => $user_id,
                    'user_type'   => $user_type,
                    'first_name'  => $first_name,
                    'last_name'   => $last_name,
                    'email'       => $email,
                    'telephone'   => $telephone,
                    'address'     => $address,
                    'city'        => $city,
                    'postalcode'  => $postalcode,
                    'description' => $description,
                    'latitude'    => $latitude,
                    'longitude'   => $longitude,
                    'diplome_principal'     => $diplome_principal,
                    'annees_experience'  => $annees_experience,
                    'budget_moyen_chantiers'      => $budget_moyen_chantiers,
                    'motivation_metier'  => $motivation_metier,
                    'architecte_type' => $architecte_type,
                    'is_page_public'  => $is_page_public
                ],
                ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%s', '%d', '%f', '%s', '%s', '%d']
            );
        }
    }

    return "Mise à jour des données terminée !";
}

// Exécuter la mise à jour une seule fois
update_wp_userinfos_from_meta();