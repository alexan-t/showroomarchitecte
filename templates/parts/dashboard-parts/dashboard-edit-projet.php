<?php
if (!is_user_logged_in()) {
    echo '<p>Veuillez vous connecter pour éditer un projet.</p>';
    return;
}

if (!isset($_GET['project_id']) || empty($_GET['project_id'])) {
    echo '<p>Projet non spécifié.</p>';
    return;
}

// Initialisation
global $wpdb;
$project_id = intval($_GET['project_id']);
$user_id = get_current_user_id();

// Récupérer le projet de l'utilisateur connecté
$table_name = $wpdb->prefix . 'projects';
$project = $wpdb->get_row(
    $wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d AND user_id = %d",
        $project_id,
        $user_id
    )
);

if (!$project) {
    echo '<p>Projet introuvable ou vous n\'êtes pas autorisé à le modifier.</p>';
    return;
}

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitiser les données envoyées
    $updated_data = [
        'search' => sanitize_text_field($_POST['search']),
        'property' => sanitize_text_field($_POST['property']),
        'proprietaire' => sanitize_text_field($_POST['proprietaire']),
        'projet' => sanitize_text_field($_POST['projet']),
        'city' => sanitize_text_field($_POST['city']),
        'budget' => sanitize_text_field($_POST['budget']),
        'total_surface' => sanitize_text_field($_POST['total_surface']),
        'work_surface' => sanitize_text_field($_POST['work_surface']),
        'project_name' => sanitize_text_field($_POST['project_name']),
        'project_description' => sanitize_textarea_field($_POST['project_description']),
        'needs' => maybe_serialize($_POST['needs'] ?? []),
    ];

    // Mettre à jour dans la base de données
    $updated = $wpdb->update(
        $table_name,
        $updated_data,
        [ 'id' => $project_id, 'user_id' => $user_id ],
        [
            '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'
        ],
        [ '%d', '%d' ]
    );

    if ($updated !== false) {
        echo '<p class="success">Le projet a été mis à jour avec succès.</p>';
        $project = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d AND user_id = %d",
                $project_id,
                $user_id
            )
        ); // Rafraîchir les données du projet
    } else {
        echo '<p class="error">Une erreur s\'est produite lors de la mise à jour du projet.</p>';
    }
}
?>

<div class="edit-project">
    <div class="h3">Éditer le projet : <?= esc_html($project->project_name); ?></div>
    <form method="POST">
        <input type="hidden" id="project_id" value="<?php echo esc_attr($project->id); ?>">
        <!-- Je recherche -->
        <div class="step">
            <h2 for="search">Je recherche*</h2>
            <p>Actuellement : <?php echo $project->search ;?> </p>
            <div class="list-options">
                <label class="<?= $project->search === 'Architecte' ? 'selected' : ''; ?>">
                    <input type="radio" name="search" value="Architecte" id="search_Architecte"
                        <?= $project->search === 'Architecte' ? 'checked' : ''; ?>> Architecte
                </label>
                <label class="<?= $project->search === 'Architecte intérieur' ? 'selected' : ''; ?>">
                    <input type="radio" name="search" value="Architecte intérieur" id="search_ArchitecteInterieur"
                        <?= $project->search === 'Architecte intérieur' ? 'checked' : ''; ?>> Architecte intérieur
                </label>
                <label class="<?= $project->search === 'Architecte diplômé d\'Etat' ? 'selected' : ''; ?>">
                    <input type="radio" name="search" value="Architecte diplômé d\'Etat" id="search_ArchitecteDiplome"
                        <?= $project->search === 'Architecte diplômé d\'Etat' ? 'checked' : ''; ?>> Architecte diplômé
                    d'Etat
                </label>
                <label class="<?= $project->search === 'Architecte paysagiste' ? 'selected' : ''; ?>">
                    <input type="radio" name="search" value="Architecte paysagiste" id="search_ArchitectePaysagiste"
                        <?= $project->search === 'Architecte paysagiste' ? 'checked' : ''; ?>> Architecte paysagiste
                </label>
            </div>
        </div>

        <!-- Type de bien -->
        <div class="step my-3">
            <h2 for="property">Type de bien*</h2>
            <p>Actuellement : <?php echo $project->property ;?> </p>
            <div class="list-options">
                <label class="<?= $project->property === 'Maison individuelle' ? 'selected' : ''; ?>">
                    <input type="radio" name="property" value="Maison individuelle"
                        <?= $project->property === 'Maison individuelle' ? 'checked' : ''; ?>> Maison individuelle
                </label>
                <label class="<?= $project->property === 'Appartement' ? 'selected' : ''; ?>">
                    <input type="radio" name="property" value="Appartement"
                        <?= $project->property === 'Appartement' ? 'checked' : ''; ?>> Appartement
                </label>
                <label class="<?= $project->property === 'Commerce' ? 'selected' : ''; ?>">
                    <input type="radio" name="property" value="Commerce"
                        <?= $project->property === 'Commerce' ? 'checked' : ''; ?>> Commerce
                </label>
                <label class="<?= $project->property === 'Bâtiment professionnel' ? 'selected' : ''; ?>">
                    <input type="radio" name="property" value="Bâtiment professionnel"
                        <?= $project->property === 'Bâtiment professionnel' ? 'checked' : ''; ?>> Bâtiment professionnel
                </label>
                <label class="<?= $project->property === 'Bâtiment collectivité' ? 'selected' : ''; ?>">
                    <input type="radio" name="property" value="Bâtiment collectivité"
                        <?= $project->property === 'Bâtiment collectivité' ? 'checked' : ''; ?>> Bâtiment collectivité
                </label>
            </div>
        </div>


        <!-- Proprietaire -->
        <div class="step my-3">
            <h2 for="proprietaire">Propriétaire*</h2>
            <p>Actuellement : <?php echo $project->proprietaire ;?> </p>
            <div class="list-options">
                <label class="<?= $project->proprietaire === 'Particulier' ? 'selected' : ''; ?>">
                    <input type="radio" name="proprietaire" value="Particulier"
                        <?= $project->proprietaire === 'Particulier' ? 'checked' : ''; ?>> Particulier
                </label>
                <label class="<?= $project->proprietaire === 'Professionnel' ? 'selected' : ''; ?>">
                    <input type="radio" name="proprietaire" value="Professionnel"
                        <?= $project->proprietaire === 'Professionnel' ? 'checked' : ''; ?>> Professionnel
                </label>
                <label class="<?= $project->proprietaire === 'Public' ? 'selected' : ''; ?>">
                    <input type="radio" name="proprietaire" value="Public"
                        <?= $project->proprietaire === 'Public' ? 'checked' : ''; ?>> Public
                </label>
            </div>
        </div>


        <!-- Votre Projet*-->
        <div class="step my-3">
            <h2 for="projet">Votre Projet*</h2>
            <p>Actuellement : <?php echo $project->projet ;?> </p>
            <div class="list-options">
                <label class="<?= $project->projet === 'Construire' ? 'selected' : ''; ?>">
                    <input type="radio" name="projet" value="Construire"
                        <?= $project->projet === 'Construire' ? 'checked' : ''; ?>> Construire
                </label>
                <label class="<?= $project->projet === 'Rénover' ? 'selected' : ''; ?>">
                    <input type="radio" name="projet" value="Rénover"
                        <?= $project->projet === 'Rénover' ? 'checked' : ''; ?>> Rénover
                </label>
                <label class="<?= $project->projet === 'Réhabiliter' ? 'selected' : ''; ?>">
                    <input type="radio" name="projet" value="Réhabiliter"
                        <?= $project->projet === 'Réhabiliter' ? 'checked' : ''; ?>> Réhabiliter
                </label>
                <label class="<?= $project->projet === 'Agrandir' ? 'selected' : ''; ?>">
                    <input type="radio" name="projet" value="Agrandir"
                        <?= $project->projet === 'Agrandir' ? 'checked' : ''; ?>> Agrandir
                </label>
                <label class="<?= $project->projet === 'Convertir' ? 'selected' : ''; ?>">
                    <input type="radio" name="projet" value="Convertir"
                        <?= $project->projet === 'Convertir' ? 'checked' : ''; ?>> Convertir
                </label>
                <label class="<?= $project->projet === 'Aménager' ? 'selected' : ''; ?>">
                    <input type="radio" name="projet" value="Aménager"
                        <?= $project->projet === 'Aménager' ? 'checked' : ''; ?>> Aménager
                </label>
                <label class="<?= $project->projet === 'Décorer' ? 'selected' : ''; ?>">
                    <input type="radio" name="projet" value="Décorer"
                        <?= $project->projet === 'Décorer' ? 'checked' : ''; ?>> Décorer
                </label>
            </div>
        </div>

        <!-- Ville -->
        <div class="my-3">
            <label class="color-blue text-md bold underline" for="city">Ville*</label>
            <input type="text" name="city" id="city" class="custom-input" value="<?= esc_attr($project->city); ?>"
                required>
        </div>
        <div class="row my-3">
            <!-- Surfaces -->
            <div class="col-md-4">
                <label class="color-blue text-md bold underline" for="total_surface">Surface totale*</label>
                <input type="number" name="total_surface" id="total_surface"
                    value="<?= esc_attr($project->total_surface); ?>" required>
            </div>

            <div class="col-md-4">
                <label class="color-blue text-md bold underline" for="work_surface">Surface des travaux*</label>
                <input type="number" name="work_surface" id="work_surface"
                    value="<?= esc_attr($project->work_surface); ?>" required>
            </div>

            <!-- Budget -->
            <div class="col-md-4">
                <label class="color-blue text-md bold underline" for="budget">Budget prévisionnel*</label>
                <input type="number" name="budget" id="budget" value="<?= esc_attr($project->budget); ?>" required>
            </div>
        </div>

        <!-- Besoins -->
        <div class="my-3">
            <label class="color-blue text-md bold underline" for="needs">Besoins spécifiques*</label>
            <div class="">
                <?php
                $needs = maybe_unserialize($project->needs);
                $options = [
                    'Architecte', 'Plans', 'Dessins techniques', 'Supervision des équipes', 'Sélection des artisans', 'Autorisations et permis', 'Achats matériaux', 'À définir ensemble'
                ];
                foreach ($options as $option) {
                    $checked = is_array($needs) && in_array($option, $needs) ? 'checked' : '';
                    echo "<div class='form-group'>
                    <input type='checkbox' name='needs[]' id='{$option}' value='{$option}' {$checked}>
                    <label for='{$option}'> {$option}</label>
                    </div>";
                }
                ?>
            </div>
        </div>

        <!-- Nom et Description du projet -->
        <div class="my-3">
            <label class="color-blue text-md bold underline" for="project_name">Nom du projet*</label>
            <input type="text" id="project_name" name="project_name" value="<?= esc_attr($project->project_name); ?>"
                required>
        </div>

        <div class="my-3">
            <label class="color-blue text-md bold underline" for="project_description">Description du projet*</label>
            <textarea name="project_description" id="project_description"
                required><?= esc_textarea($project->project_description); ?></textarea>
        </div>

        <button type="submit" class="btn btn-blue">Mettre à jour</button>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // --- Votre code existant pour la gestion des boutons radio ---
    const handleRadioSelection = (event) => {
        const radio = event.target;
        if (radio.type === "radio") {
            const radios = document.querySelectorAll(`input[name="${radio.name}"]`);
            radios.forEach((input) => {
                const label = input.closest("label");
                if (label) label.classList.remove("selected");
            });
            const selectedLabel = radio.closest("label");
            if (selectedLabel) selectedLabel.classList.add("selected");
        }
    };

    document.querySelectorAll('input[type="radio"]').forEach((radio) => {
        if (radio.checked) {
            const selectedLabel = radio.closest("label");
            if (selectedLabel) selectedLabel.classList.add("selected");
        }
        radio.addEventListener("change", handleRadioSelection);
    });
    // --- Fin de votre code radio ---

    const redirectUrl =
        "<?php echo esc_url( add_query_arg( 'section', 'mes-projets', site_url('/tableau-de-bord/') ) ); ?>";

    const form = document.querySelector(".edit-project form");

    if (form) {
        form.addEventListener("submit", async (e) => {
            e.preventDefault();

            const projectId = document.getElementById("project_id").value;
            const search = document.querySelector('input[name="search"]:checked').value;
            const property = document.querySelector('input[name="property"]:checked').value;
            const proprietaire = document.querySelector('input[name="proprietaire"]:checked').value;
            const projet = document.querySelector('input[name="projet"]:checked').value;

            const projectName = document.getElementById("project_name").value;
            const city = document.getElementById("city").value;
            const budget = document.getElementById("budget").value;
            const projectDescription = document.getElementById("project_description").value;
            const project_name = document.getElementById("project_name").value;
            const total_surface = document.getElementById("total_surface").value;
            const work_surface = document.getElementById("work_surface").value;
            const needsElements = document.querySelectorAll('input[name="needs[]"]:checked');
            const needs = Array.from(needsElements).map(el => el.value);

            const formData = new URLSearchParams();
            formData.append('action', 'update_project');
            formData.append('project_id', projectId);
            formData.append('search', search);
            formData.append('property', property);
            formData.append('proprietaire', proprietaire);
            formData.append('total_surface', total_surface);
            formData.append('work_surface', work_surface);
            formData.append('projet', projet);
            formData.append('project_name', projectName);
            formData.append('city', city);
            formData.append('budget', budget);
            formData.append('project_description', projectDescription);
            needs.forEach(need => formData.append('needs[]', need));

            try {
                const response = await fetch(ajaxObject.ajaxUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: formData.toString()
                });

                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        title: 'Projet mis à jour avec succès !',
                        text: 'Vous allez être redirigé.',
                        icon: 'success',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });

                    setTimeout(() => {
                        window.location.href = redirectUrl;
                    }, 3000);
                } else {
                    Swal.fire({
                        title: 'Erreur !',
                        text: result.data || 'Erreur lors de la mise à jour.',
                        icon: 'error'
                    });
                }
            } catch (error) {
                Swal.fire({
                    title: 'Erreur réseau',
                    text: 'Impossible de contacter le serveur.',
                    icon: 'error'
                });
            }
        });
    }
});
</script>