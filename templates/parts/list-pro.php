<?php
if (!defined('ABSPATH')) {
    exit; // Sécurité : empêche l'accès direct au fichier
}

// Récupérer la variable transmise
$users = get_query_var('users', []);

if (empty($users)) {
    echo '<p>Aucun professionnel trouvé.</p>';
    return;
}
?>

<ul class="professionnel-list">
    <li>
        <div class="row">
            <?php foreach ($users as $user) : ?>
            <div class="col-md-4">
                <div class="card-pro">
                    <?php 
                        // Récupérer les données de l'utilisateur
                        $profile_image = get_user_meta($user->ID, 'profile_image', true);
                        $city = get_user_meta($user->ID, 'city', true);
                        $user_type = get_user_meta($user->ID, 'user_type', true); 
                        ?>
                    <a class="color-<?php echo $user_type ; ?>"
                        href="<?php echo esc_url(site_url('/architectes/profil/?id=' . $user->ID)); ?>">
                        <div class="avatar image_effect">
                            <div class="hover01">
                                <figure>
                                    <img src="<?php echo $profile_image ? esc_url($profile_image) : get_avatar_url($user->ID); ?>"
                                        alt="Photo de profil" class="border-<?php echo esc_attr($user_type); ?>">
                                </figure>
                            </div>
                        </div>
                        <p class="name"><?php echo esc_html($user->display_name); ?></p>
                        <p class="city flex items-center justify-center">
                            <svg class="icon icon-xl" aria-hidden="true">
                                <use xlink:href="#marker"></use>
                            </svg>
                            <span class="color-blue"><?php echo esc_html($city); ?></span>
                        </p>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </li>
</ul>