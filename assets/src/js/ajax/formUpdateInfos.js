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
});
