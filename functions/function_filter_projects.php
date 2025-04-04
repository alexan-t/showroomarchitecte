<?php
function enqueue_project_filter_assets() {
    if (is_page_template('templates/archive-projet.php')) {
    wp_enqueue_script(
        'project-filters',
        get_template_directory_uri() . '/assets/src/js/ajax/filterProjets.js',
        array(),
        null,
        true
    );

    wp_localize_script('project-filters', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
}
add_action('wp_enqueue_scripts', 'enqueue_project_filter_assets');


////// Fonction Pour filtrer les projets

add_action('wp_ajax_filter_projects', 'filter_projects_ajax');
add_action('wp_ajax_nopriv_filter_projects', 'filter_projects_ajax');

function filter_projects_ajax() {
    global $wpdb;
    $table = $wpdb->prefix . 'projects';

    $where = ["status = 'active'"];

    if (!empty($_POST['search'])) {
        $search = esc_sql($_POST['search']);
        $where[] = "(project_name LIKE '%$search%' OR project_description LIKE '%$search%')";
    }

    if (!empty($_POST['city'])) {
        $where[] = $wpdb->prepare("city = %s", $_POST['city']);
    }

    if (!empty($_POST['type_bien'])) {
        $where[] = $wpdb->prepare("property = %s", $_POST['type_bien']);
    }

    if (!empty($_POST['type_projet'])) {
        $where[] = $wpdb->prepare("projet = %s", $_POST['type_projet']);
    }

    if (!empty($_POST['budget'])) {
        $budget_clauses = [];
        foreach ($_POST['budget'] as $range) {
            if ($range === '0-10000') $budget_clauses[] = "CAST(budget AS UNSIGNED) BETWEEN 0 AND 10000";
            if ($range === '10000-50000') $budget_clauses[] = "CAST(budget AS UNSIGNED) BETWEEN 10000 AND 50000";
            if ($range === '50000+') $budget_clauses[] = "CAST(budget AS UNSIGNED) > 50000";
        }
        if (!empty($budget_clauses)) {
            $where[] = '(' . implode(' OR ', $budget_clauses) . ')';
        }
    }

    if (!empty($_POST['surface'])) {
        $surface = $_POST['surface'];
        if ($surface === 'petite') $where[] = "CAST(total_surface AS UNSIGNED) <= 50";
        elseif ($surface === 'moyenne') $where[] = "CAST(total_surface AS UNSIGNED) > 50 AND CAST(total_surface AS UNSIGNED) <= 120";
        elseif ($surface === 'grande') $where[] = "CAST(total_surface AS UNSIGNED) > 120";
    }

    $order = ($_POST['sort_date'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';
    $page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
    $per_page = 6;
    $offset = ($page - 1) * $per_page;

    $where_sql = implode(' AND ', $where);
    $projects = $wpdb->get_results("SELECT * FROM $table WHERE $where_sql ORDER BY created_at $order LIMIT $per_page OFFSET $offset");

    $total = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE $where_sql");
    $total_pages = ceil($total / $per_page);
    error_log("TOTAL PAGES CALCULÉ : $total_pages");

    set_query_var('projects', $projects);
    set_query_var('current_page', $page);
    set_query_var('total_pages', $total_pages);

    get_template_part('templates/parts/project/list-projects');
    wp_die();
}