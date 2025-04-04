<?php 

function ajax_reset_user_password() {
    // Vérifie le nonce de sécurité
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'user_login_nonce')) {
        wp_send_json_error(['message' => 'Sécurité invalide.'], 403);
    }

    // Récupère et nettoie les champs
    $login            = isset($_POST['reset_login']) ? sanitize_user($_POST['reset_login']) : '';
    $key              = isset($_POST['reset_key']) ? sanitize_text_field($_POST['reset_key']) : '';
    $new_password     = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

    // Vérifie les champs obligatoires
    if (empty($login) || empty($key) || empty($new_password) || empty($confirm_password)) {
        wp_send_json_error(['message' => 'Tous les champs sont requis.'], 400);
    }

    // Vérifie que les mots de passe correspondent
    if ($new_password !== $confirm_password) {
        wp_send_json_error(['message' => 'Les mots de passe ne correspondent pas.'], 400);
    }

    // Vérifie la force minimale du mot de passe
    if (strlen($new_password) < 8 || !preg_match('/[A-Z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
        wp_send_json_error(['message' => 'Le mot de passe doit contenir au moins 8 caractères, dont une majuscule et un chiffre.'], 400);
    }

    // Vérifie la clé de réinitialisation
    $user = check_password_reset_key($key, $login);
    if (is_wp_error($user)) {
        wp_send_json_error(['message' => 'Le lien est invalide ou expiré.'], 400);
    }

    // Tente de réinitialiser le mot de passe
    try {
        reset_password($user, $new_password);
    } catch (Exception $e) {
        wp_send_json_error(['message' => 'Erreur lors de la mise à jour du mot de passe.'], 500);
    }

    // Succès
    wp_send_json_success([
        'message'  => 'Mot de passe mis à jour avec succès.',
        'redirect' => site_url('/connexion'),
    ], 200);
}

add_action('wp_ajax_reset_user_password', 'ajax_reset_user_password');
add_action('wp_ajax_nopriv_reset_user_password', 'ajax_reset_user_password');