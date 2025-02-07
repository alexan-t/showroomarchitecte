jQuery(document).ready(function ($) {
  // Vérifiez si Swal (SweetAlert2) est chargé
  if (typeof Swal === "undefined") {
    console.error("SweetAlert2 is not loaded.");
    return;
  }

  var toastMixin = Swal.mixin({
    toast: true,
    icon: "success",
    title: "Titre Général",
    animation: false,
    position: "top-right",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener("mouseenter", Swal.stopTimer);
      toast.addEventListener("mouseleave", Swal.resumeTimer);
    },
  });

  $("#update-profile-form").on("submit", function (e) {
    e.preventDefault(); // Empêche le rechargement de la page

    console.log("Form submission initiated.");

    // Désactiver le bouton de soumission pour éviter les soumissions multiples
    var $submitButton = $(this).find("button[type='submit']");
    $submitButton.prop("disabled", true);
    console.log("Submit button disabled.");

    // Créer un objet FormData pour envoyer les données du formulaire, y compris les fichiers
    var formData = new FormData(this);

    // Ajouter le nonce à FormData
    formData.append("nonce", formUpdateInfos.nonce);
    formData.append("action", "update_mes_informations"); // Assurez-vous que l'action correspond à celle définie en PHP

    console.log("FormData prepared:", formData);

    // Afficher un indicateur de chargement via SweetAlert2
    Swal.fire({
      title: "Mise à jour en cours...",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    // Envoyer la requête AJAX
    $.ajax({
      url: formUpdateInfos.ajaxurl,
      type: "POST",
      data: formData,
      contentType: false, // Nécessaire pour envoyer les fichiers
      processData: false, // Nécessaire pour envoyer les fichiers
      success: function (response) {
        console.log("AJAX success response:", response);
        Swal.close(); // Fermer le loading

        if (response.success) {
          toastMixin.fire({
            animation: true,
            title: response.data.message,
            icon: "success",
          });

          // Mettre à jour l'avatar si une nouvelle image a été téléchargée
          if (response.data.profile_image_url) {
            $("#imagePreview").css(
              "background-image",
              "url(" + response.data.profile_image_url + ")"
            );
            console.log(
              "Profile image updated:",
              response.data.profile_image_url
            );
          }

          // Vérifiez si le navigateur supporte setTimeout
          if (typeof setTimeout === "function") {
            console.log("setTimeout is supported.");
            setTimeout(function () {
              console.log("Reloading the page now.");
              window.location.reload();
            }, 1500);
          } else {
            console.error("setTimeout is not supported in this environment.");
          }
        } else {
          console.error("AJAX response indicates failure:", response);
          Swal.fire({
            icon: "error",
            title: "Erreur",
            text: response.data.message,
            timer: 3000,
            showConfirmButton: false,
          });
        }
      },
      error: function (xhr, status, error) {
        console.error("Erreur AJAX :", status, error);
        Swal.close(); // Fermer le loading
        Swal.fire({
          icon: "error",
          title: "Erreur",
          text: "Une erreur est survenue. Veuillez réessayer.",
          timer: 3000,
          showConfirmButton: false,
        });
      },
      complete: function () {
        // Réactiver le bouton de soumission
        $submitButton.prop("disabled", false);
        console.log("Submit button re-enabled.");
      },
    });
  });

  // Prévisualisation de l'image téléchargée (optionnel)
  $("#imageUpload").on("change", function () {
    var file = this.files[0];
    if (file) {
      var reader = new FileReader();

      reader.onload = function (event) {
        $("#imagePreview").css(
          "background-image",
          "url(" + event.target.result + ")"
        );
        console.log("Image preview updated.");
      };

      reader.readAsDataURL(file);
      console.log("FileReader initiated.");
    } else {
      // Réinitialiser l'image de prévisualisation si aucun fichier n'est sélectionné
      $("#imagePreview").css(
        "background-image",
        'url("' + formUpdateInfos.default_image + '")'
      );
      console.log("Image preview reset to default.");
    }
  });

  //FORMULAIRE PRO
  const form = document.getElementById("pro-info-form");

  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      const formData = new FormData(form);

      // ✅ Récupérer les types d'architectes cochés
      const architectTypes = [];
      form
        .querySelectorAll('input[name="architecte_type[]"]:checked')
        .forEach((checkbox) => {
          architectTypes.push(checkbox.value);
        });

      Swal.fire({
        title: "Êtes-vous sûr ?",
        text: "Vous allez enregistrer vos informations professionnelles.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Oui, enregistrer !",
      }).then((result) => {
        if (result.isConfirmed) {
          // ✅ Préparer les données à envoyer
          const dataToSend = new URLSearchParams();
          dataToSend.append("action", "update_pro_infos");
          dataToSend.append("security", formData.get("security"));
          dataToSend.append(
            "diplome_principal",
            formData.get("diplome_principal")
          );
          dataToSend.append(
            "annees_experience",
            formData.get("annees_experience")
          );
          dataToSend.append(
            "budget_moyen_chantiers",
            formData.get("budget_moyen_chantiers")
          );
          dataToSend.append(
            "motivation_metier",
            formData.get("motivation_metier")
          );

          // ✅ Ajouter les types d'architectes cochés
          architectTypes.forEach((type) => {
            dataToSend.append("architecte_type[]", type);
          });

          // ✅ Envoi de la requête AJAX
          fetch(ajax_object.ajax_url, {
            method: "POST",
            body: dataToSend,
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.success) {
                Swal.fire({
                  icon: "success",
                  title: "Succès",
                  text: data.data.message,
                });
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Erreur",
                  text: data.data.message || "Une erreur est survenue.",
                });
              }
            })
            .catch(() => {
              Swal.fire({
                icon: "error",
                title: "Erreur",
                text: "Impossible de traiter la demande.",
              });
            });
        }
      });
    });
  }
});
