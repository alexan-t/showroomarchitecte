<?php 

// Fonction pour traiter la soumission du formulaire via AJAX 
// Fonction Updates info user
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
    // $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';

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
    // update_user_meta($user_id, 'description', $description);
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
    $architect_type = isset($_POST['architecte_type']) ? sanitize_text_field($_POST['architecte_type']) : '';

    // Mise à jour des métadonnées de l'utilisateur
    update_user_meta($user_id, 'diplome_principal', $diplome);
    update_user_meta($user_id, 'annees_experience', $experience);
    update_user_meta($user_id, 'budget_moyen_chantiers', $budget);
    update_user_meta($user_id, 'motivation_metier', $motivation);
    update_user_meta($user_id, 'architecte_type', $architect_type);

    wp_send_json_success(['message' => 'Informations mises à jour avec succès !']);
}
add_action('wp_ajax_update_pro_infos', 'update_pro_infos_callback');