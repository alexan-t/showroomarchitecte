<?php
/*
Template Name: Page Profil Professionnel
*/
get_header();

// Récupérer l'ID de l'utilisateur via l'URL
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user = get_user_by('ID', $user_id);

// Vérifier si l'utilisateur existe
if ($user) {
    // Récupérer les informations utilisateur
    $first_name = get_user_meta($user_id, 'first_name', true);
    $last_name = get_user_meta($user_id, 'last_name', true);
    $email = $user->user_email;
    $telephone = get_user_meta($user_id, 'telephone', true);
    $adress = get_user_meta($user_id, 'adress', true);
    $city = get_user_meta($user_id, 'city', true);
    $postalcode = get_user_meta($user_id, 'postalcode', true);
    $description = get_user_meta($user_id, 'description', true);
    $profile_image = get_user_meta($user_id, 'profile_image', true);
    $user_type = get_user_meta($user_id, 'user_type', true);
    $background_image = get_user_meta($user_id, 'background_image', true);
    $projects = get_user_meta($user_id, 'recent_projects', true);
    $diplome = get_user_meta($user_id, 'diplome_principal', true);
    $experience = get_user_meta($user_id, 'annees_experience', true);
    $budget = get_user_meta($user_id, 'budget_moyen_chantiers', true);
    $motivation = get_user_meta($user_id, 'motivation_metier', true);
    $architect_types = get_user_meta($user_id, 'architecte_type', true);
    $architect_types = is_array($architect_types) ? implode(", ", $architect_types) : $architect_types; 

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
    $current_user_id = get_current_user_id();
    $current_user_type = get_user_meta($current_user_id, 'user_type', true);
    $is_owner = ($current_user_id === $user_id && $current_user_type !== 'particulier');

    // Définir l'image de fond : utiliser l'image téléversée si elle existe, sinon l'image par défaut
    $background_image_url = !empty($background_image) 
    ? esc_url($background_image) 
    : get_template_directory_uri() . '/assets/img/default-img-landing.jpg';

}

if (!$user) {
    // Redirection vers la page 404 si l'ID est incorrect
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part(404); // Charge le template 404.php
    exit();
}
?>

<section class="single-professionnel my-5">
    <?php if ($is_owner) : ?>
    <div class="edit-page-pro container text-center my-3">
        <a href="<?php echo esc_url(admin_url('profile.php')); ?>" class="btn btn-<?php echo $user_type ?>">
            <svg class="icon icon-xl" aria-hidden="true">
                <use xlink:href="#user-edit"></use>
            </svg>
        </a>
    </div>
    <?php endif; ?>
    <div class="landing">
        <div class="single-professionnel-name">
            <p class="uppercase bold color-gray-dark"><?php echo esc_html($first_name); ?>
                <?php echo esc_html($last_name); ?></p>
        </div>
        <div class="landing-img" style="background-image: url('<?php echo $background_image_url; ?>');"></div>
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
                    <img src="<?php echo !empty($profile_image) ? esc_url($profile_image) : get_template_directory_uri() . '/assets/img/blue-circle.svg'; ?>"
                        alt="Photo de profil">
                </div>
                <div class="col-md-6 flex items-center">
                    <p class="px-3">
                        <?php echo !empty($description) ? esc_html($description) : "L'utilisateur n'a pas mis de description."; ?>
                    </p>
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
                    <div class="project">
                        <?php if (!empty($project['image'])) : ?>
                        <!-- Image du projet avec un identifiant unique -->
                        <img class="bg-img-project" src="<?php echo esc_url($project['image']); ?>"
                            alt="Image du projet" data-modal-id="modal-<?php echo $index; ?>"
                            style="max-width: 100%; height: auto;">
                        <!-- Modal associé -->
                        <div class="modal-overlay" id="modal-<?php echo $index; ?>" style="display: none;">
                            <div class="modal-project">
                                <div class="close-modal">
                                    <svg class="icon icon-xl" aria-hidden="true">
                                        <use xlink:href="#close"></use>
                                    </svg>
                                </div>
                                <div class="row justify-center items-center">
                                    <div class="col-md-6">
                                        <img class="main_image" src="<?php echo esc_url($project['image']); ?>"
                                            alt="Image du projet">

                                        <div class="splide" id="image-carousel-<?php echo $index; ?>">
                                            <div class="splide__track">
                                                <ul class="splide__list">
                                                    <li class="splide__slide">
                                                        <img class="thumbnail"
                                                            src="<?php echo esc_url($project['image']); ?>"
                                                            alt="Image principale">
                                                    </li>
                                                    <?php 
                                            $additional_images = isset($project['additional_images']) ? $project['additional_images'] : [];
                                            foreach ($additional_images as $image_url) : ?>
                                                    <li class="splide__slide">
                                                        <img class="thumbnail" src="<?php echo esc_url($image_url); ?>"
                                                            alt="Image du Projet">
                                                    </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="project-infos">
                                            <?php if (!empty($project['title'])) : ?>
                                            <p><span class="bold">Type :</span>
                                                <?php echo esc_html($project['title']); ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($project['description'])) : ?>
                                            <p><span class="bold">Description :</span>
                                                <?php echo esc_html($project['description']); ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($project['budget'])) : ?>
                                            <p><span class="bold">Budget total du chantier :</span>
                                                <?php echo esc_html($project['budget']); ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($project['surface'])) : ?>
                                            <p><span class="bold">Surface totale :</span>
                                                <?php echo esc_html($project['surface']); ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($project['duration'])) : ?>
                                            <p><span class="bold">Durée du chantier :</span>
                                                <?php echo esc_html($project['duration']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
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
</section>

<script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.3/dist/js/splide.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation de Splide
    document.querySelectorAll('.splide').forEach(function(carousel) {
        if (carousel) { // Vérifie si l'élément existe
            new Splide(carousel, {
                type: 'slide',
                perPage: 4,
                perMove: 1,
                gap: '10px',
                pagination: false,
                arrows: true,
                breakpoints: {
                    768: {
                        perPage: 3
                    },
                    480: {
                        perPage: 2
                    }
                }
            }).mount();
        }
    });

    // Gestion du clic sur les miniatures pour changer l'image principale
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.querySelector('.main_image');

    thumbnails.forEach(function(thumbnail) {
        thumbnail.addEventListener('click', function() {
            const newSrc = this.src;

            // 1. Retirer la classe 'active' de toutes les miniatures
            thumbnails.forEach(thumb => thumb.classList.remove('active'));

            // 2. Ajouter la classe 'active' à la miniature cliquée
            this.classList.add('active');

            // 3. Animation de transition pour l'image principale
            mainImage.style.opacity = 0;
            setTimeout(() => {
                mainImage.src = newSrc;
                mainImage.style.opacity = 1;
            }, 150);
        });
    });

    // Ajouter la classe 'active' à la première miniature au chargement
    if (thumbnails.length > 0) {
        thumbnails[0].classList.add('active');
    }

    // Ouvrir le modal en cliquant sur l'image du projet
    document.querySelectorAll('.bg-img-project').forEach(function(image) {
        image.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-id');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex'; // Affiche le modal
            }
        });
    });

    // Fermer le modal en cliquant sur le bouton de fermeture ou à l'extérieur du modal
    document.querySelectorAll('.modal-overlay').forEach(function(modal) {
        // Fermeture en cliquant sur .close-modal
        modal.querySelector('.close-modal').addEventListener('click', function() {
            modal.style.display = 'none';
        });

        // Fermeture en cliquant à l'extérieur de la modal (mais pas sur le contenu)
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });

});
</script>
<?php
get_footer();