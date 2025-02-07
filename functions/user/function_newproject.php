<?php

function enqueue_form_scripts() {
    // Charger SweetAlert2
    wp_enqueue_script(
        'sweetalert2',
        'https://cdn.jsdelivr.net/npm/sweetalert2@11',
        [],
        '11.0.0',
        true
    );

    // Charger le script principal
    wp_enqueue_script(
        'form-new-project',
        get_template_directory_uri() . '/assets/src/js/ajax/formNewProject.js',
        ['jquery', 'sweetalert2'], // Dépendance sur SweetAlert2
        null,
        true
    );

    // Passer l'URL AJAX à votre script principal
    wp_localize_script('form-new-project', 'ajaxObject', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
    ]);
}
add_action('wp_enqueue_scripts', 'enqueue_form_scripts');


function load_form_step_callback() {
    if (isset($_POST['step'])) {
        $step = intval($_POST['step']);
        
        // Inclure le fichier form_steps.php pour gérer les étapes dynamiquement
        $file_path = get_template_directory() . '/templates/parts/new_project-form/form_steps.php';
        
        if (file_exists($file_path)) {
            include $file_path;
        } else {
            echo 'Fichier introuvable.';
        }
    } else {
        echo 'Étape non définie.';
    }

    wp_die(); // Finir correctement pour WordPress
}
add_action('wp_ajax_load_form_step', 'load_form_step_callback');
add_action('wp_ajax_nopriv_load_form_step', 'load_form_step_callback');


function create_projects_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'projects';

    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            search VARCHAR(255) NOT NULL,
            property VARCHAR(255) NOT NULL,
            proprietaire VARCHAR(255) NOT NULL,
            projet VARCHAR(255) NOT NULL,
            city VARCHAR(255) NOT NULL,
            budget VARCHAR(255) NOT NULL,
            total_surface VARCHAR(255) NOT NULL,
            work_surface VARCHAR(255) NOT NULL,
            project_name VARCHAR(255) NOT NULL,
            project_description TEXT NOT NULL,
            needs TEXT NULL,
            status ENUM('active', 'archived') NOT NULL DEFAULT 'active',
            closed_at DATETIME NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID) ON DELETE CASCADE
        ) $charset_collate;";
        
        dbDelta($sql);

        // Loguer la création de la table
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {
            error_log("Table '{$table_name}' créée avec succès.");
        } else {
            error_log("Échec de la création de la table '{$table_name}'.");
        }
    }
}


// Appeler la fonction lors de l'activation du thème ou d'un plugin
add_action('after_switch_theme', 'create_projects_table'); // Pour les thèmes


function submit_project_form_callback() {
    session_start();

    // Vérifier si l'utilisateur est connecté
    if (!is_user_logged_in()) {
        wp_send_json_error("Vous devez être connecté pour créer un projet.");
        wp_die();
    }

    // Obtenir l'ID de l'utilisateur connecté
    $user_id = get_current_user_id();

    // Récupérer les données de la session
    $session_data = $_SESSION['form_data'] ?? [];

    // Ajouter les données soumises de l'étape 3
    $submitted_data = [
        'city' => sanitize_text_field($_POST['city'] ?? ''),
        'total_surface' => sanitize_text_field($_POST['total_surface'] ?? ''),
        'work_surface' => sanitize_text_field($_POST['work_surface'] ?? ''),
        'budget' => sanitize_text_field($_POST['budget'] ?? ''),
        'project_name' => sanitize_text_field($_POST['project_name'] ?? ''),
        'project_description' => sanitize_textarea_field($_POST['project_description'] ?? ''),
        'needs' => maybe_serialize($_POST['needs'] ?? []),
    ];

    // Fusionner les données
    $form_data = array_merge($session_data, $submitted_data);

    // Validation des champs obligatoires
    $required_fields = ['search', 'property','proprietaire','projet', 'city', 'total_surface', 'work_surface', 'budget', 'project_name', 'project_description'];
    foreach ($required_fields as $field) {
        if (empty($form_data[$field])) {
            wp_send_json_error("Le champ $field est requis.");
            wp_die();
        }
    }

    // Sauvegarder dans la base de données
    global $wpdb;
    $table_name = $wpdb->prefix . 'projects';
    
    $inserted = $wpdb->insert(
        $table_name,
        [
            'user_id' => $user_id,
            'search' => $form_data['search'],
            'property' => $form_data['property'],
            'proprietaire' => $form_data['proprietaire'],
            'projet' => $form_data['projet'],
            'city' => $form_data['city'],
            'budget' => $form_data['budget'],
            'total_surface' => $form_data['total_surface'],
            'work_surface' => $form_data['work_surface'],
            'project_name' => $form_data['project_name'],
            'project_description' => $form_data['project_description'],
            'needs' => $form_data['needs'],
        ],
        [
            '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'
        ]
    );
    
    
    
    if ($inserted === false) {
        error_log("Erreur SQL : " . $wpdb->last_error);
        error_log("Requête : " . $wpdb->last_query);
    }
    
    
    

    if ($inserted) {
        wp_send_json_success("Projet enregistré avec succès !");
    } else {
        wp_send_json_error("Une erreur est survenue lors de l'enregistrement du projet.");
    }

    wp_die();
}
add_action('wp_ajax_submit_project_form', 'submit_project_form_callback');
add_action('wp_ajax_nopriv_submit_project_form', 'submit_project_form_callback');

// Debug session
add_action('wp_ajax_debug_session', function() {
    session_start();
    wp_send_json($_SESSION['form_data'] ?? []);
});





///// SAUVEGARDER LE PROJET MODIFIER
function update_project_callback() {
    if (!is_user_logged_in()) {
        wp_send_json_error("Vous devez être connecté pour modifier un projet.");
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

    // Valider les données
    $updated_data = [
        'search' => sanitize_text_field($_POST['search']),
        'property' => sanitize_text_field($_POST['property']),
        'proprietaire' => sanitize_text_field($_POST['proprietaire']),
        'projet' => sanitize_text_field($_POST['projet']),
        'project_name' => sanitize_text_field($_POST['project_name']),
        'total_surface' => sanitize_text_field($_POST['total_surface']),
        'work_surface' => sanitize_text_field($_POST['work_surface']),
        'city' => sanitize_text_field($_POST['city']),
        'budget' => sanitize_text_field($_POST['budget']),
        'project_description' => sanitize_textarea_field($_POST['project_description']),
        'needs' => maybe_serialize($_POST['needs'] ?? []),
    ];

    // Mettre à jour la base de données
    $updated = $wpdb->update(
        $table_name,
        $updated_data,
        ['id' => $project_id],
        ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'],
        ['%d']
    );

    if ($updated === false) {
        wp_send_json_error("Erreur lors de la mise à jour du projet.");
    } else {
        wp_send_json_success("Projet mis à jour avec succès !");
    }

    wp_die();
}
add_action('wp_ajax_update_project', 'update_project_callback');