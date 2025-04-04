<?php
function enqueue_realisation_filter_assets() {
    if (is_page_template('templates/archive-realisation.php')) {
    wp_enqueue_script(
        'realisation-filters',
        get_template_directory_uri() . '/assets/src/js/ajax/filterRealisations.js',
        array(),
        null,
        true
    );

    wp_localize_script('realisation-filters', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'lottie_empty' => get_template_directory_uri() . '/assets/lottie/empty.json',
    ));
}
}
add_action('wp_enqueue_scripts', 'enqueue_realisation_filter_assets');

function filter_realisation_ajax() {
    global $wpdb;
    $table = $wpdb->prefix . 'realisation';

    $where = ['1=1'];

    // Filtre search
    if (!empty($_POST['search'])) {
        $search = esc_sql($_POST['search']);
        $where[] = "(title LIKE '%$search%' OR description LIKE '%$search%')";
    }

    // Filtre budget
    if (!empty($_POST['budget'])) {
        $budget = $_POST['budget'];
        if ($budget === 'Moins de 10 000 €') {
            $where[] = "CAST(REPLACE(REPLACE(budget, '€', ''), ' ', '') AS UNSIGNED) < 10000";
        } elseif ($budget === '10 000 - 50 000 €') {
            $where[] = "CAST(REPLACE(REPLACE(budget, '€', ''), ' ', '') AS UNSIGNED) BETWEEN 10000 AND 50000";
        } elseif ($budget === 'Plus de 50 000 €') {
            $where[] = "CAST(REPLACE(REPLACE(budget, '€', ''), ' ', '') AS UNSIGNED) > 50000";
        }
    }

    // Surface
    if (!empty($_POST['surface'])) {
        $surface = $_POST['surface'];
        if ($surface === 'Moins de 30 m²') {
            $where[] = "CAST(REPLACE(REPLACE(surface, 'm²', ''), ' ', '') AS UNSIGNED) < 30";
        } elseif ($surface === '30 à 100 m²') {
            $where[] = "CAST(REPLACE(REPLACE(surface, 'm²', ''), ' ', '') AS UNSIGNED) BETWEEN 30 AND 100";
        } elseif ($surface === 'Plus de 100 m²') {
            $where[] = "CAST(REPLACE(REPLACE(surface, 'm²', ''), ' ', '') AS UNSIGNED) > 100";
        }
    }

    // Duration
    if (!empty($_POST['duration'])) {
        $where[] = $wpdb->prepare("duration = %s", $_POST['duration']);
    }

    // Pagination
    $page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
    $per_page = 9;
    $offset = ($page - 1) * $per_page;

    $where_sql = implode(" AND ", $where);
    $results = $wpdb->get_results("SELECT * FROM $table WHERE $where_sql ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");
    $total = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE $where_sql");
    $total_pages = ceil($total / $per_page);

    // Set les variables pour le template
    set_query_var('realisations', $results);
    set_query_var('current_page', $page);
    set_query_var('total_pages', $total_pages);

    get_template_part('templates/parts/realisation/list-realisation');
    wp_die();
}

add_action('wp_ajax_filter_realisation', 'filter_realisation_ajax');
add_action('wp_ajax_nopriv_filter_realisation', 'filter_realisation_ajax');