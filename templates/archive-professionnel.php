<?php
get_header();

/*
Template Name: Archive des architectes
*/

// Récupérer les utilisateurs avec 'user_type' égal à 'professionnel'
$args = [
    'meta_key'   => 'user_type',
    'meta_value' => 'professionnel',
    'number'     => -1, // Récupérer tous les utilisateurs correspondants
];

$users = get_users($args);

?>

<div class="archive-professionnel">
    <div class="container">
        <h2>Liste des professionnels</h2>

        <?php if (!empty($users)) : ?>
        <ul class="professionnel-list">
            <?php foreach ($users as $user) : ?>
            <li>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card-pro">
                            <?php 
                // Récupérer les données de l'utilisateur
                $profile_image = get_user_meta($user->ID, 'profile_image', true);
                $city = get_user_meta($user->ID, 'city', true);
                $user_type = get_user_meta($user->ID, 'user_type', true); 
            ?>
                            <div class="avatar">
                                <img src="<?php echo $profile_image ? esc_url($profile_image) : get_template_directory_uri() . '/assets/img/blue-circle.svg'; ?>"
                                    alt="Photo de profil" class="border-<?php echo $user_type ?>">
                            </div>
                            <p class="name"><?php echo esc_html($user->display_name); ?></p>
                            <p class="flex items-center mb-3 ">
                                <svg class="icon icon-xl" aria-hidden="true">
                                    <use xlink:href="#marker"></use>
                                </svg>
                                <span class="color-blue"><?php echo esc_html($user->city); ?></span>
                            </p>
                            <a class="color-dark"
                                href="<?php echo esc_url(site_url('/architectes/profil/?id=' . $user->ID)); ?>">
                                <div class="view-hover bg-<?php echo $user_type ?>">
                                    <p>Voir le profil</p>
                                    <svg class="icon icon-xl" aria-hidden="true">
                                        <use xlink:href="#right-arrow"></use>
                                    </svg>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else : ?>
        <p>Aucun professionnel trouvé.</p>
        <?php endif; ?>
    </div>

</div>

<?php
get_footer();