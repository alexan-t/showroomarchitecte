<?php
function showroom_create_user_customer_det_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_customer_det';

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) {
        return;
    }

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) UNSIGNED NOT NULL UNIQUE,
        first_name VARCHAR(100),
        last_name VARCHAR(100),
        email VARCHAR(255),
        telephone VARCHAR(30),
        address TEXT,
        city VARCHAR(100),
        postalcode VARCHAR(20),
        latitude FLOAT(10,6),
        longitude FLOAT(10,6),
        is_active TINYINT(1) DEFAULT 0,
        activation_date DATETIME NULL,
        account_origin VARCHAR(100) DEFAULT 'formulaire',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->users}(ID) ON DELETE CASCADE
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
add_action('after_setup_theme', 'showroom_create_user_customer_det_table');


function sync_user_meta_with_customer_table($meta_id, $user_id, $meta_key, $meta_value) {
    global $wpdb;
    $table = $wpdb->prefix . 'user_customer_det';

    $allowed_keys = [
        'first_name', 'last_name', 'email', 'telephone', 'address', 'city',
        'postalcode', 'latitude', 'longitude', 'is_active'
    ];

    if (in_array($meta_key, $allowed_keys)) {
        if ($meta_key === 'is_active') {
            $meta_value = $meta_value == '1' ? 1 : 0;
        }

        $wpdb->update(
            $table,
            [$meta_key => $meta_value],
            ['user_id' => $user_id],
            ['%s'],
            ['%d']
        );
    }
}
add_action('updated_user_meta', 'sync_user_meta_with_customer_table', 10, 4);

function update_wp_customer_det_from_meta() {
    global $wpdb;
    $table = $wpdb->prefix . 'user_customer_det';
    $users = get_users(['meta_key' => 'user_type', 'meta_value' => 'particulier']);

    foreach ($users as $user) {
        $user_id = $user->ID;
        $email = get_userdata($user_id)->user_email;

        $data = [
            'user_id'    => $user_id,
            'first_name' => get_user_meta($user_id, 'first_name', true),
            'last_name'  => get_user_meta($user_id, 'last_name', true),
            'email'      => $email,
            'telephone'  => get_user_meta($user_id, 'telephone', true),
            'address'    => get_user_meta($user_id, 'address', true),
            'city'       => get_user_meta($user_id, 'city', true),
            'postalcode' => get_user_meta($user_id, 'postalcode', true),
            'latitude'   => get_user_meta($user_id, 'latitude', true),
            'longitude'  => get_user_meta($user_id, 'longitude', true),
            'is_active'  => get_user_meta($user_id, 'is_active', true) ? 1 : 0,
        ];

        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE user_id = %d", $user_id));

        if ($exists) {
            $wpdb->update($table, $data, ['user_id' => $user_id]);
        } else {
            $wpdb->insert($table, $data);
        }
    }

    return "Synchronisation des particuliers terminée.";
}