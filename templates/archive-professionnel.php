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
            'compare' => '!=' // Exclure les utilisateurs dont le type est 'particulier'
        ],
        [
            'key'     => 'is_page_public',
            'value'   => '1',
            'compare' => '=' // Afficher uniquement les utilisateurs ayant activé la visibilité
        ]
    ],
    'number'     => -1, // Récupérer tous les utilisateurs correspondants
];

$users = get_users($args);

// Passer la variable $users au fichier template-part
set_query_var('users', $users);
?>

<div class="archive-professionnel">
    <div class="container-xl">
        <div class="row list-filter_container">
            <div class="col-md-4 border-right">
                <?php get_template_part('templates/parts/filter-pro');?>
            </div>
            <div class="col-md-8">
                <?php get_template_part('templates/parts/list-pro'); ?>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();