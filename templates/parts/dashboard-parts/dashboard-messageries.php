<?php
// Vérifier si le plugin BP Better Messages est actif avant d'afficher le chat
if (shortcode_exists('better_messages')) {
    echo do_shortcode('[better_messages]');
} else {
    echo '<p>La messagerie n’est pas disponible.</p>';
}
if (!is_user_logged_in()) {
    echo '<p>Vous devez être connecté pour accéder à la messagerie.</p>';
    return;
}
?>

<style>
.bp-messages-container {
    max-width: 100%;
    margin: 0 auto;
    background: #f7f7f7;
    padding: 20px;
    border-radius: 8px;
}
</style>