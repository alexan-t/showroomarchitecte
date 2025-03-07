<?php
if (!is_user_logged_in()) {
    echo '<p>Veuillez vous connecter pour voir vos projets.</p>';
    return;
}

global $wpdb, $current_user;
wp_get_current_user();

// Récupérer les projets de l'utilisateur connecté
$table_name = esc_sql($wpdb->prefix . 'projects');
$user_id = get_current_user_id();

$projects = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM $table_name WHERE user_id = %d ORDER BY created_at DESC",
        $user_id
    )
);

if (empty($projects)) {
    echo '<p>Vous n\'avez aucun projet enregistré.</p>';
    return;
}

// Générer le nonce
$manage_project_nonce = wp_create_nonce('manage_project_nonce');
?>

<input type="hidden" name="security" value="<?= esc_attr($manage_project_nonce); ?>">

<div class="mes-projets">
    <div class="px-2">
        <div class="container-title">
            <div class="">Nom du Projet</div>
            <div class="">Ouvert le</div>
            <div class="">Clôturé le</div>
            <div class="">État</div>
            <div class="">Actions</div>
        </div>
        <?php foreach ($projects as $project): ?>
        <div class="container-list">
            <div class=""><?= esc_html($project->project_name); ?></div>
            <div class=""><?= date('d/m/Y', strtotime($project->created_at)); ?></div>
            <div class="">
                <?= $project->closed_at ? date('d/m/Y', strtotime($project->closed_at)) : 'En cours'; ?>
            </div>
            <div class="">
                <span class="badge">
                    <?= $project->status === 'active' ? 'Actif' : 'Archivé'; ?>
                </span>
            </div>
            <div class="flex items-center gap-1 flex-wrap">
                <a
                    href="<?= esc_url(add_query_arg(['section' => 'edit-projet', 'project_id' => $project->id], site_url('/tableau-de-bord/'))); ?>">
                    <svg class="icon icon-xl" aria-hidden="true">
                        <use xlink:href="#edit"></use>
                    </svg>
                    <p class="sr-only">Modifier</p>
                </a>
                <?php if ($project->status === 'active'): ?>
                <a href="#" class="archive-button" data-id="<?= esc_attr($project->id); ?>">
                    <svg class="icon icon-xl" aria-hidden="true">
                        <use xlink:href="#archive"></use>
                    </svg>
                    <p class="sr-only">Archiver</p>
                </a>
                <a href="#" class="search-user" data-id="<?= esc_attr($project->id); ?>">
                    <svg class="icon icon-xl" aria-hidden="true">
                        <use xlink:href="#search-user"></use>
                    </svg>
                    <p class="sr-only">Rechercher un architecte</p>
                </a>
                <?php endif; ?>
                <a href="#" class="trash-button" data-id="<?= esc_attr($project->id); ?>">
                    <svg class="icon icon-xl" aria-hidden="true">
                        <use xlink:href="#trash"></use>
                    </svg>
                    <p class="sr-only">Supprimer</p>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".search-user").forEach(button => {
        button.addEventListener("click", (event) => {
            event.preventDefault();
            const projectId = event.target.closest(".search-user").dataset.id;

            if (!projectId) {
                Swal.fire("Erreur", "Impossible de récupérer l'ID du projet.", "error");
                return;
            }

            // Envoyer la requête AJAX pour récupérer les professionnels correspondants
            fetch(ajaxObject.ajaxUrl, {
                    method: "POST",
                    body: new URLSearchParams({
                        action: "get_matching_professionals_by_project",
                        project_id: projectId
                    }),
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showProfessionalsModal(data.data);
                    } else {
                        Swal.fire("Aucun résultat",
                            "Aucun professionnel trouvé pour ce projet.", "info");
                    }
                })
                .catch(error => console.error("Erreur lors de la récupération :", error));
        });
    });
});

/**
 * Affiche une modale avec les professionnels trouvés.
 * @param {Array} professionals - Liste des professionnels.
 */
/**
 * Affiche une modale avec les professionnels trouvés classés en trois sections.
 * @param {Object} data - Données contenant les professionnels triés.
 */
function showProfessionalsModal(data) {
    let {
        top_professionals,
        potential_interests,
        random_premium
    } = data;

    function generateProfessionalsHTML(professionals) {
        return professionals.length > 0 ? `
            <div class="row">
                ${professionals.map(pro => `
                    <div class="col-2">
                        <a href="${pro.profile_url}" class="color-${pro.pro_type}" target="_blank">
                            <div class="card-pro modal-card">
                                <div class="avatar">
                                    <img src="${pro.photo}" alt="${pro.name}">
                                </div>
                                <p class="name">${pro.name}</p>
                                <p class="city flex items-center">
                                    <svg class="icon icon-xl" aria-hidden="true">
                                        <use xlink:href="#marker"></use>
                                    </svg>
                                    <span class="color-blue">${pro.city}</span>
                                </p>
                            </div>
                        </a>
                    </div>
                `).join("")}
            </div>
        ` : "<p>Aucun professionnel disponible.</p>";
    }

    let htmlContent = `
        <div class="container">
            <h3 class="modal-section-title">🔝 Meilleures correspondances</h3>
            ${generateProfessionalsHTML(top_professionals)}

            <h3 class="modal-section-title">✨ Professionnels qui pourraient vous intéresser</h3>
            ${generateProfessionalsHTML(potential_interests)}

            <h3 class="modal-section-title">🏅 Professionnels Premium</h3>
            ${generateProfessionalsHTML(random_premium)}
        </div>
    `;

    Swal.fire({
        title: "Professionnels trouvés",
        html: htmlContent,
        width: "80%",
        showCloseButton: true,
        showConfirmButton: false
    });
}
</script>