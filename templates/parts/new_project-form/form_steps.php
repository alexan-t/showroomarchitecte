<?php
session_start();

if (isset($_POST['step'])) {
    $step = intval($_POST['step']);

    // Enregistrer les choix précédents dans la session
    if (isset($_POST['selected'])) {
        foreach ($_POST['selected'] as $key => $value) {
            $_SESSION['form_data'][$key] = sanitize_text_field($value, ENT_QUOTES, 'UTF-8');
        }
    }

    // Récupérer les données enregistrées
    $formData = $_SESSION['form_data'] ?? [];

    switch ($step) {
        case 1:
            // Étape 1 et Étape 2 combinées
            !empty($selectedSearch) && $selectedSearch = $formData['search'] ?? '';
            !empty($selectedProperty) && $selectedProperty  = $formData['property'] ?? '';

            echo '<div class="steps-group">
                    <div class="step">
                        <h2>Je recherche*</h2>
                       <div class="list-options text-center">
                        <label for="Architecte">
                          <input id="Architecte" type="radio" name="search" value="Architecte" ' . (!empty($selectedSearch) && $selectedSearch === 'Architecte' ? 'checked' : '') . ' required>
                          Architecte
                        </label>
                        <label>
                          <input type="radio" name="search" value="Architecte intérieur" ' . (!empty($selectedSearch) && $selectedSearch === 'Architecte intérieur' ? 'checked' : '') . ' required>
                          Architecte intérieur
                        </label>
                        <label>
                          <input type="radio" name="search" value="Architecte diplômé d\'Etat" ' . (!empty($selectedSearch) && $selectedSearch === 'Architecte diplômé d\'Etat' ? 'checked' : '') . ' required>
                          Architecte diplômé d\'Etat
                        </label>
                         <label>
                          <input type="radio" name="search" value="Architecte paysagiste" ' . (!empty($selectedSearch) && $selectedSearch === 'Architecte paysagiste' ? 'checked' : '') . ' required>
                          Architecte paysagiste
                        </label>
                      </div>
                    </div>
                    <div class="step my-5">
                        <h2>Type de bien*</h2>
                        <div class="list-options text-center">
                          <label>
                            <input type="radio" name="property" value="Maison individuelle" ' . (!empty($selectedProperty) && $selectedProperty === 'Maison individuelle' ? 'checked' : '') . ' required> Maison individuelle
                          </label>
                          <label>
                            <input type="radio" name="property" value="Appartement" ' . (!empty($selectedProperty) && $selectedProperty === 'Appartement' ? 'checked' : '') . ' required> Appartement
                          </label>
                          <label>
                            <input type="radio" name="property" value="Commerce" ' . (!empty($selectedProperty) && $selectedProperty === 'Commerce' ? 'checked' : '') . ' required> Commerce
                          </label>
                          <label>
                            <input type="radio" name="property" value="Bâtiment professionnel" ' . (!empty($selectedProperty) && $selectedProperty === 'Bâtiment professionnel' ? 'checked' : '') . ' required> Bâtiment professionnel
                          </label>
                          <label>
                            <input type="radio" name="property" value="Bâtiment collectivité" ' . (!empty($selectedProperty) && $selectedProperty === 'Bâtiment collectivité' ? 'checked' : '') . ' required> Bâtiment collectivité
                          </label>
                        </div>
                    </div>
                    <div class="text-end">
                      <button class="btn btn-blue" onclick="nextStep(2)">Suivant</button></div>
                  </div>';
            break;

        case 2:
            // Étape 3 et 4
            !empty($$selectedProprietaire) && $$selectedProprietaire = $formData['proprietaire'] ?? '';
            !empty($selectedProjet) && $selectedProjet = $formData['projet'] ?? '';

            echo '<div class="steps-group">
                    <div class="step">
                        <h2>Propriétaire*</h2>
                       <div class="list-options text-center">
                        <label>
                          <input type="radio" name="proprietaire" value="Particulier" ' . ( !empty($selectedProprietaire) && $selectedProprietaire  === 'Particulier' ? 'checked' : '') . ' required>
                          Particulier
                        </label>
                        <label>
                          <input type="radio" name="proprietaire" value="Professionnel" ' . (!empty($$selectedProprietaire) && $$selectedProprietaire  === 'Professionnel' ? 'checked' : '') . ' required>
                          Professionnel
                        </label>
                        <label>
                          <input type="radio" name="proprietaire" value="Public" ' . (!empty($$selectedProprietaire) && $$selectedProprietaire  === 'Public' ? 'checked' : '') . ' required>
                          Public
                        </label>
                      </div>
                    </div>
                    <div class="step my-5">
                        <h2>Votre Projet*</h2>
                        <div class="list-options text-center">
                          <label>
                            <input type="radio" name="projet" value="Construire" ' . (!empty($selectedProjet) && $selectedProjet === 'Construire' ? 'checked' : '') . ' required> Construire
                          </label>
                          <label>
                            <input type="radio" name="projet" value="Rénover" ' . (!empty($selectedProjet) && $selectedProjet === 'Rénover' ? 'checked' : '') . ' required> Rénover
                          </label>
                          <label>
                            <input type="radio" name="projet" value="Réhabiliter" ' . (!empty($selectedProjet) && $selectedProjet === 'Réhabiliter' ? 'checked' : '') . ' required> Réhabiliter
                          </label>
                          <label>
                            <input type="radio" name="projet" value="Agrandir" ' . (!empty($selectedProjet) && $selectedProjet === 'Agrandir' ? 'checked' : '') . ' required> Agrandir
                          </label>
                          <label>
                            <input type="radio" name="projet" value="Convertir" ' . (!empty($selectedProjet) && $selectedProjet === 'Convertir' ? 'checked' : '') . ' required> Convertir
                          </label>
                          <label>
                            <input type="radio" name="projet" value="Aménager" ' . (!empty($selectedProjet) && $selectedProjet === 'Aménager' ? 'checked' : '') . ' required> Aménager
                          </label>
                          <label>
                            <input type="radio" name="projet" value="Décorer" ' . (!empty($selectedProjet) && $selectedProjet === 'Décorer' ? 'checked' : '') . ' required> Décorer
                          </label>
                        </div>
                    </div>
                    <div class="text-end flex justify-end gap-2">
                      <button class="btn btn-blue" onclick="nextStep(1, false)">Précédent</button>
                      <button class="btn btn-blue" onclick="nextStep(3)">Suivant</button>
                    </div>
                  </div>';
            break;

        case 3:
            // Étape 5 seule
            echo '<form id="submit-project-form" enctype="multipart/form-data" method="POST" action="' . site_url('/wp-admin/admin-ajax.php') . '">
                      <input type="hidden" name="action" value="submit_project_form">
                      <input type="hidden" id="hiddenSearch" name="search" value="' . ($formData['search'] ?? '') . '">
                      <input type="hidden" id="hiddenProperty" name="property" value="' . ($formData['property'] ?? '') . '">
                      <input type="hidden" id="hiddenProprietaire" name="proprietaire" value="' . ($formData['proprietaire'] ?? '') . '">
                      <input type="hidden" id="hiddenProjet" name="projet" value="' . ($formData['projet'] ?? '') . '">
                      <div class="steps-group">
                      <div class="step">
                        <div class="mt-3 p-relative">
                          <span class="">Dans quelle ville est situé le bien ?*</span>  
                          <input type="text" id="inputSearchCity" name="city" class="custom-input" placeholder="Entrez le nom de votre ville en France mét. ou dans les DOM-TOM" required>
                          <div class="suggest"></div>
                        </div>
                        <div class="row mt-3">
                          <div class="col-md-4">
                            <span class="">Surface totale* :</span>  
                            <input type="number" id="inputSearchSurface" name="total_surface" class="custom-input" placeholder="" required>
                          </div>
                          <div class="col-md-4">
                            <span class="">Surface des travaux* :</span>  
                            <input type="number" id="inputSearchSurfaceWork" name="work_surface" class="custom-input" placeholder="" required>
                          </div>
                          <div class="col-md-4">
                            <span class="">Budget prévisionnel* :</span>  
                            <input type="number" id="inputSearchBudget" name="budget" class="custom-input" placeholder="" required>
                          </div>
                        </div>
                        <div class="mt-3">
                        <span class="">Besoins spécifiques pour ce projet* : </span>
                        <div class="row mt-1">
                          <div class="col-md-4 flex gap-1 form-group">
                            <input type="checkbox" id="needArchitecte" name="needs[]" value="Architecte">
                            <label for="needArchitecte">Architecte</label>
                          </div>
                          <div class="col-md-4 flex gap-1 form-group">
                            <input type="checkbox" id="needPlan" name="needs[]" value="Plans">
                            <label for="needPlan">Plans</label>
                          </div>
                          <div class="col-md-4 flex gap-1 form-group">
                            <input type="checkbox" id="needDessins_techniques" name="needs[]" value="Dessins techniques">
                            <label for="needDessins_techniques">Dessins techniques</label>
                          </div>
                          <div class="col-md-4 flex gap-1 form-group">
                            <input type="checkbox" id="needSupervision_des_equipes" name="needs[]" value="Supervision des équipes">
                            <label for="needSupervision_des_equipes">Supervision des équipes</label>
                          </div>
                          <div class="col-md-4 flex gap-1 form-group">
                            <input type="checkbox" id="needSelection_des_artisans" name="needs[]" value="Sélection des artisans">
                            <label for="needSelection_des_artisans">Sélection des artisans</label>
                          </div>
                          <div class="col-md-4 flex gap-1 form-group">
                            <input type="checkbox" id="needAutorisation_et_permis" name="needs[]" value="Autorisations et permis">
                            <label for="needAutorisation_et_permis">Autorisations et permis</label>
                          </div>
                          <div class="col-md-4 flex gap-1 form-group">
                            <input type="checkbox" id="needAchats_materiaux" name="needs[]" value="Achats matériaux">
                            <label for="needAchats_materiaux">Achats matériaux</label>
                          </div>
                          <div class="col-md-4 flex gap-1 form-group">
                            <input type="checkbox" id="needA_definir_ensemble" name="needs[]" value="À définir ensemble">
                            <label for="needA_definir_ensemble">À définir ensemble</label>
                          </div>
                        </div></div>
                        <div class="mt-3">
                          <span class="">Donnez un nom à votre projet*</span>  
                          <input type="text" id="inputProjectName" name="project_name" class="custom-input" placeholder="" required>
                        </div>
                        <div class="mt-3">
                          <span class="">Décrivez votre projet en quelques mots*</span>  
                          <textarea type="text" id="inputProjectDescription" name="project_description" class="custom-input" placeholder="Ce court descriptif permettra aux professionnels que vous contacterez d’en savoir plus sur votre projet avant de vous répondre." required></textarea>
                        </div>
                        <div class="mt-3">
                          <span class="">Date de commencement du projet*</span>  
                          <input type="date" id="inputStartDate" name="project_start_date" class="custom-input" required>
                        </div>
                        <div class="mt-3">
                          <span>Ajoutez des fichiers (plans, photos en .jpg ou documents .pdf)*</span>
                          <button type="button" id="addFileBtn" class="btn btn-outline-dark my-1">
                            Choisir un fichier
                          </button>
                          <!-- Champ file masqué -->
                          <input type="file" id="fileInput" multiple accept=".jpg,.jpeg,.pdf" style="display: none;">
                          <div id="filePreviewList" class="mt-2"></div>
                          <small>Formats autorisés : .jpg, .jpeg, .pdf</small>
                        </div>
                      </div>
                    </div>
                    <div class="text-end flex justify-end gap-2 mt-3">
                      <button class="btn btn-blue" onclick="nextStep(2, false)">Précédent</button>
                      <button type="submit" class="btn btn-blue">Valider</button>
                    </div>
                    </form>';
              break;

        default:
            echo 'Étape inconnue.';
    }
} else {
    echo 'Aucune étape reçue.';
}