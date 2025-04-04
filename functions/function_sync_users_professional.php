<?php
//Creation de la table wp_professional_det
function shoowroom_create_user_professional_det_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'professional_det';

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
add_action('after_setup_theme', 'shoowroom_create_user_professional_det_table');





//Quand user_meta est mis à jour, synchroniser wp_professional_det
function sync_user_meta_with_table($meta_id, $user_id, $meta_key, $meta_value) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'professional_det';

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


//Quand wp_professional_det est modifié, mettre à jour user_meta
function sync_user_meta_from_professional_det($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'professional_det';

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
        sync_user_meta_from_professional_det($user_id);
    }
}
add_action('wp', 'sync_user_meta_on_page_load');


function update_wp_professional_det_from_meta() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'professional_det';

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

        // Vérifier si l'utilisateur existe déjà dans `wp_professional_det`
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
update_wp_professional_det_from_meta();