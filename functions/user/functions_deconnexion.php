<?php 

function my_custom_logout_link() {

    $redirect_url = home_url(); // URL par défaut

    // Générer l'URL de déconnexion avec redirection sécurisée
    $logout_url = wp_logout_url( $redirect_url );

    // Générer le lien de déconnexion avec les classes et l'icône
    $logout_link = '<a href="' . esc_url( $logout_url ) . '" class="justify-center flex gap-1 italic color-gray logout">';
    $logout_link .= '<svg class="icon icon-xl" aria-hidden="true">';
    $logout_link .= '<use xlink:href="#exit"></use>';
    $logout_link .= '</svg>';
    $logout_link .= 'Déconnexion';
    $logout_link .= '</a>';

    // Afficher le lien
    echo $logout_link;
}