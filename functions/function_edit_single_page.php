<?php

//Verifie que l'utilisateur qui la modifie edit est l'utilisateur connecté
function showroom_get_edit_profile_url($user_id) {
    // Vérifier que l'utilisateur existe
    if (!get_user_by('ID', $user_id)) {
        return home_url(); // Rediriger vers l'accueil si l'utilisateur n'existe pas
    }

    // Définir directement l'URL de la page d'édition
    $edit_page_url = home_url('/edit-page-profil-pro/'); // URL fixée en dur

    // Retourner l'URL avec l'ID utilisateur en paramètre
    return esc_url($edit_page_url . '?id=' . intval($user_id));
}

//// Rendre la Page de Profil Visible
function update_page_visibility() {
    if (!isset($_POST['user_id']) || !isset($_POST['is_visible'])) {
        wp_send_json_error(['message' => 'Données invalides']);
    }

    $user_id = intval($_POST['user_id']);
    $is_visible = $_POST['is_visible'] == '1' ? '1' : '0';

    // Vérifier que l'utilisateur actuel est bien celui qui modifie son propre profil
    if (get_current_user_id() !== $user_id) {
        wp_send_json_error(['message' => 'Non autorisé']);
    }

    update_user_meta($user_id, 'is_page_public', $is_visible);

    wp_send_json_success(['message' => 'Visibilité mise à jour']);
}
add_action('wp_ajax_update_page_visibility', 'update_page_visibility');


// Ajout de l'action AJAX
function enqueue_form_manage_pro_page_scripts() {
    if (is_page_template('templates/edit_single-professionnel.php')) {

        // Inclure jQuery
        wp_enqueue_script('jquery');

        // Inclure Dropzone.js
        wp_enqueue_style('dropzone-css', 'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css', [], '5.9.3');
        wp_enqueue_script('dropzone-js', 'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js', ['jquery'], '5.9.3', true);

        // Inclure SweetAlert2
        wp_enqueue_script('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', [], '11', true);

        // Charger Quill.js via CDN
        wp_enqueue_style('quill-css', 'https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css', [], '1.3.7');
        wp_enqueue_script('quill-js', 'https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js', [], '1.3.7', true);

        // Charger Splide via CDN
        wp_enqueue_style('splide-css', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.3/dist/css/splide.min.css', [], '4.1.3');
        wp_enqueue_script('splide-js', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.3/dist/js/splide.min.js', [], '4.1.3', true);


        // Charger le fichier JS personnalisé principal
        wp_enqueue_script(
            'form-manage-pro-page-js',
            get_template_directory_uri() . '/assets/src/js/ajax/formManageProPage.js',
            ['jquery', 'dropzone-js', 'sweetalert2', 'quill-js','splide-js'],
            null,
            true
        );

        //Charger le fichier de gestion des projets
        wp_enqueue_script(
            'manage-projects-profil-pages-js',
            get_template_directory_uri() . '/assets/src/js/ajax/manageRealisationProfilPage.js',
            ['jquery', 'sweetalert2'], // Dépendances nécessaires
            null,
            true
        );

        // Passer l'URL AJAX et l'ID utilisateur aux deux scripts
        wp_localize_script('form-manage-pro-page-js', 'ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'user_id' => get_current_user_id()
        ]);

        wp_localize_script('manage-projects-profil-pages-js', 'ajax_object', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'user_id' => get_current_user_id()
        ]);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_form_manage_pro_page_scripts');






//Mettre à jour l'image de profil depuis la page edit_single_professionnel.php
function update_user_image() {
    // Vérifier que la requête provient bien d'un utilisateur connecté
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Vous devez être connecté pour effectuer cette action.']);
        return;
    }

    // Vérifier si un fichier est envoyé et si le champ 'image_type' est bien défini
    if (!isset($_FILES['image']) || !isset($_POST['user_id']) || !isset($_POST['image_type'])) {
        wp_send_json_error(['message' => 'Données manquantes.']);
        return;
    }

    $user_id = intval($_POST['user_id']);
    if ($user_id !== get_current_user_id()) {
        wp_send_json_error(['message' => 'Accès non autorisé.']);
        return;
    }

    $image_type = sanitize_text_field($_POST['image_type']); // Ex: "background", "profile"
    $uploaded_file = $_FILES['image'];

    // Vérifier la taille du fichier (max 2MB)
    $max_size = 2 * 1024 * 1024; // 2MB en bytes
    if ($uploaded_file['size'] > $max_size) {
        wp_send_json_error(['message' => 'L\'image est trop grande. Taille max : 2MB.']);
        return;
    }

    // Vérifier le type de fichier
    $file_type = wp_check_filetype($uploaded_file['name']);
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($file_type['ext'], $allowed_types)) {
        wp_send_json_error(['message' => 'Format non autorisé. Formats acceptés : jpg, jpeg, png, gif, webp.']);
        return;
    }

    // Gérer l'upload de l'image
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    $upload_overrides = ['test_form' => false];
    $movefile = wp_handle_upload($uploaded_file, $upload_overrides);

    if ($movefile && !isset($movefile['error'])) {
        if (!isset($movefile['url'])) {
            wp_send_json_error(['message' => 'Erreur de récupération de l’URL de l’image.']);
            return;
        }

        // Déterminer le champ user_meta à mettre à jour
        $meta_key = ($image_type === 'profile') ? 'profile_image' : 'background_image';

        // Mettre à jour l'URL de l'image dans la meta utilisateur
        update_user_meta($user_id, $meta_key, esc_url($movefile['url']));

        //Debug pour voir si l'URL est bien renvoyée
        error_log("Image mise à jour : " . esc_url($movefile['url']));

        // Retourner l'URL de la nouvelle image
        wp_send_json_success(['image_url' => esc_url($movefile['url']), 'image_type' => $image_type]);
    } else {
        wp_send_json_error(['message' => 'Erreur lors de l’upload de l\'image.']);
    }
}

////Mettre à jour la description utilisateur sur la page edit_single_professionnel.php
add_action('wp_ajax_update_user_image', 'update_user_image');

function update_user_description() {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Vous devez être connecté pour modifier votre description.']);
        return;
    }

    if (!isset($_POST['user_id']) || !isset($_POST['description'])) {
        wp_send_json_error(['message' => 'Données manquantes.']);
        return;
    }

    $user_id = intval($_POST['user_id']);
    if ($user_id !== get_current_user_id()) {
        wp_send_json_error(['message' => 'Accès non autorisé.']);
        return;
    }

    $description = wp_kses_post($_POST['description']);
    update_user_meta($user_id, 'description', $description);

    wp_send_json_success(['message' => 'Description mise à jour avec succès.']);
}

add_action('wp_ajax_update_user_description', 'update_user_description');

//Permet de formatté le contenue de la description
function afficher_description_formattee($description) {
    $allowed_tags = [
        'p' => ['class' => []], 
        'br' => [],
        'strong' => [],
        'em' => [],
        'ul' => [],
        'ol' => [],
        'li' => ['class' => []],
        'span' => ['style' => []],
    ];

    return wpautop(wp_kses($description, $allowed_tags));
}

//Met à jour le profil pro sur la page edit_single_professionnel.php
function update_user_pro_page_profile_info() {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Vous devez être connecté pour modifier votre profil.']);
        return;
    }

    if (!isset($_POST['user_id']) || $_POST['user_id'] != get_current_user_id()) {
        wp_send_json_error(['message' => 'Accès non autorisé.']);
        return;
    }

    $user_id = intval($_POST['user_id']);

    if (isset($_POST['diplome'])) {
        update_user_meta($user_id, 'diplome_principal', sanitize_text_field($_POST['diplome']));
    }

    if (isset($_POST['experience'])) {
        update_user_meta($user_id, 'annees_experience', intval($_POST['experience']));
    }

    if (isset($_POST['budget'])) {
        update_user_meta($user_id, 'budget_moyen_chantiers', intval($_POST['budget']));
    }

    if (isset($_POST['architect_types']) && !empty($_POST['architect_types'])) {
        $valid_types = [
            "Architecte",
            "Architecte intérieur",
            "Architecte diplômé d'État",
            "Architecte paysagiste"
        ];
    
        // Fonction de nettoyage
        $normalize = function($str) {
            $str = trim($str);
            $str = stripslashes($str); // 🔥 Corrige le \ devant '
            $str = html_entity_decode($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $str = str_replace(['’', '´'], "'", $str); // remplace les apostrophes typographiques
            $str = preg_replace('/\s+/', ' ', $str);
            $str = mb_strtolower($str, 'UTF-8'); // minuscule
            return $str;
        };
    
        $selected_type = sanitize_text_field($_POST['architect_types']);
        $selected_type_normalized = $normalize($selected_type);
        $valid_types_normalized = array_map($normalize, $valid_types);
    
        error_log("Normalisé reçu : " . $selected_type_normalized);
        error_log("Liste : " . implode(' | ', $valid_types_normalized));
    
        if (in_array($selected_type_normalized, $valid_types_normalized)) {
            update_user_meta($user_id, 'architecte_type', $selected_type); // on enregistre la version d'origine
        } else {
            wp_send_json_error(['message' => "Type d’architecte invalide : " . $selected_type]);
        }
    }
    
    
    

    if (isset($_POST['motivation'])) {
        update_user_meta($user_id, 'motivation_metier', sanitize_textarea_field($_POST['motivation']));
    }

    wp_send_json_success(['message' => 'Profil mis à jour avec succès.']);
}
add_action('wp_ajax_update_user_pro_page_profile_info', 'update_user_pro_page_profile_info');


//Ajouter un projet sur la page edit_single_professionnel.php
function add_project_on_profil_page() {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Vous devez être connecté.']);
        return;
    }

    $user_id = intval($_POST['user_id']);
    if ($user_id !== get_current_user_id()) {
        wp_send_json_error(['message' => 'Accès refusé.']);
        return;
    }

    $project_data = [
        'title' => sanitize_text_field($_POST['title']),
        'budget' => sanitize_text_field($_POST['budget']),
        'surface' => sanitize_text_field($_POST['surface']),
        'duration' => sanitize_text_field($_POST['duration']),
        'description' => sanitize_textarea_field($_POST['description']),
    ];

    // Image principale
    if (!empty($_FILES['image']['name'])) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        $uploaded = wp_handle_upload($_FILES['image'], ['test_form' => false]);
        if ($uploaded && !isset($uploaded['error'])) {
            $project_data['image'] = esc_url($uploaded['url']);
        }
    }

    if (empty($project_data['image']) && !empty($_POST['image_url'])) {
        $image_url = esc_url_raw($_POST['image_url']);
        if (filter_var($image_url, FILTER_VALIDATE_URL)) {
            $project_data['image'] = $image_url;
        }
    }

    if (empty($project_data['image'])) {
        $project_data['image'] = 'https://www.shutterstock.com/image-vector/default-image-icon-vector-missing-600nw-2079504220.jpg';
    }

    // Images additionnelles
    $additional_images = [];
    if (!empty($_FILES['additional_images']['name'][0])) {
        foreach ($_FILES['additional_images']['name'] as $key => $name) {
            if ($_FILES['additional_images']['error'][$key] === UPLOAD_ERR_OK) {
                $file = [
                    'name'     => $_FILES['additional_images']['name'][$key],
                    'type'     => $_FILES['additional_images']['type'][$key],
                    'tmp_name' => $_FILES['additional_images']['tmp_name'][$key],
                    'error'    => $_FILES['additional_images']['error'][$key],
                    'size'     => $_FILES['additional_images']['size'][$key],
                ];
                $uploaded = wp_handle_upload($file, ['test_form' => false]);
                if ($uploaded && !isset($uploaded['error'])) {
                    $additional_images[] = esc_url($uploaded['url']);
                }
            }
        }
    }
    $project_data['additional_images'] = $additional_images;

    // Insertion dans la table SQL
    global $wpdb;
    $table = $wpdb->prefix . 'realisation';
    $wpdb->insert($table, [
        'user_id'           => $user_id,
        'title'             => $project_data['title'],
        'budget'            => $project_data['budget'],
        'surface'           => $project_data['surface'],
        'duration'          => $project_data['duration'],
        'description'       => $project_data['description'],
        'image'             => $project_data['image'],
        'additional_images' => maybe_serialize($project_data['additional_images']),
    ]);

    $project_data['realisation_id'] = $wpdb->insert_id;

    // Mise à jour du user_meta
    $projects = get_user_meta($user_id, 'recent_projects', true);
    if (!is_array($projects)) {
        $projects = [];
    }
    $projects[] = $project_data;
    update_user_meta($user_id, 'recent_projects', $projects);

    wp_send_json_success(['message' => 'Projet ajouté avec succès.']);
}
add_action('wp_ajax_add_project_on_profil_page', 'add_project_on_profil_page');




//Supprimer un projet sur la page edit_single_professionnel.php
function delete_project_on_profil_page() {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Vous devez être connecté.']);
        return;
    }

    $user_id = intval($_POST['user_id']);
    $project_index = intval($_POST['project_id']); // index du tableau
    if ($user_id !== get_current_user_id()) {
        wp_send_json_error(['message' => 'Accès refusé.']);
        return;
    }

    $projects = get_user_meta($user_id, 'recent_projects', true);
    if (!is_array($projects) || !isset($projects[$project_index])) {
        wp_send_json_error(['message' => 'Projet introuvable.']);
        return;
    }

    $realisation_id = isset($projects[$project_index]['realisation_id']) ? intval($projects[$project_index]['realisation_id']) : 0;

    // Suppression dans la table
    if ($realisation_id > 0) {
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'realisation', ['id' => $realisation_id, 'user_id' => $user_id]);
    }

    // Suppression dans les meta
    unset($projects[$project_index]);
    $projects = array_values($projects);
    update_user_meta($user_id, 'recent_projects', $projects);

    wp_send_json_success(['message' => 'Projet supprimé avec succès.']);
}

add_action('wp_ajax_delete_project_on_profil_page', 'delete_project_on_profil_page');


function edit_project_on_profil_page() {
    error_log("Données reçues par WordPress: " . print_r($_POST, true));

    if (!isset($_POST['project_id']) || !isset($_POST['title']) || !isset($_POST['description'])) {
        wp_send_json_error(['message' => 'Données invalides']);
    }

    $user_id = get_current_user_id();
    $project_id = sanitize_text_field($_POST['project_id']);
    $projects = get_user_meta($user_id, 'recent_projects', true);

    if (!isset($projects[$project_id])) {
        wp_send_json_error(['message' => 'Projet introuvable']);
    }

    // Mise à jour des champs texte
    $projects[$project_id]['title'] = sanitize_text_field($_POST['title']);
    $projects[$project_id]['budget'] = sanitize_text_field($_POST['budget']);
    $projects[$project_id]['surface'] = sanitize_text_field($_POST['surface']);
    $projects[$project_id]['duration'] = sanitize_text_field($_POST['duration']);
    $projects[$project_id]['description'] = sanitize_textarea_field($_POST['description']);

    // Gestion de l’image principale
    if (!empty($_FILES['image']['name'])) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        $uploaded = wp_handle_upload($_FILES['image'], ['test_form' => false]);

        if (isset($uploaded['url'])) {
            $projects[$project_id]['image'] = esc_url($uploaded['url']);
        }
    }

    // 🔄 Images additionnelles
    $existing_additional_images = isset($projects[$project_id]['additional_images']) ? $projects[$project_id]['additional_images'] : [];

    // 🗑 Suppression d’anciennes images
    if (!empty($_POST['removed_old_images'])) {
        $remaining_images = json_decode(stripslashes($_POST['removed_old_images']), true);
        if (is_array($remaining_images)) {
            $existing_additional_images = $remaining_images;
        }
    }

    // 📥 Ajout de nouvelles images additionnelles
    if (!empty($_FILES['additional_images']['name'][0])) {
        foreach ($_FILES['additional_images']['name'] as $key => $name) {
            if ($_FILES['additional_images']['error'][$key] === UPLOAD_ERR_OK) {
                $file = [
                    'name'     => $_FILES['additional_images']['name'][$key],
                    'type'     => $_FILES['additional_images']['type'][$key],
                    'tmp_name' => $_FILES['additional_images']['tmp_name'][$key],
                    'error'    => $_FILES['additional_images']['error'][$key],
                    'size'     => $_FILES['additional_images']['size'][$key],
                ];

                require_once ABSPATH . 'wp-admin/includes/file.php';
                $uploaded = wp_handle_upload($file, ['test_form' => false]);

                if (isset($uploaded['url'])) {
                    $existing_additional_images[] = esc_url($uploaded['url']);
                }
            }
        }
    }

    $projects[$project_id]['additional_images'] = $existing_additional_images;

    // 💾 Sauvegarde dans user_meta
    update_user_meta($user_id, 'recent_projects', $projects);

    // 🔁 Mise à jour dans la table SQL si realisation_id est présent
    $realisation_id = isset($projects[$project_id]['realisation_id']) ? intval($projects[$project_id]['realisation_id']) : 0;

    if ($realisation_id > 0) {
        global $wpdb;
        $wpdb->update($wpdb->prefix . 'realisation', [
            'title'             => $projects[$project_id]['title'],
            'budget'            => $projects[$project_id]['budget'],
            'surface'           => $projects[$project_id]['surface'],
            'duration'          => $projects[$project_id]['duration'],
            'description'       => $projects[$project_id]['description'],
            'image'             => $projects[$project_id]['image'],
            'additional_images' => maybe_serialize($projects[$project_id]['additional_images']),
        ], ['id' => $realisation_id, 'user_id' => $user_id]);
    }

    // ✅ Retour JSON après mise à jour SQL
    wp_send_json_success([
        'project_id' => $project_id,
        'title' => $projects[$project_id]['title'],
        'budget' => $projects[$project_id]['budget'],
        'surface' => $projects[$project_id]['surface'],
        'duration' => $projects[$project_id]['duration'],
        'description' => $projects[$project_id]['description'],
        'image_url' => $projects[$project_id]['image'],
        'additional_images' => $projects[$project_id]['additional_images']
    ]);
}
add_action('wp_ajax_edit_project_on_profil_page', 'edit_project_on_profil_page');