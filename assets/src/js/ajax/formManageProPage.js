document.addEventListener("DOMContentLoaded", function () {
  console.log("formManageProPage.js chargé avec succès");

  //Afficher une alerte SweetAlert2 pour prévenir que l'utilisateur est en mode édition
  Swal.fire({
    icon: "info",
    title: "Mode Édition Activé",
    text: "Vous êtes en train de modifier votre profil. Cliquez sur les éléments pour les éditer.",
    confirmButtonText: "OK",
    timer: 5000, //Ferme automatiquement après 5 secondes
    timerProgressBar: true,
  });

  Dropzone.autoDiscover = false;

  function handleImageUpload(selector, inputSelector, imageType) {
    const imageElement = document.querySelector(selector);
    const inputElement = document.querySelector(inputSelector);

    if (!imageElement || !inputElement) {
      console.error(`Élément ${imageType} non trouvé.`);
      return;
    }

    imageElement.addEventListener("click", function () {
      inputElement.click();
    });

    inputElement.addEventListener("change", function (event) {
      const file = event.target.files[0];
      if (!file) return;

      const maxSize = 2 * 1024 * 1024; // 2MB
      const allowedTypes = [
        "image/jpeg",
        "image/png",
        "image/gif",
        "image/webp",
      ];

      if (file.size > maxSize) {
        Swal.fire({
          icon: "error",
          title: "Erreur",
          text: "L'image est trop grande. Taille max : 2MB.",
          confirmButtonText: "OK",
        });
        return;
      }

      if (!allowedTypes.includes(file.type)) {
        Swal.fire({
          icon: "error",
          title: "Erreur",
          text: "Format non autorisé. Formats acceptés : jpg, jpeg, png, gif, webp.",
          confirmButtonText: "OK",
        });
        return;
      }

      const reader = new FileReader();
      reader.onload = function (e) {
        if (imageType === "background") {
          imageElement.style.backgroundImage = `url('${e.target.result}')`;
        } else {
          imageElement.src = e.target.result;
        }
      };
      reader.readAsDataURL(file);

      let formData = new FormData();
      formData.append("action", "update_user_image");
      formData.append("user_id", ajax_object.user_id);
      formData.append("image_type", imageType);
      formData.append("image", file);

      fetch(ajax_object.ajax_url, {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          console.log("Réponse AJAX reçue :", data);

          if (data.success && data.data && data.data.image_url) {
            console.log(
              `Image ${imageType} mise à jour :`,
              data.data.image_url
            );

            if (imageType === "background") {
              imageElement.style.backgroundImage = `url('${data.data.image_url}')`;
            } else {
              imageElement.src = data.data.image_url;
            }

            Swal.fire({
              icon: "success",
              title: "Image mise à jour !",
              text: `Votre ${
                imageType === "background" ? "image de fond" : "photo de profil"
              } a bien été enregistrée.`,
              confirmButtonText: "OK",
            });
          } else {
            console.error("Erreur de mise à jour :", data.message);

            Swal.fire({
              icon: "error",
              title: "Erreur",
              text:
                data.message ||
                "Une erreur est survenue lors de la mise à jour.",
              confirmButtonText: "OK",
            });
          }
        })
        .catch((error) => {
          console.error("Erreur AJAX :", error);
          Swal.fire({
            icon: "error",
            title: "Erreur",
            text: "Impossible de contacter le serveur.",
            confirmButtonText: "OK",
          });
        });
    });
  }

  handleImageUpload("#background-dropzone", "#background-input", "background");
  handleImageUpload("#profile-image", "#profile-input", "profile");

  //Editor Part Description Single Page

  // console.log("Quill Editor - Chargement...");

  const editorElement = document.querySelector("#quill-editor");

  if (!editorElement) {
    console.error("Élément #quill-editor introuvable !");
    return;
  }

  //Initialisation de Quill
  const quill = new Quill(editorElement, {
    theme: "snow",
    modules: {
      toolbar: [
        ["bold", "italic", "underline", "strike"],
        [{ list: "ordered" }, { list: "bullet" }],
        [{ align: [] }],
      ],
    },
    formats: [
      "bold",
      "italic",
      "underline",
      "strike",
      "header",
      "list",
      "align",
    ],
  });

  quill.keyboard.addBinding({ key: 13 }, function (range) {
    quill.insertText(range.index, "\n");
    quill.setSelection(range.index + 1);
  });

  //Récupérer la valeur existante depuis WordPress
  const descriptionInput = document.querySelector("#editable-description");
  quill.root.innerHTML = descriptionInput.value;

  //Validation de la description (évite le spam de texte court)
  function validateDescription(content) {
    const textOnly = content.replace(/<[^>]+>/g, "").trim(); // Supprime les balises HTML
    if (textOnly.length < 10) {
      return "La description doit contenir au moins 10 caractères.";
    }
    return null;
  }

  //Gestion du bouton "Enregistrer"
  document
    .querySelector("#save-description")
    .addEventListener("click", function () {
      const content = quill.root.innerHTML;
      const error = validateDescription(content);

      const errorElement = document.querySelector("#description-error");
      if (error) {
        errorElement.textContent = error;
        errorElement.style.display = "block";
        return;
      }

      errorElement.style.display = "none";

      //Envoyer la description en AJAX à WordPress
      fetch(ajax_object.ajax_url, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
          action: "update_user_description",
          user_id: ajax_object.user_id,
          description: content,
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            console.log("Description sauvegardée !");
            Swal.fire({
              icon: "success",
              title: "Succès",
              text: "Votre description a été enregistrée avec succès.",
              confirmButtonText: "OK",
            });
          } else {
            console.error("Erreur AJAX :", data.message);
          }
        })
        .catch((error) => console.error("Erreur AJAX :", error));
    });

  // console.log("Quill Editor - Chargé avec succès !");

  //Editor Part Pro Infos Single Page
  document
    .getElementById("save-pro-profile-info")
    .addEventListener("click", function () {
      let formData = new FormData();
      formData.append("action", "update_user_pro_page_profile_info");
      formData.append("user_id", ajax_object.user_id);
      formData.append("diplome", document.getElementById("diplome").value);
      formData.append(
        "experience",
        document.getElementById("experience").value
      );
      formData.append("budget", document.getElementById("budget").value);
      formData.append(
        "architect_types",
        document.getElementById("architect_types").value
      );
      formData.append(
        "motivation",
        document.getElementById("motivation").value
      );

      fetch(ajax_object.ajax_url, {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            Swal.fire({
              icon: "success",
              title: "Succès",
              text: "Informations mises à jour avec succès !",
              confirmButtonText: "OK",
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Erreur",
              text: "Une erreur est survenue lors de la mise à jour.",
              confirmButtonText: "OK",
            });
          }
        })
        .catch((error) => {
          console.error("Erreur AJAX :", error);
          Swal.fire({
            icon: "error",
            title: "Erreur",
            text: "Impossible de contacter le serveur.",
            confirmButtonText: "OK",
          });
        });
    });

  /// Gestion de Ajax is_page_public
  // console.log("Gestion AJAX de is_page_public chargée.");

  const visibilityToggle = document.getElementById("switch");

  if (!visibilityToggle) {
    console.error("L'élément du switch de visibilité n'a pas été trouvé.");
    return;
  }

  visibilityToggle.addEventListener("change", function () {
    let isVisible = this.checked ? 1 : 0;
    let userId = ajax_object.user_id; // Utilisation d'ajax_object pour récupérer l'ID utilisateur

    let formData = new FormData();
    formData.append("action", "update_page_visibility");
    formData.append("user_id", userId);
    formData.append("is_visible", isVisible);

    fetch(ajax_object.ajax_url, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          Swal.fire({
            icon: "success",
            title: "Mise à jour réussie",
            text: isVisible
              ? "Votre profil est maintenant visible publiquement."
              : "Votre profil est maintenant privé.",
            confirmButtonText: "OK",
          });

          console.log("Visibilité mise à jour avec succès :", isVisible);
        } else {
          console.error("Erreur lors de la mise à jour :", data.message);
          Swal.fire({
            icon: "error",
            title: "Erreur",
            text:
              data.message || "Une erreur est survenue lors de la mise à jour.",
            confirmButtonText: "OK",
          });
        }
      })
      .catch((error) => {
        console.error("Erreur AJAX :", error);
        Swal.fire({
          icon: "error",
          title: "Erreur",
          text: "Impossible de contacter le serveur.",
          confirmButtonText: "OK",
        });
      });
  });
});
