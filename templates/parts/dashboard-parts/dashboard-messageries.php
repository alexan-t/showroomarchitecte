<?php
// Vérifier si l'utilisateur est connecté
if (!is_user_logged_in()) {
    echo '<p>Vous devez être connecté pour accéder à la messagerie.</p>';
    return;
}

// Afficher le formulaire de messagerie et les messages reçus
echo do_shortcode('[messagerie_chat]');
?>