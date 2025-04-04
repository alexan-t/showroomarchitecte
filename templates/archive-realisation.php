<?php
get_header();

/*
Template Name: Archive des réalisations
*/

global $wpdb;
$table_name = $wpdb->prefix . 'realisation';

// Données projets
$realisations_all = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
?>

<div class="archive-realisation py-5" style="background-color: #f9fafb;">
    <div class="container-xl">

        <!-- H1 SEO masqué -->
        <h1 class="sr-only">
            Explorez les réalisations d’architectes et professionnels du bâtiment sur Showroom Architecte – Découvrez
            des projets concrets près de chez vous
        </h1>
        <!-- H2 visible -->
        <h2 class="font-GildaDisplay bold-100 text-5xl">
            Nos Réalisations d’Architectes et de Professionnels du Bâtiment
        </h2>
        <p class="color-gray-dark text-md text-gray-600 mb-5">
            Parcourez une sélection de projets réalisés par les architectes et professionnels membres de Showroom
            Architecte.
            Inspirez-vous de leurs créations, découvrez leurs styles et trouvez le professionnel qui correspond à votre
            vision.
        </p>
        <div class="row list-filter_container">
            <div class="col-md-12 bg-white py-2 rounded-5">
                <h3 class="pl-2 text-lg bold-500">Filtres des Réalisations :</h3>
                <?php get_template_part('templates/parts/realisation/filter-realisation'); ?>
            </div>
            <div class="col-md-12 mt-5">
                <?php get_template_part('templates/parts/realisation/list-realisation'); ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>