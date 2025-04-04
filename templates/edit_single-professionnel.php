<?php
/*
Template Name: Edit Page Profil Professionnel
*/
get_header();

// Récupérer l'ID de l'utilisateur via l'URL
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user = get_user_by('ID', $user_id);



delete_transient('user_meta_' . $user_id);
wp_cache_delete($user_id, 'user_meta');

// Vérifier si l'utilisateur existe
if ($user) {
    // Récupération des informations utilisateur
    $first_name = get_user_meta($user_id, 'first_name', true);
    $last_name = get_user_meta($user_id, 'last_name', true);
    $description = get_user_meta($user_id, 'description', true);
    $background_image = get_user_meta($user_id, 'background_image', true);
    $profile_image = get_user_meta($user_id, 'profile_image', true);
    $user_img = !empty($profile_image) ? esc_url($profile_image) : get_avatar_url($user->ID);
    $projects = get_user_meta($user_id, 'recent_projects', true);
    $diplome = get_user_meta($user_id, 'diplome_principal', true);
    $experience = get_user_meta($user_id, 'annees_experience', true);
    $budget = get_user_meta($user_id, 'budget_moyen_chantiers', true);
    $motivation = get_user_meta($user_id, 'motivation_metier', true);
    $architect_types = get_user_meta($user_id, 'architecte_type', true);
    $architect_types = is_array($architect_types) ? implode(", ", $architect_types) : $architect_types;
    $has_pro_info = !empty($diplome) || !empty($experience) || !empty($budget);
    $has_specialty_info = !empty($architect_types) || !empty($motivation);
    // Définir l'image de fond par défaut si aucune n'est enregistrée
    $background_image_url = !empty($background_image) 
        ? esc_url($background_image) 
        : get_template_directory_uri() . '/assets/img/default-img-landing.jpg';
}

// Vérifier si l'utilisateur n'existe pas -> redirection vers 404
if (!$user) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part(404);
    exit();
}
$user_type = get_user_meta($user_id, 'user_type', true);
// Vérifier si l'utilisateur connecté est bien le propriétaire du profil

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

$current_user_id = get_current_user_id();
if ($current_user_id !== $user_id) {
    wp_redirect(home_url('/')); 
    exit;
}

?>


<section class="edit-single-page single-professionnel my-5">
    <div class="edit-page-pro container text-center my-3">
        <a href="<?php echo esc_url(site_url('/architectes/profil/?id=' . $user_id)); ?>"
            class="btn btn-<?php echo esc_attr($user_type); ?>">
            <svg class="icon icon-xl" aria-hidden="true">
                <use xlink:href="#close"></use>
            </svg>
        </a>
    </div>


    <div class="mx-4">
        <!-- Toggle page visible -->
        <p>
            <span class="bold">Afficher la page publiquement :</span>
            <input type="checkbox" id="switch" data-user-id="<?php echo esc_attr($user_id); ?>"
                <?php checked(get_user_meta($user_id, 'is_page_public', true), '1'); ?> />
            <label for="switch">Toggle</label>
        </p>
    </div>

    <div class="landing">
        <!-- Nom et prénom (non modifiable) -->
        <div class="single-professionnel-name">
            <p class="uppercase bold color-gray-dark"><?php echo esc_html($first_name); ?>
                <?php echo esc_html($last_name); ?></p>
        </div>

        <!-- Image de fond cliquable -->
        <div class="landing-img edit-img" id="background-dropzone"
            data-background="<?php echo esc_url($background_image_url); ?>"
            style="background-image: url('<?php echo esc_url($background_image_url); ?>');">
            <div class="container h-100">
                <div class="edit-img-text">
                    <p>Modifier cette image</p>
                </div>
            </div>
        </div>

        <!-- Input caché pour sélectionner une image -->
        <input type="file" id="background-input" accept="image/*" style="display: none;">
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
                <!-- Photo de profil cliquable -->
                <div class="col-md-6 text-center">
                    <div class="image_effect">
                        <figure>
                            <img style="max-width : 300px; height: 300px; object-fit: cover; border-radius: 50%"
                                id="profile-image" class="edit-profil-image" src="<?php echo esc_url($user_img)  ?>"
                                alt="Editer Photo de profil" data-profile="<?php echo esc_url($user_img); ?>">
                        </figure>
                        <span>Modifier l'Image de Profil</span>
                    </div>
                    <!-- Input caché pour l'upload -->
                    <input type="file" id="profile-input" accept="image/*" style="display: none;">
                </div>

                <div class="col-md-6 flex flex-col items-center gap-1">
                    <div id="quill-editor" style="height: 100%;width: 100%;"></div>
                    <input type="hidden" id="editable-description" name="content"
                        value="<?php echo esc_html($description); ?>">

                    <!--Bouton pour enregistrer la description -->
                    <button id="save-description" class="btn btn-blue">Enregistrer</button>
                    <p id="description-error" style="color: red; display: none;"></p>
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
            <div class="row">
                <div class="col-md-6 flex items-center justify-center">
                    <div class="info-box">
                        <h3 class="bold color-gray-dark">À propos du professionnel</h3>

                        <p><span class="bold">Diplôme :</span>
                            <input type="text" id="diplome" class="editable-input"
                                value="<?php echo esc_attr($diplome); ?>">
                        </p>

                        <p><span class="bold">Années d'expérience :</span>
                            <input type="number" id="experience" class="editable-input"
                                value="<?php echo esc_attr($experience); ?>" min="0">
                        </p>

                        <p><span class="bold">Budget moyen :</span>
                            <input type="number" id="budget" class="editable-input"
                                value="<?php echo esc_attr($budget); ?>" min="0">
                        </p>
                    </div>
                </div>

                <div class="col-md-6 flex items-center justify-center">
                    <div class="info-box">
                        <h3 class="bold color-gray-dark">Spécialités et motivation</h3>

                        <p><span class="bold">Type d'Architecte :</span>
                            <select id="architect_types" class="editable-input">
                                <option value="" disabled selected>Choisissez un type...</option>
                                <option value="Architecte" <?php selected($architect_types, "Architecte"); ?>>Architecte
                                </option>
                                <option value="Architecte intérieur"
                                    <?php selected($architect_types, "Architecte intérieur"); ?>>Architecte intérieur
                                </option>
                                <option value="Architecte diplômé d'État"
                                    <?php selected($architect_types, "Architecte diplômé d'État"); ?>>Architecte diplômé
                                    d'État</option>
                                <option value="Architecte paysagiste"
                                    <?php selected($architect_types, "Architecte paysagiste"); ?>>Architecte paysagiste
                                </option>
                            </select>
                        </p>


                        <p><span class="bold">Pourquoi ce métier ?</span>
                            <textarea id="motivation"
                                class="editable-textarea"><?php echo esc_textarea($motivation); ?></textarea>
                        </p>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <button id="save-pro-profile-info" class="btn btn-blue">Enregistrer</button>
                <p id="save-message" style="color: green; display: none;"></p>
            </div>
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


    <div class="portfolio p-relative mt-3">
        <div class="container">
            <?php if (!empty($projects)) : ?>
            <div class="row">
                <?php foreach ($projects as $index => $project) : ?>
                <div class="<?php echo $col_class; ?>">
                    <div class="project">
                        <?php if (!empty($project['image'])) : ?>
                        <div class="edit-project-wrapper">
                            <figure>
                                <img class="bg-img-project project-image edit-project-image"
                                    src="<?php echo esc_url($project['image']); ?>"
                                    alt="Image du projet <?php echo esc_html($project['title']); ?>"
                                    data-project-id="<?php echo $index; ?>"
                                    data-project-title="<?php echo esc_attr($project['title']); ?>"
                                    data-project-description="<?php echo esc_attr($project['description']); ?>"
                                    data-project-budget="<?php echo esc_attr($project['budget']); ?>"
                                    data-project-surface="<?php echo esc_attr($project['surface']); ?>"
                                    data-project-duration="<?php echo esc_attr($project['duration']); ?>"
                                    data-project-additional-images='<?php echo json_encode($project['additional_images']); ?>'
                                    style="max-width: 100%; max-height: 400px;">
                            </figure>
                            <button class="delete-project" data-project-id="<?php echo $index; ?>">
                                ❌ Supprimer
                            </button>
                        </div>

                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <div class="add-project text-center">
                <button id="add-project-btn" class="btn btn-<?php echo esc_attr($user_type); ?>">Ajouter un
                    projet</button>
            </div>
        </div>
    </div>

</section>




<?php
get_footer();
?>