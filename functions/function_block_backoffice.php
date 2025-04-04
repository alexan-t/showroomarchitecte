<?php
// Admin functions here
// 1. Bloque TOUT accès au back-office sauf pour l'admin
function block_admin_area_for_non_admin_users() {
    if (
        is_admin() &&                                 // Accès à /wp-admin ou sous-pages
        (!current_user_can('administrator')) &&       // Pas admin
        !(defined('DOING_AJAX') && DOING_AJAX)        // On laisse passer les requêtes AJAX
    ) {
        wp_redirect(home_url('/')); // ou site_url('/connexion/') si tu préfères
        exit;
    }
}
add_action('admin_init', 'block_admin_area_for_non_admin_users');

// 2. Bloque l'accès à wp-login.php pour tout le monde sauf admin
function block_wp_login_php() {
    $uri = $_SERVER['REQUEST_URI'];

    // Autorise les requêtes de déconnexion
    if (strpos($uri, 'wp-login.php') !== false && isset($_GET['action']) && $_GET['action'] === 'logout') {
        return;
    }

    // Sinon, bloque si non-admin
    if (
        strpos($uri, 'wp-login.php') !== false &&
        (!is_user_logged_in() || !current_user_can('administrator'))
    ) {
        wp_redirect(site_url('/connexion/'));
        exit;
    }
}


add_action('init', 'block_wp_login_php');

// 3. Supprime la barre d'administration pour les non-admins
function hide_admin_bar_for_non_admins() {
    if (!current_user_can('administrator')) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'hide_admin_bar_for_non_admins');

// 4. Redirige après connexion si non-admin
function redirect_non_admin_after_login($redirect_to, $request, $user) {
    if (!is_wp_error($user) && !in_array('administrator', (array) $user->roles)) {
        return site_url('/'); // ou '/mon-compte' ou autre page front
    }
    return $redirect_to;
}
add_filter('login_redirect', 'redirect_non_admin_after_login', 10, 3);