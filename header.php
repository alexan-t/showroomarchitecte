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
            <div class="menu-header">
                <div class="menu_toggle">
                    <div class="nav-btn" title="Toggle navigation">
                        <span></span>
                    </div>
                </div>
                <div class="menu-header-logo">
                    <a class="flex justify-center" href="<?php echo site_url('/'); ?>">
                        <img class="p-1" src="<?php echo get_template_directory_uri(); ?>/assets/img/image.png"
                            alt="Logo de Showroom d'Architecte">
                    </a>
                </div>
                <div class="menu_user_account">
                    <?php if ( ! is_user_logged_in() ) : ?>
                    <div class="account_not_connected">
                        <div class="text-end color-white mr-3">
                            Se connecter en tant que
                        </div>
                        <div class="flex justify-end gap-1 mr-1">
                            <a href="<?php echo site_url('/connexion/'); ?>?type=particulier"
                                class="btn btn-particulier uppercase">Particulier</a>
                            <a href="<?php echo site_url('/connexion/'); ?>?type=professionnel"
                                class="btn btn-professionnel uppercase">Professionnel</a>
                        </div>
                    </div>
                    <?php else : ?>
                    <?php include_once( get_template_directory() . '/templates/parts/menu_user_account.php' ); ?>
                    <?php endif; ?>
                </div>
            </div>
    </div>
    <div class="p-fixed">
        <div class="container-menu">
            <?php wp_nav_menu( array( 'theme_location' => 'menu-main', 'menu_id' => 'menu-main' ) ); ?>
        </div>
    </div>
    </header><!-- #masthead -->