<?php
if (!is_user_logged_in()) {
    echo '<p>Veuillez vous connecter pour voir vos projets.</p>';
    return;
}

global $wpdb, $current_user;
wp_get_current_user();

// Récupérer les projets de l'utilisateur connecté
$table_name = $wpdb->prefix . 'projects';
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
?>

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
            <div class=" flex items-center gap-1">
                <a
                    href="<?php echo esc_url( add_query_arg(['section' => 'edit-projet', 'project_id' => $project->id], site_url('/tableau-de-bord/'))); ?>">
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
//JS AJAX Pour achiver
document.addEventListener("DOMContentLoaded", () => {
    const archiveButtons = document.querySelectorAll('[data-id][href="#"][class*="archive"]');

    archiveButtons.forEach(button => {
        button.addEventListener("click", (event) => {
            event.preventDefault();

            const projectId = button.getAttribute("data-id");

            Swal.fire({
                title: "Êtes-vous sûr ?",
                text: "Ce projet sera archivé.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Oui, archiver",
                cancelButtonText: "Annuler"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Envoyer la requête AJAX
                    fetch(ajaxObject.ajaxUrl, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded",
                            },
                            body: new URLSearchParams({
                                action: "archive_project",
                                project_id: projectId,
                            }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: "Succès",
                                    text: data.data,
                                    icon: "success",
                                }).then(() => {
                                    location.reload(); // Rafraîchir la page
                                });
                            } else {
                                Swal.fire({
                                    title: "Erreur",
                                    text: data.data ||
                                        "Une erreur est survenue.",
                                    icon: "error",
                                });
                            }
                        })
                        .catch(error => {
                            console.error("Erreur réseau :", error);
                            Swal.fire({
                                title: "Erreur réseau",
                                text: "Impossible de contacter le serveur.",
                                icon: "error",
                            });
                        });
                }
            });
        });
    });


    // JS AJAX FONCTION POUR SUPPRIMER PROJET
    const deleteButtons = document.querySelectorAll('[data-id][href="#"][class*="trash"]');

    deleteButtons.forEach(button => {
        button.addEventListener("click", (event) => {
            event.preventDefault();

            const projectId = button.getAttribute("data-id");

            Swal.fire({
                title: "Êtes-vous sûr ?",
                text: "Ce projet sera supprimé définitivement.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Oui, supprimer",
                cancelButtonText: "Annuler"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Envoyer la requête AJAX
                    fetch(ajaxObject.ajaxUrl, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded",
                            },
                            body: new URLSearchParams({
                                action: "delete_project",
                                project_id: projectId,
                            }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: "Succès",
                                    text: data.data,
                                    icon: "success",
                                }).then(() => {
                                    location.reload(); // Rafraîchir la page
                                });
                            } else {
                                Swal.fire({
                                    title: "Erreur",
                                    text: data.data ||
                                        "Une erreur est survenue lors de la suppression.",
                                    icon: "error",
                                });
                            }
                        })
                        .catch(error => {
                            console.error("Erreur réseau :", error);
                            Swal.fire({
                                title: "Erreur réseau",
                                text: "Impossible de contacter le serveur.",
                                icon: "error",
                            });
                        });
                }
            });
        });
    });
});
</script>