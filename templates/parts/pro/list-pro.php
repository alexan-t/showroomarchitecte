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
                <div class="pro-card">
                    <div class="row items-center">
                        <?php 
                        // Récupérer les données de l'utilisateur
                        $profile_image = get_user_meta($user->ID, 'profile_image', true);
                        $city = get_user_meta($user->ID, 'city', true);
                        $user_type = get_user_meta($user->ID, 'user_type', true); 
                        $architect_types = get_user_meta($user->ID, 'architecte_type', true);
                        $architect_types = is_array($architect_types) ? implode(", ", $architect_types) : $architect_types;
                        $experience = get_user_meta($user->ID, 'annees_experience', true);
                        $budget = get_user_meta($user->ID, 'budget_moyen_chantiers', true);
                        $budget = number_format((int)$budget, 0, '', ' ');

                        ?>
                        <div class="col-md-3">
                            <div class="avatar">
                                <figure>
                                    <img src="<?php echo $profile_image ? esc_url($profile_image) : get_avatar_url($user->ID); ?>"
                                        alt="Photo de profil">
                                </figure>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <p class="name bold-500 m-0"><?php echo esc_html($user->display_name); ?></p>
                            <p class="type bold-500 color-gray-dark"><?php echo esc_html($architect_types); ?></p>
                        </div>
                    </div>
                    <p class="city">
                        <ion-icon name="location-outline"></ion-icon>
                        <span class="color-dark bold-500"><?php echo esc_html($city); ?></span>
                    </p>
                    <p class="experience">
                        <ion-icon name="briefcase-outline"></ion-icon>
                        <span class="color-dark bold-500"><?php echo esc_html($experience); ?> années
                            d'experiences</span>
                    </p>
                    <p class="budget">
                        <ion-icon name="cash-outline"></ion-icon>
                        <span class="color-dark bold-500"><?php echo esc_html($budget); ?>€</span>
                    </p>
                    <a class="p-1 rounded-5 bold-500 justify-center w-100 radius btn btn-<?php echo $user_type ; ?>"
                        href="<?php echo esc_url(site_url('/architectes/profil/?id=' . $user->ID)); ?>">Voir le
                        Profil</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </li>
</ul>