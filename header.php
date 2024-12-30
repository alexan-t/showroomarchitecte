<!DOCTYPE HTML>
<html <?php language_attributes(); ?>>

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
                        </div>
                        <?php wp_nav_menu( array( 'theme_location' => 'menu-main', 'menu_id' => 'menu-main' ) ); ?>
                    </div>
                </div>
                <div class="menu-header-account">
                    <div class="menu-header-account-container">
                        <span>Mon espace</span>
                        <svg class="icon icon-xl" aria-hidden="true">
                            <use xlink:href="#profil-icon"></use>
                        </svg>
                    </div>
                    <div class="menu-header-account-buttons">
                        <a href="<?php echo site_url('/connexion/'); ?>?type=particulier"
                            class="col-md-6 btn btn-blue uppercase bold">Particulier</a>
                        <a href="<?php echo site_url('/connexion/'); ?>?type=professionnel"
                            class="col-md-6 btn btn-standard uppercase bold">Professionnel</a>
                    </div>
                </div>
            </div>
        </header><!-- #masthead -->