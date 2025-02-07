<?php
// Récupérer l'ID de l'utilisateur connecté
$user_id = get_current_user_id();

// Récupérer l'URL de l'image de profil
$profile_image = get_user_meta( $user_id, 'profile_image', true );

// Définir l'image de fond
if ( $profile_image ) {
    $background_image = esc_url( $profile_image );
} else {
    $background_image = get_template_directory_uri() . '/assets/img/blue-circle.svg';
}
?>

<div class="dashboard-particulier ">
    <div class="avatar-container">
        <div class="avatar"
            style="border : 5px solid <?php echo $user_type === 'professionnel' ? '#fc8f02' : '#3968a8' ;?>">
            <?php if ( $profile_image ) : ?>
            <img src="<?php echo esc_url( $profile_image ); ?>"
                alt="Photo de profil de <?php echo esc_attr( wp_get_current_user()->display_name ); ?>">
            <?php else : ?>
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/blue-circle.svg"
                alt="Image de profil par défaut">
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 profil">
            <div class="mt-4">
                <div class="text-lg mb-1">Mon profil</div>
                <ul>
                    <li><a href="<?php echo esc_url( add_query_arg( 'section', 'mes-informations', site_url('/tableau-de-bord/') ) ); ?>"
                            class="italic color-gray">Mes informations</a></li>
                    <li><a href="<?php echo esc_url( add_query_arg( 'section', 'mes-projets', site_url('/tableau-de-bord/') ) ); ?>"
                            class="italic color-gray">Mes projets</a></li>
                    <li><a href="<?php echo esc_url( add_query_arg( 'section', 'messageries', site_url('/tableau-de-bord/') ) ); ?>"
                            class="italic color-gray">Messageries</a></li>
                    <li><a href="<?php echo esc_url( add_query_arg( 'section', 'mes-devis', site_url('/tableau-de-bord/') ) ); ?>"
                            class="italic color-gray">Mes devis en cours</a></li>
                    <li><a href="<?php echo esc_url( add_query_arg( 'section', 'fichiers', site_url('/tableau-de-bord/') ) ); ?>"
                            class="italic color-gray">Fichiers</a></li>
                </ul>
            </div>
            <div class="mt-1">
                <div class="text-lg mb-1">Gérer mon compte</div>
                <ul>
                    <li><a href="<?php echo esc_url( add_query_arg( 'section', 'changer-mdp', site_url('/tableau-de-bord/') ) ); ?>"
                            class="italic color-gray">Changer de mot de passe</a></li>
                    <li><a href="<?php echo esc_url( add_query_arg( 'section', 'infos-professionnelles', site_url('/tableau-de-bord/') ) ); ?>"
                            class="italic color-gray">Informations professionnelles</a></li>
                    <li>
                        <a href="<?php echo esc_url(site_url('/architectes/profil/?id=' . $user_id)); ?>"
                            class="italic color-gray">
                            Voir ma page
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=edit-professionnel')); ?>"
                            class="italic color-gray">
                            Editer ma page
                        </a>
                    </li>

                </ul>
            </div>
            <div class="mt-1">
                <!-- <div class="text-lg mb-1">Parrainage</div> -->
            </div>
            <div class="logout">
                <?php my_custom_logout_link(); ?>
            </div>
        </div>
        <div class="col-md-8">
            <?php
                // Récupérer la valeur du paramètre 'section' dans l'URL
                $section = isset($_GET['section']) ? sanitize_text_field($_GET['section']) : 'dashboard';

                // Charger le contenu en fonction de la section sélectionnée
                switch ($section) {
                    case 'mes-informations':
                        get_template_part('templates/parts/dashboard-parts/dashboard', 'mes-informations');
                        break;
                    case 'mes-projets':
                        get_template_part('templates/parts/dashboard-parts/dashboard', 'mes-projets');
                        break;
                    case 'messageries':
                        get_template_part('templates/parts/dashboard-parts/dashboard', 'messageries');
                        break;
                    case 'mes-devis':
                        get_template_part('templates/parts/dashboard-parts/dashboard', 'mes-devis');
                        break;
                    case 'fichiers':
                        get_template_part('templates/parts/dashboard-parts/dashboard', 'fichiers');
                        break;
                    case 'changer-mdp':
                        get_template_part('templates/parts/dashboard-parts/dashboard', 'changer-mdp');
                        break;
                    case 'infos-professionnelles':
                        get_template_part('templates/parts/dashboard-parts/dashboard', 'infos-professionnelles');
                        break;
                    case 'edit-projet':
                        get_template_part('templates/parts/dashboard-parts/dashboard', 'edit-projet');
                        break;
                    default:
                        get_template_part('templates/parts/dashboard-parts/dashboard', 'mes-informations');
                        break;
                }
            ?>
        </div>
    </div>
</div>