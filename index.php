<?php
get_header();
?>
<style>
header {
    position: absolute;
    background-color: transparent;
}

.menu-header-links a {
    color: #fff !important;
}
</style>
<div id="homepage">
    <section class="landing">
        <video autoplay loop muted playsinline class="background-video">
            <source src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/landing-vid.mp4'); ?>"
                type="video/mp4">
            Votre navigateur ne supporte pas les vidéos HTML5.
        </video>
        <div class="container flex flex-col justify-center items-center pt-3 h-100">
            <div class="landing-title">
                <h1 class="sr-only">Showroom d'arcitecte - architectes vérifiés dans votre ville pour donner vie à vos
                    projets - France </h1>
                <h2 class="uppercase color-white xlbold text-center">
                    nos <span class="bg-white color-professionnel">architectes vérifiés</span>
                    dans votre ville pour donner vie à vos projets.</h2>
            </div>
            <div class="landing-slogan">
                <p class="color-white text-xl">Architecte DPLG, architecte, ouvrier de France, architecte d’intérieur
                    et
                    paysagiste.</p>
            </div>
        </div>
    </section>
    <section class="reassurance pt-5 pb-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="font-GildaDisplay bold-100 text-5xl">Caractéristiques clés </h2>
                </div>
                <div class="col-md-6">
                    <div class="row flex-col items-center p-1">
                        <div class="row items-center">
                            <div class="col-md-2">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/open-eye.png"
                                    alt="Un vaste réseau d’architectes">
                            </div>
                            <div class="col-md-10">
                                <h4>Un vaste réseau d’architectes</h4>
                                <p class="text-sm color-gray-dark">Vaste sélection</p>
                                <p>Accédez à un large éventail d’architectes talentueux. Trouvez votre partenaire idéal
                                    dès aujourd’hui ! </p>
                            </div>
                        </div>
                        <div class="row items-center">
                            <div class="col-md-2">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/pencil-ruler.png"
                                    alt="Connexion client transparente">
                            </div>
                            <div class="col-md-10">
                                <h4>Connexion client transparente </h4>
                                <p class="text-sm color-gray-dark">Direct Engagement </p>
                                <p>Connectez-vous directement avec les architectes. Discutez de vos besoins et
                                    collaborez efficacement pour atteindre les objectifs de votre projet de manière
                                    efficace et créative. </p>
                            </div>
                        </div>
                        <div class="row items-center">
                            <div class="col-md-2">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/boxes.png"
                                    alt="Connexion client transparente">
                            </div>
                            <div class="col-md-10">
                                <h4>Plateforme de présentation de projets</h4>
                                <p class="text-sm color-gray-dark">Inspiration visuelle</p>
                                <p>Découvrez divers projets architecturaux. Laissez-vous inspirer et visualisez vos
                                    futurs espaces avec facilité et innovation. </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="services bg-dark color-white py-5">
        <div class="container">
            <div class="text-center">
                <h2 class="font-GildaDisplay bold-100 text-5xl">Nos Services</h2>
            </div>
            <div class="row">
                <div class="service-item">
                    <div class="py-2">
                        <h4>Trouver des architectes </h4>
                        <p>Parcourez les profils, les portefeuilles et l’expertise. Connectez-vous avec l’architecte
                            parfait pour votre projet, assurant une collaboration transparente et réussie pour vos
                            besoins.</p>
                    </div>
                    <hr>
                </div>
                <div class="service-item">
                    <div class="py-2">
                        <h4>Vitrine du projet </h4>
                        <p>Affichez vos plus beaux designs architecturaux. Attirez des clients potentiels et obtenez la
                            reconnaissance de votre travail innovant, améliorant ainsi votre réputation professionnelle.
                        </p>
                    </div>
                    <hr>
                </div>
                <div class="service-item">
                    <div class="py-2">
                        <h4>Appariement des clients </h4>
                        <p>Mettez-vous en relation avec des clients potentiels. Élargissez votre portée et sécurisez
                            efficacement de nouveaux projets qui correspondent à votre expertise et à vos préférences en
                            matière de design.
                        </p>
                    </div>
                    <hr>
                </div>
                <div class="service-item">
                    <h4>Outils de gestion des abonnements </h4>
                    <p>Gérez votre profil, vos projets et vos abonnements. Restez organisé et optimisez votre présence
                        sur Showroom Architecte pour un maximum d’impact et d’efficacité.
                    </p>
                </div>
            </div>
        </div>
    </section>
    <section class="py-5">
        <div class="container">
            <h2 class="font-GildaDisplay bold-100 text-5xl">Présentation de nos projets </h2>
            <div class="row mt-5">
                <div class="col-md-6">
                    <div class="projet projet-item">
                        <figure class="ratio ratio-4x3">
                            <img class="projet-item-image ratio-item"
                                src="<?php echo get_template_directory_uri(); ?>/assets/img/imgtest3.jpg" alt="">
                        </figure>
                        <div class="projet-item-infos pt-2">
                            <h5>Oasis urbaine </h5>
                            <p>Un projet résidentiel moderne. Mettre en valeur le design innovant et l’habitat durable
                                en milieu urbain. </p>
                            <a href="">Voir le projet</a>
                        </div>
                    </div>
                    <div class="projet projet-item mt-5">
                        <figure class="ratio ratio-1x1">
                            <img class="projet-item-image ratio-item"
                                src="<?php echo get_template_directory_uri(); ?>/assets/img/imgtest3.jpg" alt="">
                        </figure>
                        <div class="projet-item-infos pt-2">
                            <h5>Oasis urbaine </h5>
                            <p>Un projet résidentiel moderne. Mettre en valeur le design innovant et l’habitat durable
                                en milieu urbain. </p>
                            <a href="">Voir le projet</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 pt-5">
                    <div class="projet projet-item">
                        <figure class="ratio ratio-1x1">
                            <img class="projet-item-image ratio-item"
                                src="<?php echo get_template_directory_uri(); ?>/assets/img/imgtest3.jpg" alt="">
                        </figure>
                        <div class="projet-item-infos pt-2">
                            <h5>Oasis urbaine </h5>
                            <p>Un projet résidentiel moderne. Mettre en valeur le design innovant et l’habitat durable
                                en milieu urbain. </p>
                            <a href="">Voir le projet</a>
                        </div>
                    </div>
                    <div class="projet projet-item mt-5">
                        <figure class=" ratio ratio-4x3">
                            <img class="projet-item-image ratio-item"
                                src="<?php echo get_template_directory_uri(); ?>/assets/img/imgtest3.jpg" alt="">
                        </figure>
                        <div class="projet-item-infos pt-2">
                            <h5>Oasis urbaine </h5>
                            <p>Un projet résidentiel moderne. Mettre en valeur le design innovant et l’habitat durable
                                en milieu urbain. </p>
                            <a href="">Voir le projet</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="contact bg-dark color-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="font-GildaDisplay bold-100 text-5xl">Contactez-nous pour toute demande de renseignements
                    </h2>
                    <p class="color-gray">Contactez-nous pour des collaborations. Nous sommes là pour vous aider à
                        donner vie à vos rêves architecturaux. </p>
                    <ul>
                        <li>Adresse : Paris, France </li>
                        <li>Téléphone : +33 1 23 45 67 89 </li>
                        <li>Courriel : info@showroomarchitecte.com </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <div class="form-contact">
                        <?php include_once( get_template_directory() . '/templates/parts/contact-form.php' ); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="temoignage py-5">
        <div class="container">
            <h2 class="text-center font-GildaDisplay bold-100 text-5xl">Témoignages et avis
            </h2>
            <div class="row pt-5">
                <div class="col-md-4">
                    <div>
                        <p class="bold-500">
                            Showroom Architecte m’a aidé à trouver l’architecte parfait pour la maison de mes rêves. La
                            plateforme est facile à utiliser et les architectes sont incroyablement talentueux. Je le
                            recommande vivement !
                        </p>
                        <div class="row pt-3">
                            <div class="col-md-4">
                                <img class="projet-item-image ratio-item"
                                    src="<?php echo get_template_directory_uri(); ?>/assets/img/imgtest3.jpg" alt="">
                            </div>
                            <div class="col-md-8">
                                <p class="bold-500">Jane Doe</p>
                                <p>Particulier</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div>
                        <p class="bold-500">
                            Showroom Architecte m’a aidé à trouver l’architecte parfait pour la maison de mes rêves. La
                            plateforme est facile à utiliser et les architectes sont incroyablement talentueux. Je le
                            recommande vivement !
                        </p>
                        <div class="row pt-3">
                            <div class="col-md-4">
                                <img class="projet-item-image ratio-item"
                                    src="<?php echo get_template_directory_uri(); ?>/assets/img/imgtest3.jpg" alt="">
                            </div>
                            <div class="col-md-8">
                                <p class="bold-500">Jane Doe</p>
                                <p>Particulier</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div>
                        <p class="bold-500">
                            Showroom Architecte m’a aidé à trouver l’architecte parfait pour la maison de mes rêves. La
                            plateforme est facile à utiliser et les architectes sont incroyablement talentueux. Je le
                            recommande vivement !
                        </p>
                        <div class="row pt-3">
                            <div class="col-md-4">
                                <img class="projet-item-image ratio-item"
                                    src="<?php echo get_template_directory_uri(); ?>/assets/img/imgtest3.jpg" alt="">
                            </div>
                            <div class="col-md-8">
                                <p class="bold-500">Jane Doe</p>
                                <p>Particulier</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>









</div>

<?php
get_footer();