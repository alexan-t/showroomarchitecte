<?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'realisation';
    $realisations = get_query_var('realisations') ?? [];
    $total_pages = get_query_var('total_pages') ?? 1;
    $current_page = get_query_var('current_page') ?? 1;
?>
<?php if (is_array($realisations) && count($realisations) > 0): ?>

<div class="row">
    <?php foreach ($realisations as $realisation) : ?>
    <div class="col-md-4">
        <div class="card-realisation">
            <div class="card-realisation-img">
                <!-- Main image (ouverture de la galerie) -->
                <a href="<?php echo esc_url($realisation->image); ?>" class="glightbox"
                    data-gallery="real-<?php echo $realisation->id; ?>"
                    data-glightbox="title: <?php echo esc_attr($realisation->title); ?>; description: <?php echo esc_attr(wp_strip_all_tags($realisation->description)); ?>">
                    <img class="main-image" src="<?php echo esc_url($realisation->image); ?>" alt="">
                </a>

                <?php
                // Désérialisation sécurisée
                $additional_images = maybe_unserialize($realisation->additional_images);
                $additional_images = is_array($additional_images) ? $additional_images : [];
                ?>

                <!-- Additional images masquées mais dans la même galerie -->
                <div class="sr-only">
                    <?php foreach ($additional_images as $img) : ?>
                    <a href="<?php echo esc_url($img); ?>" class="glightbox"
                        data-gallery="real-<?php echo $realisation->id; ?>"
                        data-glightbox="title: <?php echo esc_attr($realisation->title); ?>; description: <?php echo esc_attr(wp_strip_all_tags($realisation->description)); ?>">
                    </a>
                    <?php endforeach; ?>
                </div>

                <div class="count-img">
                    <?php echo (1 + count($additional_images)) . ' photo' . (count($additional_images) ? 's' : ''); ?>
                </div>
            </div>

            <div class="card-realisation-infos">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="card-realisation-infos-title text-md bold-500"><?php echo $realisation->title ?></h3>
                    </div>
                    <div class="col-md-4 end">
                        <p class="card-realisation-infos-surface"><span class="sr-only">Surface:</span> <span
                                class="bold-500"><?php echo $realisation->surface ?></span></p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-realisation-infos-budget"><span class="sr-only">Budget:</span> <span
                                class="bold-500"><?php echo $realisation->budget ?></span></p>
                    </div>
                    <div class="col-md-6 end">
                        <p class="card-realisation-infos-duree">
                            <span class="sr-only">
                                Durée:
                            </span> <span class="bold-500"><?php echo $realisation->duration ?></span>
                        </p>
                    </div>
                    <div class="cold-md-12">
                        <p class="card-realisation-infos-description">
                            <span class="sr-only">
                                Description:
                            </span>
                            <?php echo $realisation->description ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<?php if ($total_pages > 1): ?>
<ul id="pagination" class="pagination mt-4">
    <?php
    if ($current_page > 1) {
        echo '<li><button class="pagination-btn" data-page="' . ($current_page - 1) . '">Précédent</button></li>';
    }

    for ($i = 1; $i <= $total_pages; $i++) {
        $active = $i === $current_page ? 'active' : '';
        echo '<li><button class="pagination-btn ' . $active . '" data-page="' . $i . '">' . $i . '</button></li>';
    }

    if ($current_page < $total_pages) {
        echo '<li><button class="pagination-btn" data-page="' . ($current_page + 1) . '">Suivant</button></li>';
    }
    ?>
</ul>
<?php endif; ?>