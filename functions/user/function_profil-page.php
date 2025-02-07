<?php

// Ajouter un menu personnalisé pour les utilisateurs professionnels
function add_custom_profile_menu() {
    if (current_user_can('read')) {
        $user_type = get_user_meta(get_current_user_id(), 'user_type', true);
        if ($user_type === 'professionnel' || $user_type === 'gold' || $user_type === 'premium') {
            add_menu_page(
                'Modifier ma Page Portfolio',
                'Modifier ma Page Portfolio',
                'read',
                'edit-professionnel',
                'custom_profile_page_callback',
                'dashicons-admin-users',
                6
            );
        }
    }
}
add_action('admin_menu', 'add_custom_profile_menu');

// Charger le gestionnaire de médias WordPress
function load_wp_media_files($hook) {
    if ($hook != 'toplevel_page_edit-professionnel') {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_script('jquery');
}
add_action('admin_enqueue_scripts', 'load_wp_media_files');

// Fonction de rappel pour afficher la page
function custom_profile_page_callback() {
    $user_id = get_current_user_id();
    $background_image = get_user_meta($user_id, 'background_image', true);
    $description = get_user_meta($user_id, 'description', true);
    $projects = get_user_meta($user_id, 'recent_projects', true);
    $user_type = get_user_meta($user_id, 'user_type', true);

    if (!$projects) $projects = [];
    $max_projects = 1;
    if ($user_type === 'gold') {
        $max_projects = 3;
    } elseif ($user_type === 'premium') {
        $max_projects = PHP_INT_MAX;
    }

    // Sécurité
    $nonce = wp_create_nonce('save_profile_data');
    ?>
<style>
.flex {
    display: flex;
}

.flex-col {
    flex-direction: column;
}

.mb-3 {
    margin-bottom: 3rem;
}

.w-maxcontent {
    width: max-content;
}

.image-preview img {
    border: 1px solid #ddd;
    padding: 5px;
    background: #f9f9f9;
}

.gap-1 {
    gap: 1rem;
}

.w-500px {
    width: 500px;
}

textarea {
    width: 100%;
    height: 200px;
    resize: none;
}

.button-danger {
    background-color: red;
    color: white;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
}

.button-danger:hover {
    background-color: darkred;
}

.project-header {
    background-color: #f0f0f0;
    padding: 10px;
    cursor: pointer;
    font-weight: bold;
    border: 1px solid #ccc;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.justify-between {
    justify-content: space-between;
}

.accordion-project {
    display: grid;
    grid-template-columns: 20% 40% 40%;
}

.image-item {
    max-width: 29%;
    display: flex;
    flex-direction: column;
    padding: 1rem;
}

.additional-images-preview {
    display: flex;
    flex-wrap: wrap;
}
</style>
<div class="wrap">
    <h1>Modifier ma Page Portfolio</h1>
    <form method="post" action="">
        <input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>">
        <div class="flex gap-1 mb-3">
            <label>Image de Fond :</label>
            <input type="text" name="background_image" value="<?php echo esc_url($background_image); ?>"
                class="regular-text" />
            <button type="button" class="w-maxcontent button upload_background_image">Télécharger une image</button>
        </div>
        <div class="flex gap-1 mb-3">
            <label>Description :</label>
            <textarea name="description" rows="5"><?php echo esc_textarea($description); ?></textarea>
        </div>
        <h3>Projets :</h3>
        <div id="projects_wrapper">
            <?php foreach ($projects as $index => $project) : 
        $project_image = isset($project['image']) ? esc_url($project['image']) : ''; 
    ?>
            <div class="project" data-project-index="<?php echo $index; ?>">
                <!-- En-tête du projet avec le bouton de suppression -->
                <div class="project-header"
                    style="background: #f5f5f5; padding: 10px; cursor: pointer; border: 1px solid #ccc; display: flex; justify-content: space-between; align-items: center;">
                    <strong><?php echo esc_attr($project['title']) ?: "Projet " . ($index + 1); ?></strong>
                    <button type="button" class="button button-danger delete-project"
                        data-project-index="<?php echo $index; ?>"
                        data-nonce="<?php echo wp_create_nonce('delete_project_' . $index); ?>">
                        Supprimer
                    </button>
                </div>

                <!-- Détails du projet (masqué par défaut) - Placé correctement à l'intérieur de .project -->
                <div class="project-details"
                    style="display: none; padding: 10px; border: 1px solid #ccc; border-top: none;">
                    <div class="grid accordion-project">

                        <div class="gap-1 flex flex-col">
                            <input type="text" name="projects[<?php echo $index; ?>][title]"
                                value="<?php echo esc_attr($project['title']); ?>" placeholder="Titre"
                                class="regular-text" />
                            <textarea
                                name="projects[<?php echo $index; ?>][description]"><?php echo esc_textarea($project['description']); ?></textarea>

                            <input type="text" name="projects[<?php echo $index; ?>][budget]"
                                value="<?php echo esc_attr($project['budget']); ?>" placeholder="Budget (€)" />

                            <input type="text" name="projects[<?php echo $index; ?>][surface]"
                                value="<?php echo esc_attr($project['surface']); ?>" placeholder="Surface (m²)" />

                            <input type="text" name="projects[<?php echo $index; ?>][duration]"
                                value="<?php echo esc_attr($project['duration']); ?>" placeholder="Durée (mois)" />

                        </div>

                        <!-- Image principale -->
                        <div class="flex flex-col" style="padding: 0 1rem">
                            <button type="button" class="button upload_project_image">Sélectionner une autre image de
                                couverture</button>
                            <input type="hidden" name="projects[<?php echo $index; ?>][image]"
                                value="<?php echo $project_image; ?>" />
                            <div class="image-preview">
                                <?php if ($project_image) : ?>
                                <img src="<?php echo $project_image; ?>" style="max-width: 100%;" />
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- Images supplémentaires -->
                        <div class="col-md-3 flex flex-col">
                            <button type="button" class="button upload_additional_images">Ajouter des images
                                supplémentaires</button>
                            <div class="additional-images-preview">
                                <?php
                $additional_images = $project['additional_images'] ?? [];
                foreach ($additional_images as $image_url) :
                ?>
                                <div class="image-item">
                                    <img src="<?php echo esc_url($image_url); ?>" style="max-width: 100%;" />
                                    <input type="hidden" name="projects[<?php echo $index; ?>][additional_images][]"
                                        value="<?php echo esc_url($image_url); ?>" />
                                    <button type="button" class="remove-image">Supprimer</button>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- FIN DU .project -->
            <?php endforeach; ?>
        </div>
        <button type="button" id="add_project" class="button"
            <?php echo count($projects) >= $max_projects ? 'disabled' : ''; ?>>Ajouter un projet</button>

        <?php submit_button('Enregistrer les modifications'); ?>
    </form>
</div>


<script>
jQuery(document).ready(function($) {

    // Fonction d'accordéon
    $(document).on('click', '.project-header', function(e) {
        if (!$(e.target).closest('button').length) { // Empêche l'accordéon si clic sur un bouton
            const details = $(this).siblings('.project-details'); // Correct: chercher le sibling direct
            $('.project-details').not(details).slideUp(); // Ferme les autres projets
            details.slideToggle(); // Ouvre/ferme celui cliqué
        }
    });


    // Suppression du projet
    $(document).on('click', '.delete-project', function() {
        const projectIndex = $(this).data('project-index');
        const nonce = $(this).data('nonce');

        if (confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'delete_project',
                    project_index: projectIndex,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Projet supprimé avec succès.');
                        location
                            .reload(); // Rafraîchir la page pour refléter les changements
                    } else {
                        alert('Erreur : ' + response.data);
                    }
                },
                error: function() {
                    alert('Une erreur est survenue.');
                }
            });
        }
    });


    let projectIndex = <?php echo count($projects); ?>;
    const maxProjects = <?php echo $max_projects; ?>;

    $('#add_project').on('click', function() {
        if (projectIndex < maxProjects) {
            let projectTemplate = `
                    <div class="project flex flex-col gap-1">
                        <input class="w-500px" type="text" name="projects[${projectIndex}][title]" placeholder="Titre" class="regular-text" />
                        <textarea name="projects[${projectIndex}][description]" placeholder="Description"></textarea>
                        <input class="w-500px" type="text" name="projects[${projectIndex}][budget]" placeholder="Budget (€)" min="0" step="1000" />
                        <input class="w-500px" type="text" name="projects[${projectIndex}][surface]" placeholder="Surface (m²)" min="0" step="1" />
                        <input class="w-500px" type="text" name="projects[${projectIndex}][duration]" placeholder="Durée (mois)" />

                        <button type="button" class="w-maxcontent button upload_project_image">Sélectionner une image de couverture</button>
                        <input type="hidden" name="projects[${projectIndex}][image]" value="" />
                        <div class="image-preview" style="margin-top: 10px;">
                            <img src="" alt="Aperçu de l'image" style="max-width: 500px; display: none;" />
                        </div>
                        <label>Images supplémentaires :</label>
                        <button type="button" class="button upload_additional_images w-500px">Ajouter des images</button>
                        <div class="additional-images-preview" style="margin-top: 10px;"></div>
                    </div>
                </div>`;
            $('#projects_wrapper').append(projectTemplate);
            projectIndex++;
            if (projectIndex >= maxProjects) {
                $('#add_project').prop('disabled', true);
            }
        }
    });

    $(document).on('click', '.upload_project_image', function(e) {
        e.preventDefault();
        const button = $(this);
        const hiddenInput = button.next('input[type="hidden"]');
        const previewImage = button.nextAll('.image-preview').find('img');

        const mediaUploader = wp.media({
            title: 'Sélectionner une image',
            button: {
                text: 'Utiliser cette image'
            },
            multiple: false
        });

        mediaUploader.on('select', function() {
            const attachment = mediaUploader.state().get('selection').first().toJSON();

            // Mise à jour de l'URL dans le champ caché
            hiddenInput.val(attachment.url);

            // Mise à jour de l'aperçu de l'image
            previewImage.attr('src', attachment.url).show();
        });

        mediaUploader.open();
    });
});

jQuery(document).ready(function($) {
    // Fonction pour uploader les images supplémentaires
    $(document).on('click', '.upload_additional_images', function(e) {
        e.preventDefault();
        const button = $(this);
        const previewContainer = button.siblings('.additional-images-preview');

        const mediaUploader = wp.media({
            title: 'Sélectionner des images',
            button: {
                text: 'Utiliser ces images'
            },
            multiple: true
        });

        mediaUploader.on('select', function() {
            const attachments = mediaUploader.state().get('selection').toJSON();

            attachments.forEach(attachment => {
                const imageItem = `
                    <div class="image-item">
                        <img src="${attachment.url}" style="max-width: 100%;" />
                        <input type="hidden" name="${button.closest('.project').find('input[name^="projects"]').attr('name').replace('[title]', '')}[additional_images][]" value="${attachment.url}" />
                        <button type="button" class="remove-image" style="max-width: 300px; ">Supprimer</button>
                    </div>
                `;
                previewContainer.append(imageItem);
            });
        });

        mediaUploader.open();
    });

    // Fonction pour supprimer une image
    $(document).on('click', '.remove-image', function() {
        $(this).parent('.image-item').remove();
    });
});
</script>
<?php
}

// Enregistrement des données
function save_custom_profile_fields() {
    if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'save_profile_data')) {
        $user_id = get_current_user_id();

        if (isset($_POST['background_image'])) {
            update_user_meta($user_id, 'background_image', esc_url_raw($_POST['background_image']));
        }

        if (isset($_POST['description'])) {
            update_user_meta($user_id, 'description', sanitize_textarea_field($_POST['description']));
        }

        
        if (isset($_POST['projects'])) {
            $projects = array_map(function ($project) {
                return [
                    'title'       => sanitize_text_field($project['title']),
                    'description' => sanitize_textarea_field($project['description']),
                    'image'       => esc_url_raw($project['image']),
                    'budget'      => isset($project['budget']) ? sanitize_text_field($project['budget']) : 0,
                    'surface'     => isset($project['surface']) ? sanitize_text_field($project['surface']) : 0,
                    'duration'    => isset($project['duration']) ? sanitize_text_field($project['duration']) : 0,
                    'additional_images' => isset($project['additional_images']) ? array_map('esc_url_raw', $project['additional_images']) : []
                ];
            }, $_POST['projects']);

            update_user_meta($user_id, 'recent_projects', $projects);
        }
    }
}
add_action('admin_init', 'save_custom_profile_fields');

// Suppression des données du projet
function delete_project() {
    if (isset($_POST['action']) && $_POST['action'] === 'delete_project') {
        $project_index = intval($_POST['project_index']);
        $nonce = $_POST['nonce'];

        if (wp_verify_nonce($nonce, 'delete_project_' . $project_index)) {
            $user_id = get_current_user_id();
            $projects = get_user_meta($user_id, 'recent_projects', true);

            if (is_array($projects) && isset($projects[$project_index])) {
                unset($projects[$project_index]);
                $projects = array_values($projects); // Réindexer les projets

                update_user_meta($user_id, 'recent_projects', $projects);

                wp_send_json_success('Projet supprimé avec succès.');
            } else {
                wp_send_json_error('Projet non trouvé.');
            }
        } else {
            wp_send_json_error('Échec de la vérification de sécurité.');
        }
    }
}
add_action('wp_ajax_delete_project', 'delete_project');



// Autorisation de téléversement
function grant_upload_permission_to_professionals() {
    $current_user = wp_get_current_user();
    $user_type = get_user_meta($current_user->ID, 'user_type', true);

    if ($user_type !== 'particulier' && !current_user_can('upload_files')) {
        $role = get_role('subscriber');
        if ($role) {
            $role->add_cap('upload_files');
        }
    }
}
add_action('admin_init', 'grant_upload_permission_to_professionals');