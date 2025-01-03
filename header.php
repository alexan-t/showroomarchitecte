<!DOCTYPE HTML>
<html <?php language_attributes(); 
$user_id = get_current_user_id();
$user_type = get_user_meta( $user_id, 'user_type', true );
?>>

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <div id="page" class="site">

        <header id="masthead" class="site-header">
            <div class="container menu-header">
                <div class="menu-header-logo">
                    <a href="<?php echo site_url('/'); ?>">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo.svg"
                            alt="Logo de Showroom d'Architecte">
                    </a>
                </div>
                <div class="menu-header-links">
                    <button id="menu-button" data-module aria-expanded="false" aria-label="Toggle menu">
                        <div class="menu icon"></div>
                    </button>
                    <div class="menu-header-links-container">
                        <div class="menu-header-links-container-buttons-mobile">
                            <?php if ( ! is_user_logged_in() ) : ?>
                            <div class="menu-header-links-container-buttons-mobile-text">
                                <span>Mon espace</span>
                                <svg class="icon icon-xl" aria-hidden="true">
                                    <use xlink:href="#profil-icon"></use>
                                </svg>
                            </div>
                            <a href="<?php echo site_url('/connexion/'); ?>?type=particulier"
                                class="btn btn-blue uppercase">Particulier</a>
                            <a href="<?php echo site_url('/connexion/'); ?>?type=professionnel"
                                class="btn btn-standard uppercase">Professionnel</a>
                            <?php else : ?>
                            <div class="bg-white p-1 text-start bradius-1">
                                <div
                                    class="mb-1 text-sm <?php echo $user_type === 'professionnel' ? 'color-standard' : 'color-blue' ;?>">
                                    Bienvenue, <span
                                        class="capitalize bold"><?php echo esc_html( wp_get_current_user()->display_name ); ?></span>
                                </div>
                                <a href="<?php echo esc_url( site_url('/tableau-de-bord') ); ?>"
                                    class="justify-center flex gap-1 color-dark">
                                    <svg class="icon icon-xl" aria-hidden="true">
                                        <use xlink:href="#profil-icon"></use>
                                    </svg>
                                    <span>Mon compte</span>
                                </a>
                                <a href="" class="justify-center flex gap-1 italic color-gray">
                                    <?php my_custom_logout_link(); ?>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php wp_nav_menu( array( 'theme_location' => 'menu-main', 'menu_id' => 'menu-main' ) ); ?>
                    </div>
                </div>
                <div class="menu-header-account">
                    <?php if ( ! is_user_logged_in() ) : ?>
                    <div class="menu-header-account-container mb-1">
                        <span>Mon espace</span>
                        <svg class="icon icon-xl" aria-hidden="true">
                            <use xlink:href="#profil-icon"></use>
                        </svg>
                    </div>
                    <div class="menu-header-account-buttons">
                        <a href="<?php echo esc_url( site_url('/connexion/') ); ?>?type=particulier"
                            class="col-md-6 btn btn-blue uppercase bold">Particulier</a>
                        <a href="<?php echo esc_url( site_url('/connexion/') ); ?>?type=professionnel"
                            class="col-md-6 btn btn-standard uppercase bold">Professionnel</a>
                    </div>
                    <?php else : ?>
                    <div class="bg-white p-1 text-start bradius-1">
                        <div
                            class="mb-1 text-sm <?php echo $user_type === 'professionnel' ? 'color-standard' : 'color-blue' ;?>">
                            Bienvenue, <span
                                class="capitalize bold"><?php echo esc_html( wp_get_current_user()->display_name ); ?></span>
                        </div>
                        <a href="<?php echo esc_url( site_url('/tableau-de-bord') ); ?>"
                            class="justify-center flex gap-1 color-dark myaccount">
                            <svg class="icon icon-xl" aria-hidden="true">
                                <use xlink:href="#profil-icon"></use>
                            </svg>
                            <span>Mon compte</span>
                        </a>
                        <a href="" class="justify-center flex gap-1 italic color-gray">
                            <?php my_custom_logout_link(); ?>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </header><!-- #masthead -->