<div class="col-md-4 mb-4">
    <div class="card-realisation">
        <div class="card-realisation-img">
            <img class="main-image" src="<?php echo esc_url($realisation->image); ?>"
                alt="<?php echo esc_attr($realisation->titre); ?>">

            <div class="sr-only additionnal-images">
                <img class="add-img" src="<?php echo esc_url($realisation->image); ?>" alt="">
            </div>

            <div class="count-img">1 photo</div> <!-- À adapter si tu gères plusieurs images -->
        </div>

        <div class="card-realisation-infos">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-realisation-infos-title text-md bold-500">
                        <?php echo esc_html($realisation->titre); ?>
                    </h3>
                </div>

                <div class="col-md-6">
                    <p class="card-realisation-infos-surface">
                        <span class="sr-only">Surface:</span>
                        <span class="bold-500"><?php echo esc_html($realisation->surface); ?></span>
                    </p>
                </div>

                <div class="col-md-6">
                    <p class="card-realisation-infos-budget">
                        <span class="sr-only">Budget:</span>
                        <span class="bold-500"><?php echo esc_html($realisation->budget); ?></span>
                    </p>
                </div>

                <div class="col-md-6">
                    <p class="card-realisation-infos-duree">
                        <span class="sr-only">Durée:</span>
                        <span class="bold-500"><?php echo esc_html($realisation->duree); ?></span>
                    </p>
                </div>

                <div class="col-md-12">
                    <p class="card-realisation-infos-description"
                        style="overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                        <span class="sr-only">Description:</span>
                        <?php echo esc_html($realisation->description); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>