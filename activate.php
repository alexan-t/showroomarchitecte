<?php
/*
Template Name: Activation compte
*/
get_header();

if (isset($_GET['token'], $_GET['user'])) {
    $user_id = intval($_GET['user']);
    $token = sanitize_text_field($_GET['token']);

    $saved_token = get_user_meta($user_id, 'activation_token', true);
    $token_time  = get_user_meta($user_id, 'activation_token_time', true);

    // Vérifier si le token correspond
    if ($saved_token && $saved_token === $token) {
        // Vérifier expiration (24h = 86400s)
        $now = time();
        if (($now - intval($token_time)) > 86400) {
            echo '⏰ Le lien d’activation a expiré. Veuillez vous réinscrire ou demander un nouveau lien.';
            exit;
        }

        // Activer le compte
        update_user_meta($user_id, 'is_active', 1);
        delete_user_meta($user_id, 'activation_token');
        delete_user_meta($user_id, 'activation_token_time');

        echo '✅ Votre compte a été activé. Vous pouvez maintenant vous connecter.';
    } else {
        echo '❌ Lien invalide ou expiré.';
    }
} else {
    echo '❌ Paramètres manquants.';
}


get_footer();