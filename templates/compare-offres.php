<?php
/* Template Name: Comparatif Offres */
get_header();
?>

<section class="offres py-5">
    <div class="container">
        <h1 class="sr-only">Comparez nos offres pour trouver les meilleurs architectes et mettre en avant vos projets
            sur Showroom
            Architecte</h1>
        <h2 class="font-GildaDisplay bold-100 text-5xl">Nos Tarifs et Fonctionnalités</h2>
        <p class="color-gray-dark text-md text-gray-600 mb-5">
            Showroom Architecte propose plusieurs formules adaptées à vos besoins. Si vous êtes un professionnel
            cherchant à
            valoriser votre savoir-faire , découvrez les fonctionnalités
            incluses dans nos
            comptes Gratuit, Gold et Premium.
        </p>
        <h3 class="text-center text-lg bold-500">Choisissez le plan qui correspond à vos besoins </h3>
        <div class="row">
            <?php get_template_part('templates/parts/pricing-cards'); ?>
        </div>
    </div>
</section>
<section class="comparatif py-5" style="background-color: #f9fafb;">
    <div class="container">
        <h3 class="text-center text-lg bold-500">Comparaison détaillée des fonctionnalités</h3>
        <div class="py-5">
            <?php get_template_part('templates/parts/comparison-table'); ?>
        </div>
    </div>
</section>
<?php get_footer(); ?>