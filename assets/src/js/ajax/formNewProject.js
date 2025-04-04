document.addEventListener("DOMContentLoaded", () => {
  if (typeof Swal === "undefined") {
    console.error("SweetAlert2 is not loaded.");
    return;
  }

  ///Gestion de fichiers pour le projet
  let selectedFiles = [];

  function renderFileList() {
    const preview = document.getElementById("filePreviewList");
    preview.innerHTML = "";

    selectedFiles.forEach((file, index) => {
      const item = document.createElement("div");
      item.innerHTML = `
      <span>${file.name}</span>
      <button type="button" class="mx-1 btn btn-sm btn-dark" data-index="${index}">Supprimer</button>
    `;
      preview.appendChild(item);
    });

    document.querySelectorAll("#filePreviewList button").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        const index = parseInt(e.target.dataset.index);
        selectedFiles.splice(index, 1);
        renderFileList();
      });
    });
  }

  //// API Autocomplétion ville
  function enableCityAutocomplete() {
    const input = document.getElementById("inputSearchCity");
    const suggestBox = document.querySelector(".suggest");

    if (!input || !suggestBox) return;

    input.addEventListener("input", async () => {
      const query = input.value.trim();
      suggestBox.innerHTML = "";

      if (query.length < 2) return;

      try {
        const res = await fetch(
          `https://geo.api.gouv.fr/communes?nom=${query}&limit=5`
        );
        const data = await res.json();
        console.log(data);
        data.forEach((ville) => {
          const option = document.createElement("div");
          option.textContent = `${ville.nom} (${ville.code})`;
          option.classList.add("suggestion-item");
          option.style.cursor = "pointer";
          option.addEventListener("click", () => {
            input.value = ville.nom;
            suggestBox.innerHTML = "";
          });
          suggestBox.appendChild(option);
        });
      } catch (error) {
        console.error("Erreur avec l’autocomplétion :", error);
      }
    });

    document.addEventListener("click", (e) => {
      if (!suggestBox.contains(e.target) && e.target !== input) {
        suggestBox.innerHTML = "";
      }
    });
  }

  // Définir le mixin pour les notifications d'erreur
  const toastMixinError = Swal.mixin({
    icon: "error",
    position: "center",
    showConfirmButton: true,
    didOpen: (toast) => {
      toast.addEventListener("mouseenter", Swal.stopTimer);
      toast.addEventListener("mouseleave", Swal.resumeTimer);
    },
  });

  const formContainer = document.getElementById("form-container");
  const progressBar = document.getElementById("progress-bar");
  const totalSteps = 3; // Total des étapes
  let currentStep = 1; // Étape initiale

  /**
   * Mettre à jour la barre de progression.
   * @param {number} step - Étape actuelle.
   */
  function updateProgressBar(step) {
    if (progressBar) {
      const progressPercentage = (step / totalSteps) * 100; // Calcul du pourcentage
      progressBar.style.width = `${progressPercentage}%`;
      progressBar.setAttribute("aria-valuenow", progressPercentage); // Accessibilité
      // console.log(`Progress updated: ${progressPercentage}%`);
    }
  }

  /**
   * Ajouter une classe "selected" au label lorsque le radio est sélectionné.
   */
  if (formContainer) {
    formContainer.addEventListener("click", (event) => {
      if (event.target.matches('input[type="radio"]')) {
        const radio = event.target;

        // Supprimez la classe 'selected' uniquement dans le groupe actuel
        const groupName = radio.name; // Le groupe basé sur le nom
        document
          .querySelectorAll(`input[name="${groupName}"]`)
          .forEach((input) => {
            input.parentElement.classList.remove("selected");
          });

        // Ajoutez la classe 'selected' au parent du radio sélectionné
        if (radio.checked) {
          radio.parentElement.classList.add("selected");
        }
        console.log(
          `Radio clicked! Group: ${groupName}, Value: ${radio.value}`
        );
      }
    });
  }

  /**
   * Charger une étape via une requête AJAX.
   * @param {number} step - Étape à charger.
   */
  if (formContainer) {
    function loadStep(step) {
      $.ajax({
        url: ajaxObject.ajaxUrl,
        type: "POST",
        data: {
          action: "load_form_step",
          step: step,
        },
        beforeSend: function () {
          Swal.fire({
            title: "Chargement...",
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            },
          });
        },
        success: function (response) {
          $("#form-container").html(response);
          updateProgressBar(step); // Mettre à jour la barre de progression
          Swal.close(); // Fermer le loader
        },
        error: function () {
          Swal.fire({
            icon: "error",
            title: "Erreur",
            text: "Impossible de charger l'étape. Veuillez réessayer.",
          });
        },
      });
    }
  }

  /**
   * Validation des groupes radio.
   * @returns {boolean} - Indique si tous les groupes sont valides.
   */
  function validateGroups() {
    console.log("Validation des groupes radio...");
    const groups = document.querySelectorAll(".list-options");
    let allSelected = true;

    groups.forEach((group) => {
      const radios = group.querySelectorAll('input[type="radio"]');
      const selectedRadio = Array.from(radios).find((radio) => radio.checked);

      if (!selectedRadio) {
        const groupName =
          group.previousElementSibling?.innerText || "un groupe";
        toastMixinError.fire({
          title: `Veuillez sélectionner une option pour ${groupName}.`,
        });
        allSelected = false;
      }
    });

    return allSelected;
  }

  /**
   * Naviguer entre les étapes.
   * @param {number} step - Étape suivante.
   * @param {boolean} [validate=true] - Activer ou non la validation.
   */
  if (formContainer) {
    function nextStep(step, validate = true) {
      if (!validate || validateGroups()) {
        const selectedData = {};
        const inputs = document.querySelectorAll(
          '#form-container input[type="radio"]:checked'
        );

        inputs.forEach((input) => {
          selectedData[input.name] = input.value;
        });

        // Log des radios cochés
        console.log("Radios sélectionnés :", selectedData);

        $.ajax({
          url: ajaxObject.ajaxUrl,
          type: "POST",
          data: {
            action: "load_form_step",
            step,
            selected: selectedData,
          },
          success: function (response) {
            $("#form-container").html(response);
            updateProgressBar(step);
            if (step === 3) {
              enableCityAutocomplete();

              document
                .getElementById("addFileBtn")
                .addEventListener("click", () => {
                  document.getElementById("fileInput").click();
                });

              document
                .getElementById("fileInput")
                .addEventListener("change", (e) => {
                  const newFiles = Array.from(e.target.files).filter(
                    (file) =>
                      file &&
                      file.name &&
                      file.size > 0 &&
                      /\.(jpe?g|pdf)$/i.test(file.name)
                  );

                  selectedFiles = [...selectedFiles, ...newFiles];
                  renderFileList();

                  e.target.type = "";
                  e.target.type = "file";
                });
              renderFileList();
            }
          },
          error: function () {
            Swal.fire({
              icon: "error",
              title: "Erreur",
              text: "Impossible de charger l'étape. Veuillez réessayer.",
            });
          },
        });
      }
    }
  }

  /**
   * Gestion de la soumission du formulaire final.
   */
  document.addEventListener("submit", (event) => {
    if (event.target.id === "submit-project-form") {
      event.preventDefault();

      const formData = new FormData(event.target);
      selectedFiles.forEach((file) => {
        formData.append("project_files[]", file);
      });

      $.ajax({
        url: ajaxObject.ajaxUrl,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
          Swal.fire({
            title: "Envoi en cours...",
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            },
          });
        },
        success: function (response) {
          Swal.close();
          if (response.success) {
            Swal.fire({
              icon: "success",
              title: "Succès",
              text: response.data,
            }).then(() => {
              window.location.href = "/resultats-recherche-architecte"; // Redirection après succès
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Erreur",
              text: response.data || "Une erreur inattendue s'est produite.",
            });
          }
        },
        error: function () {
          Swal.fire({
            icon: "error",
            title: "Erreur",
            text: "Une erreur est survenue lors de la soumission.",
          });
        },
      });
    }
  });

  // Charger la première étape au démarrage
  if (formContainer) {
    loadStep(currentStep);
  }
  // Exposer la fonction `nextStep` globalement
  window.nextStep = nextStep;
});
