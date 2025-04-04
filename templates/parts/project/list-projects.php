<?php
if (!defined('ABSPATH')) {
    exit;
}

$projects = get_query_var('projects');


if (!empty($projects)) :
?>
<ul class="projects-list">
    <div class="row">
        <?php foreach ($projects as $project) : ?>
        <?php 
        $client_id = $project->user_id;
        $first_name = get_user_meta($client_id, 'first_name', true);
        $last_name = get_user_meta($client_id, 'last_name', true);
        $profile_image = get_user_meta($client_id, 'profile_image', true);
        $attachments = maybe_unserialize($project->attachments); 
        $needs = maybe_unserialize($project->needs);
        ?>
        <div class="card-project col-md-6 scale">
            <h4 class="bold-500"><?= esc_html($project->project_name); ?></h4>
            <ul class="mb-1">
                <li class="flex gap-1">
                    <ion-icon name="home-outline"></ion-icon>
                    <p class="bold-500"><?= esc_html($project->property); ?></p>
                </li>
                <li class="flex gap-1">
                    <ion-icon name="hammer-outline"></ion-icon>
                    <p class="bold-500"><?= esc_html($project->total_surface); ?>m²</p>
                </li>
                <li class="flex gap-1">
                    <ion-icon name="logo-euro"></ion-icon>
                    <p class="bold-500"><?= esc_html($project->budget); ?>€</p>
                </li>
                <li class="flex gap-1">
                    <ion-icon name="navigate-outline"></ion-icon>
                    <p class="bold-500"><?= esc_html($project->city); ?></p>
                </li>
                <li class="flex gap-1 items-center">
                    <ion-icon name="eye-outline"></ion-icon>
                    <button class="bold-500 rounded-5 btn btn-blue flex items-center open-project-details"
                        data-name="<?= esc_attr($project->project_name); ?>"
                        data-description="<?= esc_attr($project->project_description); ?>"
                        data-needs='<?= json_encode(maybe_unserialize($project->needs)); ?>'
                        data-proprietaire="<?= esc_attr($project->proprietaire); ?>"
                        data-projet="<?= esc_attr($project->projet); ?>"
                        data-surface="<?= esc_attr($project->total_surface); ?>"
                        data-work="<?= esc_attr($project->work_surface); ?>"
                        data-budget="<?= esc_attr($project->budget); ?>" data-city="<?= esc_attr($project->city); ?>"
                        data-attachments='<?= esc_attr(json_encode($attachments)); ?>'
                        data-client_name="<?= esc_attr($first_name . ' ' . $last_name); ?>"
                        data-client_img="<?= esc_url($profile_image); ?>"
                        data-timeline="<?= esc_attr($project->project_start_date); ?>"
                        data-status="<?= esc_attr($project->status); ?>">
                        En savoir plus
                    </button>
                </li>
            </ul>
            <?php
                if (is_user_logged_in()) {
                    $current_user_id = get_current_user_id();
                    $user_type = get_user_meta($current_user_id, 'user_type', true);

                    if ($user_type === 'pro-gold') {
                        echo get_prendre_contact_button($project->user_id, 'Contacter le client');
                    } else {
                        echo '<p class="text-muted">Il faut être <span class="bold-500 color-pro-gold">membres Gold</span> pour pouvoir contacter le client</p>';
                    }
                } else {
                    echo '<p><a href="' . esc_url(site_url('/connexion')) . '" class="btn btn-dark">Connectez-vous pour contacter</a></p>';
                }
                ?>

        </div>

        </li>
        <?php endforeach; ?>
    </div>
</ul>
<?php
$current_page = get_query_var('current_page') ?? 1;
$total_pages = get_query_var('total_pages') ?? 1;
?>
<?php if ($total_pages > 1): ?>
<ul id="pagination">
    <?php
    $range = 1;
    $ellipsis_shown = false;

    // Bouton « précédent »
    if ($current_page > 1) {
        echo '<li><button class="pagination-btn" data-page="' . ($current_page - 1) . '">Précédent</button></li>';
    }

    for ($i = 1; $i <= $total_pages; $i++) {
        if (
            $i == 1 || 
            $i == $total_pages || 
            abs($i - $current_page) <= $range
        ) {
            $activeClass = ($i == $current_page) ? 'active' : '';
            echo '<li><button class="pagination-btn ' . $activeClass . '" data-page="' . $i . '">' . $i . '</button></li>';
            $ellipsis_shown = false;
        } else {
            if (!$ellipsis_shown) {
                echo '<li><span class="px-2">…</span></li>';
                $ellipsis_shown = true;
            }
        }
    }

    // Bouton « suivant »
    if ($current_page < $total_pages) {
        echo '<li><button class="pagination-btn" data-page="' . ($current_page + 1) . '">Suivant</button></li>';
    }
    ?>
</ul>
<?php endif; ?>



<?php else : ?>
<div id="empty-lottie" style="width:300px; margin:50px auto;"></div>
<p style="text-align:center; font-weight:500;">Aucun projet trouvé pour le moment.</p>
<?php endif; ?>