<?php
global $wpdb;

// Fonction pour récupérer 1 seule réalisation par utilisateur, pour un ou plusieurs user_types
function get_unique_realisations($user_types, $limit = 4, $already_ids = []) {
    $args = [
        'meta_key'   => 'user_type',
        'meta_value' => $user_types,
        'meta_compare' => is_array($user_types) ? 'IN' : '=',
        'fields'     => 'ID'
    ];

    $user_ids = get_users($args);
    $realisations = [];

    foreach ($user_ids as $user_id) {
        // Skip si déjà utilisé
        if (in_array($user_id, $already_ids)) continue;

        global $wpdb;
        $real = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}realisation 
                 WHERE user_id = %d 
                 ORDER BY RAND() 
                 LIMIT 1",
                $user_id
            )
        );

        if ($real) {
            $realisations[] = $real;
            $already_ids[] = $user_id;
        }

        if (count($realisations) >= $limit) break;
    }

    return [$realisations, $already_ids];
}

// Init
$realisations = [];
$used_user_ids = [];

// Étape 1 - pro-premium
list($step1, $used_user_ids) = get_unique_realisations('pro-premium', 4, $used_user_ids);
$realisations = array_merge($realisations, $step1);

// Étape 2 - pro-gold
if (count($realisations) < 4) {
    list($step2, $used_user_ids) = get_unique_realisations('pro-gold', 4 - count($realisations), $used_user_ids);
    $realisations = array_merge($realisations, $step2);
}

// Étape 3 - professionnel
if (count($realisations) < 4) {
    list($step3, $used_user_ids) = get_unique_realisations('professionnel', 4 - count($realisations), $used_user_ids);
    $realisations = array_merge($realisations, $step3);
}
?>

<?php if (!empty($realisations)) : ?>
<section class="py-5">
    <div class="container">
        <h2 class="font-GildaDisplay bold-100 text-5xl">Les dernières réalisations</h2>
        <div class="row mt-5">
            <?php foreach ($realisations as $index => $realisation): ?>
            <?php
                    $ratio = ($index === 0 || $index === 3) ? 'ratio-4x3' : 'ratio-1x1';
                    $image_url = esc_url($realisation->image);
                ?>
            <div class="col-md-6 <?php echo ($index === 2) ? 'pt-5' : ''; ?>">
                <div class="projet projet-item <?php echo ($index % 2 === 1) ? 'mt-5' : ''; ?>">
                    <figure class="ratio <?php echo $ratio; ?>">
                        <img class="projet-item-image ratio-item" src="<?php echo $image_url; ?>" alt="">
                    </figure>
                    <div class="projet-item-infos pt-2">
                        <h5><?php echo esc_html($realisation->title); ?></h5>
                        <p><?php echo esc_html(wp_trim_words($realisation->description, 20)); ?></p>
                        <a href="<?php echo site_url('/projet/?id=' . $realisation->id); ?>">Voir le projet</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>