<?php
$current_user = wp_get_current_user();
$first_name = get_user_meta($current_user->ID, 'first_name', true);
$last_name = get_user_meta($current_user->ID, 'last_name', true);
$profile_image = get_user_meta($current_user->ID, 'profile_image', true);
$user_img = !empty($profile_image) ? esc_url($profile_image) : get_avatar_url($current_user->ID);
$type =  get_user_meta($current_user->ID, 'user_type', true);

?>


<div class="container">
    <div class="navigation">
        <div class="user-box">
            <div class="image-box p-relative">
                <img src="<?php echo esc_url($user_img); ?>" alt="Photo de profil">
            </div>
            <p class="username text-sm color-<?php echo $type ?>"><?php echo esc_html($first_name); ?>
                <?php echo esc_html($last_name); ?></p>
        </div>
        <div class="menu-toggle_user"></div>

        <ul class="menu">
            <li>
                <a href="<?php echo esc_url( site_url('/tableau-de-bord') ); ?>" class=" color-dark">
                    <ion-icon name="person-outline"></ion-icon>Profile
                </a>
            </li>
            <li class="p-relative">
                <a href="<?php echo esc_url( site_url('/tableau-de-bord/?section=messageries') ); ?>">
                    <ion-icon name="chatbox-outline"></ion-icon>Messages
                </a><?php echo do_shortcode('[messagerie_unread_count]'); ?>
            </li>
            <?php if($type === "particulier") : ?>
            <li><a href="<?php echo esc_url( site_url('/new-project') ); ?>">
                    <ion-icon name="construct-outline"></ion-icon>Nouveau Projet
                </a></li>
            <?php endif;?>
            <?php if($type !== "particulier") : ?>
            <li>
                <a href="<?php echo esc_url(showroom_get_edit_profile_url($user_id)); ?>" class="italic color-gray">
                    <ion-icon name="create-outline"></ion-icon>Editer ma page
                </a>
            </li>
            <?php endif;?>
            <li><a href="<?php echo wp_logout_url( home_url() ); ?>">
                    <ion-icon name="log-out-outline"></ion-icon>Déconnexion
                </a></li>
        </ul>
    </div>
</div>