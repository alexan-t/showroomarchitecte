<?php

// Fonction pour traiter la soumission du formulaire via AJAX
function handle_update_mes_informations_ajax() {
    // Vérifier le nonce pour la sécurité
    $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
    if ( ! wp_verify_nonce( $nonce, 'update_mes_informations_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Sécurité non valide.' ) );
    }

    // Vérifier si l'utilisateur est connecté
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( array( 'message' => 'Vous devez être connecté pour effectuer cette action.' ) );
    }

    // Récupérer l'ID de l'utilisateur connecté
    $user_id = get_current_user_id();

    // Vérifier les capacités de l'utilisateur
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        wp_send_json_error( array( 'message' => 'Vous n\'avez pas les permissions nécessaires pour effectuer cette action.' ) );
    }

    // Récupérer et sanitizer les données du formulaire
    $first_name   = isset($_POST['firstname']) ? sanitize_text_field( $_POST['firstname'] ) : '';
    $last_name    = isset($_POST['name']) ? sanitize_text_field( $_POST['name'] ) : '';
    $email        = isset($_POST['email']) ? sanitize_email( $_POST['email'] ) : '';
    $telephone    = isset($_POST['telephone']) ? sanitize_text_field( $_POST['telephone'] ) : '';
    $adress       = isset($_POST['adress']) ? sanitize_text_field( $_POST['adress'] ) : '';
    $city         = isset($_POST['city']) ? sanitize_text_field( $_POST['city'] ) : '';
    $postalcode   = isset($_POST['postalcode']) ? sanitize_text_field( $_POST['postalcode'] ) : '';
    $description  = isset($_POST['description']) ? sanitize_textarea_field( $_POST['description'] ) : '';

    // Validation supplémentaire
    if ( ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => 'Adresse email invalide.' ) );
    }

    if ( email_exists( $email ) && email_exists( $email ) != $user_id ) {
        wp_send_json_error( array( 'message' => 'Cette adresse email est déjà utilisée.' ) );
    }

    // Mettre à jour les informations de base de l'utilisateur sans toucher à 'user_login'
    $update_user = wp_update_user( array(
        'ID'         => $user_id,
        'user_email' => $email,
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'display_name' => $first_name . ' ' . $last_name, // Optionnel : définir le nom affiché
    ) );

    if ( is_wp_error( $update_user ) ) {
        wp_send_json_error( array( 'message' => 'Erreur lors de la mise à jour de l\'utilisateur.' ) );
    }

    // Mettre à jour les métadonnées utilisateur
    update_user_meta( $user_id, 'telephone', $telephone );
    update_user_meta( $user_id, 'adress', $adress );
    update_user_meta( $user_id, 'city', $city );
    update_user_meta( $user_id, 'postalcode', $postalcode );
    update_user_meta( $user_id, 'description', $description );

    // Gérer le téléchargement de la photo de profil
    $profile_image_url = '';
    if ( isset($_FILES['profile_image']) && ! empty($_FILES['profile_image']['name']) ) {
        // Inclure les fichiers nécessaires pour gérer les téléchargements
        require_once( ABSPATH . 'wp-admin/includes/file.php' );

        $uploadedfile = $_FILES['profile_image'];

        // Configuration des paramètres de téléchargement
        $upload_overrides = array( 'test_form' => false );

        // Limiter les types de fichiers autorisés
        $allowed_mime_types = array( 'image/jpeg', 'image/png', 'image/jpg' );
        $file_mime_type = mime_content_type( $uploadedfile['tmp_name'] );

        if ( ! in_array( $file_mime_type, $allowed_mime_types ) ) {
            wp_send_json_error( array( 'message' => 'Type de fichier non autorisé. Veuillez télécharger une image JPG ou PNG.' ) );
        }

        // Limiter la taille des fichiers (2MB par exemple)
        $max_size = 2 * 1024 * 1024; // 2MB
        if ( $uploadedfile['size'] > $max_size ) {
            wp_send_json_error( array( 'message' => 'La taille du fichier dépasse la limite autorisée de 2MB.' ) );
        }

        // Téléchargement du fichier
        $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

        if ( $movefile && ! isset( $movefile['error'] ) ) {
            // Mettre à jour la métadonnée avec l'URL de l'image
            update_user_meta( $user_id, 'profile_image', esc_url( $movefile['url'] ) );
            $profile_image_url = esc_url( $movefile['url'] );
        } else {
            wp_send_json_error( array( 'message' => 'Erreur lors du téléchargement de la photo de profil.' ) );
        }
    }

    // Envoyer la réponse de succès avec éventuellement l'URL de la nouvelle image de profil
    wp_send_json_success( array(
        'message'           => 'Vos informations ont été mises à jour avec succès.',
        'profile_image_url' => $profile_image_url
    ) );
}
add_action( 'wp_ajax_update_mes_informations', 'handle_update_mes_informations_ajax' );
// Il est préférable de ne pas ajouter 'nopriv' si seuls les utilisateurs connectés peuvent modifier leurs informations
// add_action( 'wp_ajax_nopriv_update_mes_informations', 'handle_update_mes_informations_ajax' );


// Fonction pour inclure le JavaScript AJAX
function my_dashboard_scripts() {
    if (is_page_template('templates/dashboard.php')) { // Remplacez par le chemin correct de votre template
        // Enregistrer SweetAlert2 depuis un CDN
        wp_register_script(
            'sweetalert2',
            'https://cdn.jsdelivr.net/npm/sweetalert2@11', // Vérifiez la version actuelle sur le CDN
            array(),
            '11.0.0', // Remplacez par la version appropriée
            true
        );

        // Enregistrer votre script personnalisé et définir SweetAlert2 comme dépendance
        wp_enqueue_script(
            'form-updateInfos',
            get_template_directory_uri() . '/assets/src/js/ajax/formUpdateInfos.js',
            array('jquery', 'sweetalert2'), // Dépendances
            null,
            true
        );

        // Localiser les variables nécessaires
        wp_localize_script('form-updateInfos', 'formUpdateInfos', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('update_mes_informations_nonce'),
            'default_image' => get_template_directory_uri() . '/assets/img/blue-circle.svg',
        ));
        
        wp_localize_script('form-update-infos', 'ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php')
        ]);
    }
}
add_action('wp_enqueue_scripts', 'my_dashboard_scripts');




//Fonction pour archiver un projet
function archive_project_callback() {
    if (!is_user_logged_in()) {
        wp_send_json_error("Vous devez être connecté pour archiver un projet.");
        wp_die();
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'projects';

    $project_id = intval($_POST['project_id']);
    $user_id = get_current_user_id();

    // Vérifier si le projet appartient à l'utilisateur connecté
    $project = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d AND user_id = %d",
            $project_id,
            $user_id
        )
    );

    if (!$project) {
        wp_send_json_error("Projet introuvable ou non autorisé.");
        wp_die();
    }

    // Mettre à jour le statut et la date de fermeture
    $updated = $wpdb->update(
        $table_name,
        [
            'status' => 'archived',
            'closed_at' => current_time('mysql')
        ],
        ['id' => $project_id],
        ['%s', '%s'],
        ['%d']
    );

    if ($updated === false) {
        wp_send_json_error("Erreur lors de l'archivage du projet.");
    } else {
        wp_send_json_success("Projet archivé avec succès !");
    }

    wp_die();
}
add_action('wp_ajax_archive_project', 'archive_project_callback');


//Fonction pour Supprimer un projet définitivement 
function delete_project_callback() {
    if (!is_user_logged_in()) {
        wp_send_json_error("Vous devez être connecté pour supprimer un projet.");
        wp_die();
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'projects';

    $project_id = intval($_POST['project_id']);
    $user_id = get_current_user_id();

    // Vérifier si le projet appartient à l'utilisateur connecté
    $project = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d AND user_id = %d",
            $project_id,
            $user_id
        )
    );

    if (!$project) {
        wp_send_json_error("Projet introuvable ou non autorisé.");
        wp_die();
    }

    // Supprimer le projet
    $deleted = $wpdb->delete(
        $table_name,
        ['id' => $project_id],
        ['%d']
    );

    if ($deleted === false) {
        wp_send_json_error("Erreur lors de la suppression du projet.");
    } else {
        wp_send_json_success("Projet supprimé avec succès !");
    }

    wp_die();
}
add_action('wp_ajax_delete_project', 'delete_project_callback');



/// Fomulaire Infos Professionnelles
function update_pro_infos_callback() {
    // Vérification du nonce pour la sécurité
    check_ajax_referer('update_pro_infos', 'security');

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


add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('form-update-infos', get_template_directory_uri() . '/js/formUpdateInfos.js', ['jquery'], null, true);

    // Définition de la variable ajaxurl
    wp_localize_script('form-update-infos', 'ajax_object', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);
});





?>