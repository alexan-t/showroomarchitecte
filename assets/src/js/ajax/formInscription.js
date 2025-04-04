document.addEventListener("DOMContentLoaded", () => {
  const registrationForm = document.querySelector("#registrationForm");
  if (!registrationForm) return; // Si le formulaire n'est pas présent sur la page, on arrête le script

  const continueBtn = document.getElementById("continueBtn");
  const step1 = document.getElementById("step1");
  const step2 = document.getElementById("step2");
  const commonFields = document.getElementById("common-fields");
  const proFields = document.getElementById("professional-fields");
  const sirenField = document.getElementById("siren");

  // ⚙️ Étape 1 : Affichage des champs selon le type d’utilisateur
  if (continueBtn) {
    continueBtn.addEventListener("click", (e) => {
      e.preventDefault();

      // On récupère les champs à valider
      const lastName = getInputValue("last_name");
      const firstName = getInputValue("first_name");
      const email = getInputValue("username");
      const password = getInputValue("password");
      const selectedType = registrationForm.querySelector(
        'input[name="user_type"]:checked'
      );

      // Vérifie que tous les champs sont remplis
      if (!lastName || !firstName || !email || !password || !selectedType) {
        showToast("Veuillez remplir tous les champs obligatoires", "warning");
        return;
      }

      // Affiche les bons champs en fonction du type d’utilisateur
      commonFields.classList.remove("none");

      if (selectedType.value === "professionnel") {
        proFields.classList.remove("none");
      } else {
        proFields.classList.add("none");
      }

      // Masquer l’étape 1 et afficher l’étape 2
      step1.classList.add("none");
      step2.classList.remove("none");
    });
  }

  // ⚙️ Étape 2 : Envoi du formulaire d’inscription en AJAX
  registrationForm.addEventListener("submit", (e) => {
    e.preventDefault();

    const formData = new FormData(registrationForm);
    formData.append("action", "showroom_handle_registration"); // Obligatoire pour WordPress

    fetch("/wp-admin/admin-ajax.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        showToast(data.message, data.success ? "success" : "error");

        if (data.success && data.redirect) {
          setTimeout(() => {
            window.location.href = data.redirect;
          }, 1500);
        }
      })
      .catch(() => {
        showToast("Une erreur est survenue", "error");
      });
  });

  // ⚙️ API INSEE : Auto-remplir les infos de l’entreprise via le numéro SIREN
  if (sirenField) {
    sirenField.addEventListener("input", () => {
      const siren = sirenField.value.trim();

      // Vérifie que le SIREN est bien un nombre de 9 chiffres
      if (siren.length === 9 && /^\d{9}$/.test(siren)) {
        const formData = new FormData();
        formData.append("action", "get_sirene_info");
        formData.append("siren", siren);

        fetch("/wp-admin/admin-ajax.php", {
          method: "POST",
          body: formData,
        })
          .then((res) => res.json())
          .then((data) => {
            if (data.success) {
              const uniteLegale = data.data.uniteLegale;
              const periodes = uniteLegale.periodesUniteLegale || [];
              const periodeActive = periodes.find((p) => p.dateFin === null);

              if (periodeActive) {
                const activite =
                  periodeActive.activitePrincipaleUniteLegale || "";
                const nomEntreprise =
                  periodeActive.denominationUsuelle1UniteLegale ||
                  periodeActive.denominationUniteLegale ||
                  periodeActive.nomUniteLegale ||
                  "";

                // Remplir automatiquement les champs
                const activiteInput = document.getElementById("ape");
                const nomEntrepriseInput =
                  document.getElementById("entreprise");

                if (activiteInput) activiteInput.value = activite;
                if (nomEntrepriseInput)
                  nomEntrepriseInput.value = nomEntreprise;
              }
            } else {
              console.warn(data.data.message);
            }
          })
          .catch((err) => {
            console.error("Erreur API SIREN :", err);
          });
      }
    });
  }

  // ============================
  // 🔧 Fonctions utilitaires
  // ============================

  // 🔹 Récupère la valeur d’un champ input par son nom
  function getInputValue(name) {
    const input = registrationForm.querySelector(`[name="${name}"]`);
    return input ? input.value.trim() : "";
  }

  // 🔹 Affiche une alerte toast SweetAlert
  function showToast(message, type = "info") {
    Swal.fire({
      toast: true,
      icon: type,
      title: message,
      position: "top-end",
      showConfirmButton: false,
      timer: 4000,
      timerProgressBar: true,
    });
  }
});
