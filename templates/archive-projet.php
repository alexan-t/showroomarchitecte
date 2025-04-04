<?php
get_header();
/*
Template Name: Archive des projets
*/

global $wpdb;
$table_name = $wpdb->prefix . 'projects';

// Données projets
$projects_all = $wpdb->get_results("SELECT * FROM $table_name WHERE status = 'active' ORDER BY created_at DESC");

// Pagination serveur classique (au chargement initial uniquement)
$per_page = 6;
$current_page = 1;
$total = count($projects_all);
$total_pages = ceil($total / $per_page);
$projects = array_slice($projects_all, 0, $per_page);

// Envoi des données aux partials
set_query_var('projects', $projects);
set_query_var('current_page', $current_page);
set_query_var('total_pages', $total_pages);
?>

<div class="archive-projet py-5" style="background-color: #f9fafb;">
    <div class="container-xl">

        <!-- H1 invisible SEO-friendly -->
        <h1 class="sr-only">
            Découvrez les projets déposés par des particuliers sur Showroom Architecte – Trouvez l’inspiration et
            contactez des professionnels qualifiés
        </h1>
        <!-- H2 visible -->
        <h2 class="text-center font-GildaDisplay bold-100 text-5xl my-5">
            Projets d’architecture à découvrir et à réaliser
        </h2>

        <div class="row justify-between">
            <div class="col-md-4 filter-projects bg-white">
                <?php get_template_part('templates/parts/project/filter-projects'); ?>
            </div>
            <div class="col-md-7 list-projects">
                <div class="row" id="project-list">
                    <?php get_template_part('templates/parts/project/list-projects'); ?>
                </div>
                <div id="project-pagination" class="mt-4 text-center">
                    <!-- Pagination AJAX apparaîtra ici -->
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>