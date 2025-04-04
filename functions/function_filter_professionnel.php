<?php

function enqueue_filter_scripts() {
    if (is_page_template('templates/archive-professionnel.php')) {
    wp_enqueue_script(
        'ajax-filters',
        get_template_directory_uri() . '/assets/src/js/ajax/filterPro.js',
        ['jquery'],
        null,
        true
    );

    wp_enqueue_script(
        'sweetalert2',
        'https://cdn.jsdelivr.net/npm/sweetalert2@11',
        [],
        null,
        true
    );

    wp_localize_script('ajax-filters', 'ajaxurl', ['ajaxurl' => admin_url('admin-ajax.php')]);
}
}
add_action('wp_enqueue_scripts', 'enqueue_filter_scripts');


function filter_professionnels() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'userinfos';

    $location = isset($_POST['location']) ? sanitize_text_field($_POST['location']) : '';
    $budgetMin = isset($_POST['budgetMin']) ? floatval($_POST['budgetMin']) : '';
    $budgetMax = isset($_POST['budgetMax']) ? floatval($_POST['budgetMax']) : '';
    $experienceMin = isset($_POST['experienceMin']) ? intval($_POST['experienceMin']) : '';
    $experienceMax = isset($_POST['experienceMax']) ? intval($_POST['experienceMax']) : '';
    $architectTypes = isset($_POST['architectTypes']) ? $_POST['architectTypes'] : [];

    // Commencer la requête en filtrant sur les architectes publics
    $query = "SELECT * FROM $table_name WHERE is_page_public = 1";

    if (!empty($location)) {
        $query .= $wpdb->prepare(" AND city = %s", $location);
    }

    if (!empty($budgetMin)) {
        $query .= $wpdb->prepare(" AND budget_moyen_chantiers >= %f", $budgetMin);
    }

    if (!empty($budgetMax)) {
        $query .= $wpdb->prepare(" AND budget_moyen_chantiers <= %f", $budgetMax);
    }

    if (!empty($experienceMin)) {
        $query .= $wpdb->prepare(" AND annees_experience >= %d", $experienceMin);
    }

    if (!empty($experienceMax)) {
        $query .= $wpdb->prepare(" AND annees_experience <= %d", $experienceMax);
    }

    // 🔥 Correction ici : utilisation correcte de JSON_CONTAINS()
    if (!empty($architectTypes)) {
        $conditions = [];
        foreach ($architectTypes as $type) {
            // Convertir chaque valeur en JSON et l'encadrer avec `CAST(... AS JSON)`
            $type_json = json_encode($type); // Transforme en JSON correctement
            $conditions[] = $wpdb->prepare("JSON_CONTAINS(architecte_type, CAST(%s AS JSON))", $type_json);
        }
        $query .= " AND (" . implode(" OR ", $conditions) . ")";
    }

    $results = $wpdb->get_results($query);

    ob_start();
    ?>

<ul class="professionnel-list">
    <li>
        <div class="row">
            <?php if (!empty($results)) : ?>
            <?php foreach ($results as $user) : ?>
            <div class="col-md-4">
                <div class="card-pro">
                    <?php 
                        // Récupérer les données de l'utilisateur
                        $profile_image = get_user_meta($user->user_id, 'profile_image', true);
                        $city = get_user_meta($user->user_id, 'city', true);
                        $user_type = get_user_meta($user->user_id, 'user_type', true); 
                        $firstname = get_user_meta($user->user_id, 'first_name', true);
                        $lastname = get_user_meta($user->user_id, 'last_name', true);
                        $display_name = $firstname . ' ' . $lastname; 
                        ?>
                    <a class="color-<?php echo $user_type ; ?>"
                        href="<?php echo esc_url(site_url('/architectes/profil/?id=' . $user->user_id)); ?>">
                        <div class="avatar image_effect">
                            <div class="hover01">
                                <figure>
                                    <img src="<?php echo $profile_image ? esc_url($profile_image) : get_avatar_url($user->user_id); ?>"
                                        alt="Photo de profil" class="border-<?php echo esc_attr($user_type); ?>">
                                </figure>
                            </div>
                        </div>
                        <p class="name"><?php echo esc_html($display_name); ?></p>
                        <p class="city flex items-center justify-center">
                            <svg class="icon icon-xl" aria-hidden="true">
                                <use xlink:href="#marker"></use>
                            </svg>
                            <span class="color-blue"><?php echo esc_html($city); ?></span>
                        </p>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
            <?php else : ?>
            <p>Aucun professionnel trouvé.</p>
            <?php endif; ?>
        </div>
    </li>
</ul>

<?php
    echo ob_get_clean();
    wp_die();
}
add_action('wp_ajax_filter_professionnels', 'filter_professionnels');
add_action('wp_ajax_nopriv_filter_professionnels', 'filter_professionnels');