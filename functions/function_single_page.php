<?php

// Ajout de l'action AJAX
function enqueue_profil_page_scripts() {
    if (is_page_template('templates/single-professionnel.php')) {

        // Inclure jQuery
        wp_enqueue_script('jquery');

        // Inclure SweetAlert2
        wp_enqueue_script('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', [], '11', true);

        // Charger Splide via CDN
        wp_enqueue_style('splide-css', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.3/dist/css/splide.min.css', [], '4.1.3');
        wp_enqueue_script('splide-js', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.3/dist/js/splide.min.js', [], '4.1.3', true);


        // Charger le fichier JS personnalisé principal
        wp_enqueue_script(
            'carousel-profil-page-js',
            get_template_directory_uri() . '/assets/src/js/ajax/modalRealisation.js',
            ['jquery', 'sweetalert2','splide-js'],
            null,
            true
        );

        wp_enqueue_script(
            'review-js',
            get_template_directory_uri() . '/assets/src/js/ajax/review.js',
            ['jquery', 'sweetalert2','splide-js'],
            null,
            true
        );

        // Passer l'URL AJAX et l'ID utilisateur aux deux scripts
        wp_localize_script('carousel-profil-page-js', 'ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'user_id' => get_current_user_id()
        ]);

        wp_localize_script('review-js', 'ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'user_id' => get_current_user_id(),
            'nonce' => wp_create_nonce('manage_review_nonce')
        ]);

    }
}
add_action('wp_enqueue_scripts', 'enqueue_profil_page_scripts');


//////////////////////////////////////// Creation de la table pour les avis pro si ce n'est pas fais
function create_reviews_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'professional_reviews'; // Utiliser le bon préfixe
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        professional_id BIGINT UNSIGNED NOT NULL,
        review_text TEXT NOT NULL,
        rating TINYINT(1) UNSIGNED DEFAULT 5, 
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_review (user_id, professional_id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);

}
add_action('init', 'create_reviews_table');



//////////////////////////////////////////////////////    Ajout d'un avis

function manage_review_function() {
    check_ajax_referer('manage_review_nonce', 'security');

    error_log("Données reçues en AJAX : " . print_r($_POST, true)); // Debug pour voir la requête envoyée

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Vous devez être connecté pour donner un avis.']);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'professional_reviews';

    $user_id = get_current_user_id();
    $professional_id = isset($_POST['professional_id']) ? intval($_POST['professional_id']) : 0;
    $review_text = isset($_POST['review_text']) ? sanitize_textarea_field($_POST['review_text']) : '';
    $action_type = isset($_POST['action_type']) ? sanitize_text_field($_POST['action_type']) : '';
    $review_id = isset($_POST['review_id']) ? intval($_POST['review_id']) : 0;

    // Vérifier si l'action et l'ID professionnel sont valides
    if ($professional_id <= 0 || empty($action_type)) {
        wp_send_json_error(['message' => 'ID professionnel ou action invalide']);
    }

    // Vérifier si l'utilisateur a déjà un avis
    $existing_review = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d AND professional_id = %d", $user_id, $professional_id)
    );

    if ($action_type === 'add' || $action_type === 'update') {
        if (empty($review_text)) {
            wp_send_json_error(['message' => 'Le commentaire ne peut pas être vide.']);
        }

        if ($existing_review) {
            $wpdb->update(
                $table_name,
                ['review_text' => $review_text, 'updated_at' => current_time('mysql')],
                ['id' => $existing_review->id, 'user_id' => $user_id]
            );
            wp_send_json_success(['message' => 'Votre avis a été mis à jour !']);
        } else {
            $wpdb->insert(
                $table_name,
                ['user_id' => $user_id, 'professional_id' => $professional_id, 'review_text' => $review_text]
            );
            wp_send_json_success(['message' => 'Votre avis a été ajouté avec succès !']);
        }
    } elseif ($action_type === 'delete') {
        if ($review_id > 0) {
            $review_to_delete = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d AND user_id = %d", $review_id, $user_id)
            );

            if ($review_to_delete) {
                $wpdb->delete($table_name, ['id' => $review_id, 'user_id' => $user_id]);
                wp_send_json_success(['message' => 'Votre avis a été supprimé.']);
            } else {
                wp_send_json_error(['message' => 'Aucun avis trouvé à supprimer.']);
            }
        } else {
            wp_send_json_error(['message' => 'ID de l\'avis invalide.']);
        }
    } else {
        wp_send_json_error(['message' => 'Action non reconnue.']);
    }

    wp_die();
}


add_action('wp_ajax_manage_review', 'manage_review_function');
add_action('wp_ajax_nopriv_manage_review', 'manage_review_function');


//////////////////////////////////////////////////////////////////////    Récuperer un avis 
add_action('wp_ajax_get_user_review', 'get_user_review_function');
add_action('wp_ajax_nopriv_get_user_review', 'get_user_review_function');

function get_user_review_function() {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Vous devez être connecté pour voir votre avis.']);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'professional_reviews';

    $user_id = get_current_user_id();
    $professional_id = isset($_GET['professional_id']) ? intval($_GET['professional_id']) : 0;

    if ($professional_id <= 0) {
        wp_send_json_error(['message' => 'ID invalide']);
    }

    $existing_review = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d AND professional_id = %d", $user_id, $professional_id)
    );

    error_log("Avis retourné à AJAX : " . print_r($existing_review, true));

    if ($existing_review) {
        wp_send_json_success(['review' => !empty($existing_review->review_text) ? $existing_review->review_text : ""]);
    } else {
        wp_send_json_success(['review' => ""]); // Retourne une chaîne vide
    }

    wp_die();
}


/// Voir information du projet quand on clique sur l'image de celui-ci sur la single_professionnel.php
function get_project_details() {
    // if (!is_user_logged_in()) {
    //     wp_send_json_error(['message' => 'Vous devez être connecté.']);
    //     return;
    // }

    $user_id = intval($_POST['user_id']);
    $project_id = intval($_POST['project_id']);
    $projects = get_user_meta($user_id, 'recent_projects', true);

    if (isset($projects[$project_id])) {
        wp_send_json_success(['project' => $projects[$project_id]]);
    } else {
        wp_send_json_error(['message' => 'Projet introuvable.']);
    }
}
add_action('wp_ajax_get_project_details', 'get_project_details');
add_action('wp_ajax_nopriv_get_project_details', 'get_project_details');