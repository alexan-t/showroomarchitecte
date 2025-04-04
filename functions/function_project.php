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


//Ajout de fichier au projet client
/**
 * Gérer l'upload des fichiers du formulaire projet (jpg, jpeg, pdf uniquement).
 *
 * @param array $files Tableau provenant de $_FILES['project_files']
 * @return array Résultat contenant soit les URLs, soit des erreurs.
 */
function handle_project_file_uploads($files) {
    $results = [
        'success' => [],
        'errors' => []
    ];

    if (empty($files['name'][0])) {
        $results['errors'][] = 'Aucun fichier envoyé.';
        return $results;
    }

    $allowed_mime_types = ['image/jpeg', 'application/pdf'];
    $allowed_extensions = ['jpg', 'jpeg', 'pdf'];

    $upload_dir = wp_upload_dir();

    foreach ($files['name'] as $index => $name) {
        $tmp_name = $files['tmp_name'][$index];
        $type     = $files['type'][$index];
        $error    = $files['error'][$index];
        $size     = $files['size'][$index];

        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $safe_name = sanitize_file_name($name);

        if ($error !== UPLOAD_ERR_OK) {
            $results['errors'][] = "Erreur avec le fichier : $name";
            continue;
        }

        if (!in_array($type, $allowed_mime_types) || !in_array($extension, $allowed_extensions)) {
            $results['errors'][] = "Type de fichier non autorisé : $name";
            continue;
        }

        $destination = $upload_dir['path'] . '/' . $safe_name;

        // Ajouter suffixe si fichier déjà présent
        $counter = 1;
        while (file_exists($destination)) {
            $safe_name = pathinfo($name, PATHINFO_FILENAME) . "-$counter." . $extension;
            $destination = $upload_dir['path'] . '/' . $safe_name;
            $counter++;
        }

        if (move_uploaded_file($tmp_name, $destination)) {
            $results['success'][] = $upload_dir['url'] . '/' . $safe_name;
        } else {
            $results['errors'][] = "Impossible d'enregistrer le fichier : $name";
        }
    }

    return $results;
}




function create_projects_table() {
    global $wpdb;

    $table_name = esc_sql($wpdb->prefix . 'projects');

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
            latitude FLOAT NULL,
            longitude FLOAT NULL,
            budget VARCHAR(255) NOT NULL,
            total_surface VARCHAR(255) NOT NULL,
            work_surface VARCHAR(255) NOT NULL,
            project_name VARCHAR(255) NOT NULL,
            project_description TEXT NOT NULL,
            needs TEXT NULL,
            attachments TEXT NULL, 
            status ENUM('active', 'archived') NOT NULL DEFAULT 'active',
            project_start_date DATE NOT NULL,
            closed_at DATETIME NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID) ON DELETE CASCADE
        ) $charset_collate;";
        
        dbDelta($sql);

        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {
            error_log("Table '{$table_name}' créée avec succès.");
        } else {
            error_log("Échec de la création de la table '{$table_name}'.");
        }
    } else {
        // ✅ Ajouter les colonnes manquantes si la table existe déjà

        $columns_to_add = [
            'latitude' => "FLOAT NULL",
            'longitude' => "FLOAT NULL",
            'attachments' => "TEXT NULL",
            'project_start_date' => isset($_POST['project_start_date']) ? sanitize_text_field($_POST['project_start_date']) : date('Y-m-d'),
        ];

        foreach ($columns_to_add as $column => $definition) {
            $exists = $wpdb->get_results("SHOW COLUMNS FROM $table_name LIKE '$column'");
            if (empty($exists)) {
                $wpdb->query("ALTER TABLE $table_name ADD COLUMN $column $definition");
                error_log("Colonne '$column' ajoutée à la table '$table_name'.");
            }
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
        'project_start_date' => sanitize_text_field($_POST['project_start_date'] ?? ''),
    ];

    // Fusionner les données
    $form_data = array_merge($session_data, $submitted_data);

    // Validation des champs obligatoires
    $required_fields = ['search', 'property', 'proprietaire', 'projet', 'city', 'total_surface', 'work_surface', 'budget', 'project_name', 'project_description', 'project_start_date'];
    foreach ($required_fields as $field) {
        if (empty($form_data[$field])) {
            wp_send_json_error("Le champ $field est requis.");
            wp_die();
        }
    }

    // Récupérer la latitude et la longitude en fonction de la ville
    $city = urlencode($form_data['city']);
    $api_url = "https://nominatim.openstreetmap.org/search?q={$city}&format=json&limit=1";

    $response = wp_remote_get($api_url, ['timeout' => 10]);
    $latitude = null;
    $longitude = null;

    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
            $latitude = floatval($data[0]['lat']);
            $longitude = floatval($data[0]['lon']);
        }
    }

    // Gérer l'upload de fichiers .jpg / .jpeg / .pdf
    $uploaded_files_result = [];
    if (!empty($_FILES['project_files'])) {
        $uploaded_files_result = handle_project_file_uploads($_FILES['project_files']);

        if (!empty($uploaded_files_result['errors'])) {
            wp_send_json_error('Erreur lors de l\'upload des fichiers : ' . implode(', ', $uploaded_files_result['errors']));
            wp_die();
        }
    }

    // Sauvegarder dans la base de données
    global $wpdb;
    $table_name = esc_sql($wpdb->prefix . 'projects');

    $inserted = $wpdb->insert(
        $table_name,
        [
            'user_id' => $user_id,
            'search' => $form_data['search'],
            'property' => $form_data['property'],
            'proprietaire' => $form_data['proprietaire'],
            'projet' => $form_data['projet'],
            'city' => $form_data['city'],
            'latitude' => $latitude,
            'longitude' => $longitude,
            'budget' => $form_data['budget'],
            'total_surface' => $form_data['total_surface'],
            'work_surface' => $form_data['work_surface'],
            'project_name' => $form_data['project_name'],
            'project_description' => $form_data['project_description'],
            'needs' => $form_data['needs'],
            'attachments' => maybe_serialize($uploaded_files_result['success']),
            'project_start_date' => $form_data['project_start_date'],
        ],
        [
            '%d', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%s','%s'
        ]
    );

    if ($inserted === false) {
        error_log("Erreur SQL : " . $wpdb->last_error);
        error_log("Requête : " . $wpdb->last_query);
        wp_send_json_error("Une erreur est survenue lors de l'enregistrement du projet.");
    } else {
        wp_send_json_success("Projet enregistré avec succès !");
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
    $table_name = esc_sql($wpdb->prefix . 'projects');

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

    $existing_files = $_POST['existing_files'] ?? [];
    $uploaded_files_result = [];

    if (!empty($_FILES['project_files'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        $uploaded_files_result = handle_project_file_uploads($_FILES['project_files']);
    }

    $all_files = array_merge(
        is_array($existing_files) ? $existing_files : [],
        $uploaded_files_result['success'] ?? []
    );



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
        'project_start_date' => sanitize_text_field($_POST['project_start_date']),
        'project_description' => sanitize_textarea_field($_POST['project_description']),
        'needs' => maybe_serialize($_POST['needs'] ?? []),
        'attachments' => maybe_serialize($all_files),
        'latitude' => $latitude ?? null,
        'longitude' => $longitude ?? null,
    ];
    $format = [
        '%s', // search
        '%s', // property
        '%s', // proprietaire
        '%s', // projet
        '%s', // project_name
        '%s', // total_surface
        '%s', // work_surface
        '%s', // city
        '%s', // budget
        '%s', // project_start_date
        '%s', // project_description
        '%s', // needs
        '%s', // attachments
        '%f', // latitude
        '%f', // longitude
    ];
    
    

    // Vérifier si la ville a changé
    if ($updated_data['city'] !== $project->city) {
        // Récupérer les coordonnées GPS en fonction de la nouvelle ville
        $city = urlencode($updated_data['city']);
        $api_url = "https://nominatim.openstreetmap.org/search?q={$city}&format=json&limit=1";

        $response = wp_remote_get($api_url, ['timeout' => 10]);

        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                $updated_data['latitude'] = floatval($data[0]['lat']);
                $updated_data['longitude'] = floatval($data[0]['lon']);
            }
        }
    }

    // Mettre à jour la base de données
    $updated = $wpdb->update(
        $table_name,
        $updated_data,
        ['id' => $project_id],
        $format,
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