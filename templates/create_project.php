<?php
/*
Template Name: New Project
*/

// Vérifier si l'utilisateur est connecté
if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url() );
    exit;
}

// Récupérer le type d'utilisateur
$user_id = get_current_user_id();
$user_type = get_user_meta( $user_id, 'user_type', true );

get_header();
?>


<section class="createproject-page my-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 left-panel bg-blue">
                <?php 
                if ( has_post_thumbnail() ) {
                    the_post_thumbnail( 'large', ['alt' => get_the_title()] ); 
                } else {
                    echo '<p>Aucune image trouvée.</p>';
                }
                ?>
                <div class="p-2">
                    <p class="color-white">
                        Créez votre fiche de projet pour que nous puissions vous orienter vers le professionnel qui vous
                        correspond le plus.
                    </p>
                </div>
            </div>
            <div class="col-md-8 right-panel">
                <!-- Ajouter ici le formulaire dynamique -->
            </div>
        </div>
    </div>

</section>

<?php get_footer(); ?>