<?php
get_header();

/*
Template Name: Archive des architectes
*/

// Récupérer les utilisateurs avec 'user_type' égal à 'professionnel' et is_page_public à 'true'
$args = [
    'meta_query' => [
        [
            'key'     => 'user_type',
            'value'   => 'particulier',
            'compare' => '!='
        ],
        [
            'key'     => 'is_page_public',
            'value'   => '1',
            'compare' => '='
        ]
    ],
    'number'     => -1,
];

$users = get_users($args);

// Passer la variable $users au fichier template-part
set_query_var('users', $users);
?>

<div class="archive-professionnel">

    <div class="container-xl">
        <!-- H1 SEO invisible -->
        <h1 class="sr-only">
            Trouvez un architecte ou un professionnel du bâtiment sur Showroom Architecte – Consultez les profils,
            projets et réalisations près de chez vous
        </h1>
        <!-- H2 visible pour l'utilisateur -->
        <h2 class="font-GildaDisplay bold-100 text-5xl">
            Découvrez nos architectes et professionnels disponibles
        </h2>
        <p class="color-gray-dark text-md text-gray-600 mb-5">
            Explorez notre sélection d’architectes et de professionnels du bâtiment inscrits sur Showroom Architecte.
            Consultez leurs profils, découvrez leurs réalisations et contactez-les facilement pour donner vie à vos
            projets d’architecture ou de rénovation.
        </p>
        <div class="row list-filter_container">
            <div class="col-md-12 border-bottom">
                <?php get_template_part('templates/parts/pro/filter-pro'); ?>
            </div>
            <div class="col-md-12 mt-5">
                <?php get_template_part('templates/parts/pro/list-pro'); ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>