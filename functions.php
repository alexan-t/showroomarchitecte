<?php

//Link du JS ET CSS
function enqueue_vite_assets() {
    $manifest_path = get_template_directory() . '/assets/dist/.vite/manifest.json';

    if (file_exists($manifest_path)) {
        $manifest = json_decode(file_get_contents($manifest_path), true);

        // Récupérer les détails de l'entrée principale
        $main_entry = $manifest['assets/src/js/main.js'];

        // Ajouter le fichier CSS
        if (isset($main_entry['css'])) {
            foreach ($main_entry['css'] as $css_file) {
                wp_enqueue_style(
                    'vite-main-style',
                    get_template_directory_uri() . '/assets/dist/' . $css_file,
                    [],
                    null
                );
            }
        }

        // Ajouter le fichier JS
        if (isset($main_entry['file'])) {
            wp_enqueue_script(
                'vite-main-script',
                get_template_directory_uri() . '/assets/dist/' . $main_entry['file'],
                [],
                null,
                true
            );
        }
    }
}


////Link des fonctions dans le dossier functions
add_action('wp_enqueue_scripts', 'enqueue_vite_assets');

// Inclure automatiquement tous les fichiers dans le dossier 'functions'
function include_functions($folder) {
    foreach (glob($folder . '/*.php') as $file) {
        require_once $file;
    }

    foreach (glob($folder . '/*', GLOB_ONLYDIR) as $subfolder) {
        include_functions($subfolder);
    }
}

// Inclure tous les fichiers du dossier 'functions'
include_functions(get_template_directory() . '/functions');






// CPT TAXONOMY

include( 'configure/cpt-taxonomy.php' );

// Utilities

include( 'configure/utilities.php' );

// CONFIG

include( 'configure/configure.php' );

// JAVASCRIPT & CSS

include( 'configure/js-css.php' );

// SHORTCODES

include( 'configure/shortcodes.php' );

// ACF

include( 'configure/acf.php' );

// HOOKS ADMIN

if(is_admin()) {
	include( 'configure/admin.php' );
}