<?php
/* Template Name: Messagerie */
get_header(); // Inclure l’en-tête WordPress

// Vérifier si l'utilisateur est connecté
if (!is_user_logged_in()) {
    echo '<p>Vous devez être connecté pour accéder à la messagerie.</p>';
    get_footer();
    return;
}

// Afficher les shortcodes correctement
echo do_shortcode('[messagerie_chat]');

get_footer(); // Inclure le pied de page WordPress
?>