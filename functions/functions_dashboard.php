<?php
// Fonction pour inclure le JavaScript AJAX
function my_dashboard_scripts() {
    if (is_page_template('templates/dashboard.php')) {
        // Enregistrer SweetAlert2 depuis un CDN
        wp_register_script(
            'sweetalert2',
            'https://cdn.jsdelivr.net/npm/sweetalert2@11',
            array(),
            '11.0.0',
            true
        );

        // Enregistrer et charger les scripts personnalisés
        wp_enqueue_script(
            'form-updateInfos',
            get_template_directory_uri() . '/assets/src/js/ajax/formUpdateInfos.js',
            array('jquery', 'sweetalert2'), // Dépendances
            null,
            true
        );

        wp_enqueue_script(
            'form-manage-projects',
            get_template_directory_uri() . '/assets/src/js/ajax/formManageProjects.js',
            array('jquery', 'sweetalert2'), // Dépendances
            null,
            true
        );

        // Fusionner toutes les variables AJAX dans une seule déclaration
        $localized_data = array(
            'ajaxurl'                => admin_url('admin-ajax.php'),
            'nonce'                  => wp_create_nonce('update_mes_informations_nonce'),
            'default_image'          => get_template_directory_uri() . '/assets/img/blue-circle.svg',
            'update_pro_infos_nonce' => wp_create_nonce('update_pro_infos_nonce'),
            'manage_project_nonce'   => wp_create_nonce('manage_project_nonce'),
        );

        // Appliquer `wp_localize_script()` une seule fois pour les deux scripts
        wp_localize_script('form-updateInfos', 'ajax_object', $localized_data);
        wp_localize_script('form-manage-projects', 'ajax_object', $localized_data);
    }
}
add_action('wp_enqueue_scripts', 'my_dashboard_scripts');


// Fonction pour archiver un projet
function archive_project_callback() {
    // Vérification du nonce
    if (!isset($_POST['manage_project_nonce'])) {
        wp_send_json_error("Nonce de sécurité manquant.");
        wp_die();
    }

    // Vérification utilisateur
    if (!is_user_logged_in()) {
        wp_send_json_error("Vous devez être connecté pour archiver un projet.");
        wp_die();
    }

    global $wpdb;
    $table_name = esc_sql($wpdb->prefix . 'projects');

    $project_id = intval($_POST['project_id']);
    $user_id = get_current_user_id();

    if (!$project_id) {
        wp_send_json_error("ID du projet invalide.");
        wp_die();
    }

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

    // Vérifier si le projet est déjà archivé
    if ($project->status === 'archived') {
        wp_send_json_error("Ce projet est déjà archivé.");
        wp_die();
    }

    // Mettre à jour le statut et la date de fermeture
    $updated = $wpdb->update(
        $table_name,
        [
            'status' => sanitize_text_field('archived'),
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
add_action('wp_ajax_manage_archive_project', 'archive_project_callback');

// Fonction pour supprimer un projet définitivement 
function delete_project_callback() {
    

    // Vérification du nonce
    if (!isset($_POST['manage_project_nonce'])) {
        wp_send_json_error("Nonce de sécurité manquant.");
        wp_die();
    }

    $nonce_recu = $_POST['manage_project_nonce'];
    $nonce_attendu = wp_create_nonce('manage_project_nonce');

    if (!wp_verify_nonce($nonce_recu, 'manage_project_nonce')) {
        wp_send_json_error("Nonce de sécurité invalide.");
        wp_die();
    }


    // Vérifier que l'utilisateur est connecté
    if (!is_user_logged_in()) {
        wp_send_json_error("Vous devez être connecté pour supprimer un projet.");
        wp_die();
    }

    global $wpdb;
    $table_name = esc_sql($wpdb->prefix . 'projects');

    $project_id = intval($_POST['project_id']);
    $user_id = get_current_user_id();

    if (!$project_id) {
        wp_send_json_error("ID du projet invalide.");
        wp_die();
    }

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

add_action('wp_ajax_manage_delete_project', 'delete_project_callback');















?>