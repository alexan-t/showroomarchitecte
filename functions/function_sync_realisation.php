<?php 

function create_realisation_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'realisation';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        title VARCHAR(255) NOT NULL,
        budget VARCHAR(100),
        surface VARCHAR(100),
        duration VARCHAR(100),
        description TEXT,
        image TEXT,
        additional_images LONGTEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (user_id) REFERENCES {$wpdb->users}(ID) ON DELETE CASCADE
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
add_action('after_setup_theme', 'create_realisation_table');


function sync_user_meta_to_realisation_table() {
    global $wpdb;
    $users = get_users();

    foreach ($users as $user) {
        $projects = get_user_meta($user->ID, 'recent_projects', true);
        if (!is_array($projects)) continue;

        foreach ($projects as $project) {
            $wpdb->insert($wpdb->prefix . 'realisation', [
                'user_id' => $user->ID,
                'title' => sanitize_text_field($project['title']),
                'budget' => sanitize_text_field($project['budget']),
                'surface' => sanitize_text_field($project['surface']),
                'duration' => sanitize_text_field($project['duration']),
                'description' => sanitize_textarea_field($project['description']),
                'image' => esc_url($project['image']),
                'additional_images' => maybe_serialize($project['additional_images']),
            ]);
        }
    }
}

function sync_realisation_table_to_user_meta($user_id) {
    global $wpdb;
    $table = $wpdb->prefix . 'realisation';

    $projects = $wpdb->get_results($wpdb->prepare("
        SELECT * FROM $table WHERE user_id = %d ORDER BY created_at ASC
    ", $user_id));

    $formatted = [];

    foreach ($projects as $project) {
        $formatted[] = [
            'title' => $project->title,
            'budget' => $project->budget,
            'surface' => $project->surface,
            'duration' => $project->duration,
            'description' => $project->description,
            'image' => $project->image,
            'additional_images' => maybe_unserialize($project->additional_images),
        ];
    }

    update_user_meta($user_id, 'recent_projects', $formatted);
}