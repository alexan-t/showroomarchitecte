<?php
/*
Template Name: Tableau de Bord
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


<section class="dashboard-page my-5">
    <div class="container">
        <?php if ( $user_type !== 'particulier' ) : ?>
        <!-- <h2>Bienvenue, <?php echo esc_html( wp_get_current_user()->display_name ); ?> (Professionnel)</h2> -->
        <?php include( get_template_directory() . '/templates/parts/dashboard-professionnel.php' ); ?>
        <?php elseif ( $user_type === 'particulier' ) : ?>
        <?php include( get_template_directory() . '/templates/parts/dashboard-particulier.php' ); ?>
        <?php else : ?>
        <p>Vous n'avez pas accès à ce tableau de bord.</p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>