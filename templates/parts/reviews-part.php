<?php
        global $wpdb;
        $table_name = $wpdb->prefix . 'professional_reviews';
        $professional_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $current_user_id = get_current_user_id();

        // Récupérer tous les avis pour ce professionnel
        $reviews = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $table_name WHERE professional_id = %d ORDER BY created_at DESC", $professional_id)
        );

        // Récupérer l'avis de l'utilisateur connecté pour ce professionnel
        $user_review = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE user_id = %d AND professional_id = %d",
                $current_user_id,
                $user_id_page
            )
        );
?>


<div class="container text-center">
    <?php if (!empty($reviews)) : ?>
    <div class="splide" id="reviews-slider">
        <div class="splide__track">
            <ul class="splide__list reviews-list">
                <?php 
                // Découpe les avis en groupes de 2
                $reviews_chunked = array_chunk($reviews, 2); 

                foreach ($reviews_chunked as $reviews_pair) : ?>
                <li class="splide__slide">
                    <div class="row flex-col justify-center items-center p-1">
                        <?php foreach ($reviews_pair as $review) : ?>
                        <div class="col-md-6 flex justify-center">
                            <div class="review-item">
                                <div class="row items-center">
                                    <div class="col-md-3">
                                        <img src="<?php echo esc_url(get_userdata($review->user_id)->profile_image); ?>"
                                            alt="Photo de profil">
                                    </div>
                                    <div class="col-md-9 flex flex-start">
                                        <p>
                                            <strong><?php echo esc_html(get_userdata($review->user_id)->first_name); ?></strong>
                                            <strong><?php echo esc_html(get_userdata($review->user_id)->last_name); ?></strong>
                                        </p>
                                    </div>
                                </div>
                                <div class="review-text">
                                    <div class="p-1 text-start">
                                        <!-- Suppression de "flex" ici -->
                                        <p class="text-wrapper">
                                            <?php echo esc_html($review->review_text); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mt-1">
                                        <span><?php echo date('d/m/Y', strtotime($review->created_at)); ?></span>
                                    </div>
                                    <?php if ($review->user_id == $current_user_id) : ?>
                                    <div class="col-md-6 text-right">
                                        <button class="delete-review" data-id="<?php echo $review->id; ?>">
                                            <svg class="icon icon-xl" aria-hidden="true">
                                                <use xlink:href="#delete"></use>
                                            </svg>
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php else : ?>
    <p>Soyez le premier à donner votre avis !</p>
    <?php endif; ?>
    <?php if (get_user_meta($current_user_id, 'user_type', true) === "particulier") : ?>

    <!-- Seuls les utilisateurs qui ne sont pas le propriétaire peuvent ajouter un avis -->
    <div class="flex flex-col mt-4">
        <div class="flex justify-center">
            <textarea class="review-write" id="user_review" style="resize:none"></textarea>
        </div>
        <div class="text-center">
            <small id="charCount">0/100</small>
        </div>
        <div class="justify-center flex mt-1">
            <div class="text-center">
                <?php if ($user_review) : ?>
                <button class="btn btn-blue" data-action="update">Modifier votre avis</button>
                <?php else : ?>
                <button class="btn btn-blue" data-action="add">Ajouter votre avis</button>
                <?php endif; ?>
            </div>
        </div>

    </div>
    <?php endif; ?>
</div>