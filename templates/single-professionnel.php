<?php
/*
Template Name: Page Profil Professionnel
*/
get_header();

// Récupérer l'ID de l'utilisateur via l'URL
$user_id_page = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user = get_user_by('ID', $user_id_page);

// Vérifier si l'utilisateur existe
if ($user) {
    //Variable pour savoir si la page est public
    $is_page_public = get_user_meta($user_id_page, 'is_page_public', true);

    // Récupérer les informations utilisateur
    $first_name = get_user_meta($user_id_page, 'first_name', true);
    $last_name = get_user_meta($user_id_page, 'last_name', true);
    $email = $user->user_email;
    $telephone = get_user_meta($user_id_page, 'telephone', true);
    $address = get_user_meta($user_id_page, 'address', true);
    $city = get_user_meta($user_id_page, 'city', true);
    $postalcode = get_user_meta($user_id_page, 'postalcode', true);
    $description = get_user_meta($user_id_page, 'description', true);
    $profile_image = get_user_meta($user_id_page, 'profile_image', true);
    $user_type = get_user_meta($user_id_page, 'user_type', true);
    $background_image = get_user_meta($user_id_page, 'background_image', true);
    $projects = get_user_meta($user_id_page, 'recent_projects', true);
    $diplome = get_user_meta($user_id_page, 'diplome_principal', true);
    $experience = get_user_meta($user_id_page, 'annees_experience', true);
    $budget = get_user_meta($user_id_page, 'budget_moyen_chantiers', true);
    $motivation = get_user_meta($user_id_page, 'motivation_metier', true);
    $architect_types = get_user_meta($user_id_page, 'architecte_type', true);
    $architect_types = is_array($architect_types) ? implode(", ", $architect_types) : $architect_types; 
    $user_img = !empty($profile_image) ? esc_url($profile_image) : get_avatar_url($user->ID);
    // Vérifie si des informations existent pour chaque bloc
    $has_pro_info = !empty($diplome) || !empty($experience) || !empty($budget);
    $has_specialty_info = !empty($architect_types) || !empty($motivation);

    // Si aucun projet, initialiser un tableau vide
    if (!$projects) {
        $projects = [];
    }
    $project_count = count($projects); // Compter le nombre de projets

    // Déterminer la classe Bootstrap en fonction du nombre de projets
    $col_class = 'col-md-4'; // Par défaut pour 3 projets ou plus
    if ($project_count === 1) {
        $col_class = 'col-md-12';
    } elseif ($project_count === 2) {
        $col_class = 'col-md-6';
    }

    // Vérifier si l'utilisateur connecté est le propriétaire du profil et n'est pas un particulier
    $current_user_id_page = get_current_user_id();
    $current_user_type = get_user_meta($current_user_id_page, 'user_type', true);
    $is_owner = ($current_user_id_page === $user_id_page && $current_user_type !== 'particulier');

    // Définir l'image de fond : utiliser l'image téléversée si elle existe, sinon l'image par défaut
    $background_image_url = !empty($background_image) 
    ? esc_url($background_image) 
    : get_template_directory_uri() . '/assets/img/default-img-landing.jpg';

}

if ($is_page_public !== '1' && !$is_owner) {
    // Si la page n'est pas publique et que l'utilisateur n'est pas propriétaire, rediriger vers une 404
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part(404);
    exit();
}
?>

<section class="single-professionnel my-5">
    <?php if ($is_owner) : ?>
    <div class="edit-page-pro container text-center my-3">
        <a href="<?php echo esc_url(showroom_get_edit_profile_url($user_id_page)); ?>"
            class="btn btn-<?php echo esc_attr($user_type); ?>">
            <svg class="icon icon-xl" aria-hidden="true">
                <use xlink:href="#user-edit"></use>
            </svg>
        </a>
    </div>
    <?php endif; ?>



    <?php 
    if (isset($is_page_public) && $is_page_public === '0' && $is_owner) : ?>
    <div class="mx-4">
        <p class="color-primary bold">
            Votre page n'est actuellement pas visible.<br>
            Rendez-la visible directement dans le mode édition.
        </p>
    </div>
    <?php endif; ?>


    <div class="landing">
        <div class="single-professionnel-name">
            <p class="uppercase bold color-gray-dark"><?php echo esc_html($first_name); ?>
                <?php echo esc_html($last_name); ?></p>
        </div>
        <div class="landing-img" id="background-preview"
            style="background-image: url('<?php echo esc_url($background_image_url); ?>');">
        </div>
    </div>

    <div class="container">
        <div class="separator">
            <div class="line"></div>
            <div class="step-line">
                01 . à propos
            </div>
        </div>
    </div>

    <div class="about py-3">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <a href="<?php echo esc_url($user_img); ?>" class="glightbox flex justify-center">
                        <img src="<?php echo esc_url($user_img); ?>" alt="Photo de profil"
                            style="max-width : 300px; height: 300px; object-fit: cover; border-radius: 50%; z-index: -1;">
                    </a>
                </div>
                <div class="col-md-6 flex items-center">
                    <div class="description-profil-page">
                        <?php 
                    $description = get_user_meta($user_id_page, 'description', true);
                    echo !empty($description) ? afficher_description_formattee($description) : "L'utilisateur n'a pas mis de description."; 
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="separator">
            <div class="line"></div>
            <div class="step-line">
                02 . professionnel
            </div>
        </div>
    </div>

    <div class="about-pro py-3">
        <div class="container">
            <?php if (empty($has_pro_info) && empty($has_specialty_info)) : ?>
            <div class="text-center py-5">
                <p class="bold color-gray-dark">Aucune information professionnelle n'a été fournie pour le moment.</p>
            </div>
            <?php else : ?>
            <div class="row">
                <?php if ($has_pro_info) : ?>
                <div
                    class="<?php echo $has_specialty_info ? 'col-md-6' : 'col-md-12'; ?> flex items-center justify-center">
                    <div class="info-box">
                        <h3 class="bold color-gray-dark">À propos du professionnel</h3>

                        <?php if (!empty($diplome)) : ?>
                        <p><span class="bold">Diplôme :</span> <?php echo esc_html($diplome); ?></p>
                        <?php endif; ?>

                        <?php if (!empty($experience)) : ?>
                        <p><span class="bold">Années d'expérience :</span> <?php echo esc_html($experience); ?> ans</p>
                        <?php endif; ?>

                        <?php if (!empty($budget)) : ?>
                        <p><span class="bold">Budget moyen :</span> <?php echo esc_html($budget); ?> €</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php if ($has_specialty_info) : ?>
                <div class="<?php echo $has_pro_info ? 'col-md-6' : 'col-md-12'; ?> flex items-center justify-center">
                    <div class="info-box">
                        <h3 class="bold color-gray-dark">Spécialités et motivation</h3>

                        <?php if (!empty($architect_types)) : ?>
                        <p><span class="bold">Type d'Architecte :</span> <?php echo esc_html($architect_types); ?></p>
                        <?php endif; ?>

                        <?php if (!empty($motivation)) : ?>
                        <p><span class="bold">Pourquoi ce métier ?</span> <?php echo esc_html($motivation); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <div class="separator">
            <div class="line"></div>
            <div class="step-line">
                03 . portfolio
            </div>
        </div>
    </div>



    <div class="portfolio mt-3">
        <div class="container">
            <div class="row">
                <?php foreach ($projects as $index => $project) : ?>
                <div class="<?php echo $col_class; ?>">
                    <?php if (!empty($project['image'])) : ?>
                    <!-- Image du projet avec un identifiant unique -->
                    <img class="bg-img-project profil-project-image" src="<?php echo esc_url($project['image']); ?>"
                        alt="Image du projet" data-project-id="<?php echo $index; ?>"
                        data-userpage-id="<?php echo $user_id_page ?>"
                        style="max-width: 100%; height: auto; cursor: pointer">
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="separator">
            <div class="line"></div>
            <div class="step-line">
                04 . avis
            </div>
        </div>
    </div>

    <div class="avis my-5">
        <?php  include get_template_directory() . '/templates/parts/reviews-part.php'; ?>
    </div>





    <?php if($current_user_type === "particulier") : ?>
    <div class="container">
        <div class="separator">
            <div class="line"></div>
            <div class="step-line">
                05 . contact
            </div>
        </div>
    </div>

    <div class="contact">
        <div class="mt-3 flex justify-center">
            <?php
            // $user_id contient l'ID de l'architecte.
            // On appelle le shortcode pour générer le bouton + JS 
            echo do_shortcode('[prendre_contact_button user_id="' . $user_id_page . '"]');
        ?>
        </div>
    </div>

    <?php endif ?>
    <!-- <img src="<?php echo esc_url(get_userdata($review->user_id)->profile_image); ?>"
    alt="Photo de profil"> -->
</section>
<?php
get_footer();