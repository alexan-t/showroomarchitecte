<?php
get_header();
?>
<style>
header {
    position: absolute;
}

.menu-header-links a {
    color: #fff !important;
}
</style>
<div id="homepage">
    <section class="landing"
        style="background-image: url('<?php echo esc_url(get_the_post_thumbnail_url(null, 'full')); ?>');">
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
            <div class="form ">
                <form action="" class="row">
                    <div class="custom-select col-md-6">
                        <select>
                            <option>Je recherche...</option>
                            <option value="1">Option 1</option>
                            <option value="2">Option 2</option>
                            <option value="3">Option 3</option>
                        </select>
                    </div>
                    <div class="custom-select col-md-6">
                        <select>
                            <option>Type de bien</option>
                            <option value="1">Option 1</option>
                            <option value="2">Option 2</option>
                            <option value="3">Option 3</option>
                        </select>
                    </div>
                </form>
                <button type="submit">Rechercher</button>
            </div>
        </div>
    </section>
    <section class="reassurance pt-5 pb-5 bg-grey">
        <div class="container">
            <div class="row p-1">
                <div class="col-6 col-md-3 flex flex-col justify-center items-center">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/pro.svg" alt="">
                    <p class="uppercase xlbold text-lg text-center">+ 10 OOO professionnels</p>
                </div>
                <div class="col-6 col-md-3 flex flex-col justify-center items-center">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/monde.svg" alt="">
                    <p class="uppercase xlbold text-lg text-center">partout en France et dans les dom-tom</p>
                </div>
                <div class="col-6 col-md-3 flex flex-col justify-center items-center">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/qualite.svg" alt="">
                    <p class="uppercase xlbold text-lg text-center">qualité <br> verifiée</p>
                </div>
                <div class="col-6 col-md-3 flex flex-col justify-center items-center">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/clock.svg" alt="">
                    <p class="uppercase xlbold text-lg text-center">parcours <br> Simplifié</p>
                </div>
            </div>
        </div>
    </section>
    <section class="talent mt-5 mb-5">
        <h2 class="text-center smbold uppercase">Nos Talents</h2>
        <div class="container">
            <div class="splide" id="talentCarousel">
                <div class="splide__track">
                    <ul class="splide__list">
                        <!-- Slide 1 -->
                        <li class="splide__slide slide-type-1 col-md-5 col-sm-6 col-6">
                            <div class="talent-infos">
                                <img class="cover"
                                    src="<?php echo get_template_directory_uri(); ?>/assets/img/imgtest.jpg" alt="">
                                <div class="talent-text text-center">
                                    <div class="talent-infos-title">L'oasis pictural</div>
                                    <div class="talent-infos-professionnel">Annie, architecte d’intérieur</div>
                                    <div class="talent-infos-localisation">Bordeaux</div>
                                </div>
                            </div>
                        </li>
                        <!-- Slide 2 -->
                        <li class="splide__slide slide-type-2 col-md-3 col-sm-6 col-6">
                            <div class=" talent-infos">
                                <img class="cover"
                                    src="<?php echo get_template_directory_uri(); ?>/assets/img/imgtest2.jpg" alt="">
                                <div class="talent-text">
                                    <div class="talent-infos-title">L'oasis pictural</div>
                                    <div class="talent-infos-professionnel">Annie, architecte d’intérieur</div>
                                    <div class="talent-infos-localisation">Bordeaux</div>
                                </div>
                            </div>
                        </li>
                        <!-- Slide 3 -->
                        <li class="splide__slide slide-type-3 col-md-4 col-sm-6 col-6">
                            <div class="talent-infos">
                                <img class="cover"
                                    src="<?php echo get_template_directory_uri(); ?>/assets/img/imgtest3.jpg" alt="">
                                <div class="talent-text">
                                    <div class="talent-infos-title">L'oasis pictural</div>
                                    <div class="talent-infos-professionnel">Annie, architecte d’intérieur</div>
                                    <div class="talent-infos-localisation">Bordeaux</div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <div class="separator container flex justify-center p-5">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/pen.svg" alt="Separateur">
    </div>
    <section class="searchtalent">
        <div class="container">
            <div class="searchtalent-title">
                <p class="text-xl text-end uppercase">Showroom d’architecte met en relation des architectes <br>
                    qualifiés <br>
                    avec des porteurs <br>
                    de projets.</p>
            </div>
            <div class="searchtalent-slogan">
                <p>Quelle que soit la taille de votre projet, comparez et <br> trouvez ici votre architecte <br>
                    grâce à nos fiches détaillées et nos avis vérifiés.</p>
            </div>
            <div class="searchtalent-note">
                <p class="text-end text-sm mr-3">Architecte DPLG, architecte, ouvrier de France, architecte d’intérieur
                    et
                    paysagiste.</p>
            </div>
            <div class="m-5 text-center">
                <a href="#" class="btn btn-blue-dark">Je lance ma recherche</a>
            </div>
        </div>
    </section>
    <section class="findtalent mb-5">
        <div class="container">
            <div class="row items-center">
                <div class="col-md-4">
                    <div class="findtalent-title">
                        <div class="pr-1 pl-1 bold"><span class="ml-1">Grâce</span> à Showroom d'Architecte, nous avons
                            rencontré Anita
                            Leckmann, notre
                            architecte de
                            choc. Elle a compris de suite ce que nous voulions.</div>
                    </div>
                    <div class="mt-1">
                        <a href="#" class="btn btn-professionnel text-center">Trouver mon architecte</a>
                    </div>
                </div>
                <div class="col-md-8">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/shape.svg" alt="Separateur">
                </div>
            </div>
        </div>
    </section>
</div>

<?php
get_footer();