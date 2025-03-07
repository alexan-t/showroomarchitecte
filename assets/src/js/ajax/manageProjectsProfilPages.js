document.addEventListener("DOMContentLoaded", function () {
  // Suppression d'un Projet Profil Page
  document.querySelectorAll(".delete-project").forEach((button) => {
    button.addEventListener("click", function () {
      let projectId = this.getAttribute("data-project-id");

      Swal.fire({
        title: "Êtes-vous sûr ?",
        text: "Ce projet sera définitivement supprimé.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Oui, supprimer",
        cancelButtonText: "Annuler",
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(ajax_object.ajax_url, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
              action: "delete_project_on_profil_page",
              user_id: ajax_object.user_id,
              project_id: projectId,
            }),
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.success) {
                Swal.fire(
                  "Supprimé !",
                  "Le projet a été supprimé.",
                  "success"
                ).then(() => {
                  location.reload();
                });
              } else {
                Swal.fire("Erreur", "Une erreur est survenue.", "error");
              }
            })
            .catch((error) => {
              console.error("❌ Erreur AJAX :", error);
              Swal.fire(
                "Erreur",
                "Problème de connexion avec le serveur.",
                "error"
              );
            });
        }
      });
    });
  });

  // Ajout d'un Projet Profil Page
  document
    .getElementById("add-project-btn")
    .addEventListener("click", function () {
      let additionalImagesArray = []; // Stocke les fichiers ajoutés

      Swal.fire({
        title: "Ajouter un projet",
        width: "80%",
        heightAuto: false,
        customClass: { popup: "custom-modal-height" },
        html: `
        <div class="row">
          <div class="col-md-6 w-100">

            <div class="pb-3 flex flex-col">
              <label><strong>Titre :</strong></label>
              <input type="text" id="project-title" class="swal2-input" placeholder="Titre du projet">
            </div>

            <div class="pb-3 flex flex-col">
              <label><strong>Budget :</strong></label>
              <input type="number" id="project-budget" class="swal2-input" placeholder="Budget">
            </div>

            <div class="pb-3 flex flex-col">
              <label><strong>Surface :</strong></label>
              <input type="text" id="project-surface" class="swal2-input" placeholder="Surface">
            </div>

            <div class="pb-3 flex flex-col">
              <label><strong>Durée (jours) :</strong></label>
              <input type="text" id="project-duration" class="swal2-input" placeholder="Durée (jours)">
            </div>

            <div class="pb-3 flex flex-col">
              <label><strong>Description :</strong></label>
              <textarea id="project-description" class="swal2-textarea" placeholder="Description"></textarea>
            </div>

          </div>

          <div class="col-md-6 flex flex-col justify-between items-center">
              <div>
                <label><strong>Image principale :</strong></label>
                <input type="file" id="project-image" accept="image/*">
                <img id="preview-project-image" style="max-width: 100%; height: auto; display: none; margin-top: 10px;">
              </div>

              <div class="mt-1">
                <label><strong>Images additionnelles :</strong></label>
                <input type="file" id="additional-images" accept="image/*">
                <div id="additional-images-preview" style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px;"></div>
              </div>
          </div>
        </div>
        `,
        showCancelButton: true,
        confirmButtonText: "Ajouter",
        cancelButtonText: "Annuler",
        didOpen: () => {
          // 🖼 Preview de l'image principale
          document
            .getElementById("project-image")
            .addEventListener("change", function (event) {
              let file = event.target.files[0];
              if (file) {
                let reader = new FileReader();
                reader.onload = function (e) {
                  let img = document.getElementById("preview-project-image");
                  img.src = e.target.result;
                  img.style.display = "block";
                };
                reader.readAsDataURL(file);
              }
            });

          // 📸 Gestion des images additionnelles
          document
            .getElementById("additional-images")
            .addEventListener("change", function (event) {
              let previewContainer = document.getElementById(
                "additional-images-preview"
              );

              Array.from(event.target.files).forEach((file, index) => {
                let reader = new FileReader();
                reader.onload = function (e) {
                  let div = document.createElement("div");
                  div.style.position = "relative";
                  div.style.display = "inline-block";

                  let img = document.createElement("img");
                  img.src = e.target.result;
                  img.style.maxWidth = "250px";
                  img.style.height = "auto";
                  img.style.marginRight = "10px";

                  let removeBtn = document.createElement("button");
                  removeBtn.innerText = "✖";
                  removeBtn.style.position = "absolute";
                  removeBtn.style.top = "0";
                  removeBtn.style.right = "0";
                  removeBtn.style.background = "red";
                  removeBtn.style.color = "white";
                  removeBtn.style.border = "none";
                  removeBtn.style.cursor = "pointer";

                  removeBtn.addEventListener("click", function () {
                    let indexToRemove = additionalImagesArray.indexOf(file);
                    if (indexToRemove > -1) {
                      additionalImagesArray.splice(indexToRemove, 1);
                    }
                    previewContainer.removeChild(div);
                  });

                  additionalImagesArray.push(file);
                  div.appendChild(img);
                  div.appendChild(removeBtn);
                  previewContainer.appendChild(div);
                };
                reader.readAsDataURL(file);
              });

              // Réinitialiser l'input pour permettre de choisir plusieurs fois
              event.target.value = "";
            });
        },
        preConfirm: () => {
          let formData = new FormData();
          formData.append("action", "add_project_on_profil_page");
          formData.append("user_id", ajax_object.user_id);
          formData.append(
            "title",
            document.getElementById("project-title").value
          );
          formData.append(
            "budget",
            document.getElementById("project-budget").value
          );
          formData.append(
            "surface",
            document.getElementById("project-surface").value
          );
          formData.append(
            "duration",
            document.getElementById("project-duration").value
          );
          formData.append(
            "description",
            document.getElementById("project-description").value
          );

          // Ajout de l'image principale
          let mainImage = document.getElementById("project-image").files[0];
          if (mainImage) {
            formData.append("image", mainImage);
          }

          // Ajout des images additionnelles
          additionalImagesArray.forEach((file, index) => {
            formData.append(`additional_images[${index}]`, file);
          });

          return fetch(ajax_object.ajax_url, {
            method: "POST",
            body: formData,
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.success) {
                Swal.fire(
                  "Ajouté !",
                  "Le projet a été ajouté avec succès.",
                  "success"
                ).then(() => {
                  location.reload();
                });
              } else {
                Swal.fire("Erreur", "Impossible d'ajouter le projet.", "error");
              }
            });
        },
      });
    });

  //Edit d'un Projet Profil Page
  // console.log("Gestion de l'édition du portfolio chargée.");

  document.querySelectorAll(".edit-project-image").forEach(function (img) {
    img.addEventListener("click", function () {
      let projectId = this.dataset.projectId;
      let title = this.dataset.projectTitle || "";
      let description = this.dataset.projectDescription || "";
      let budget = this.dataset.projectBudget || "";
      let surface = this.dataset.projectSurface || "";
      let duration = this.dataset.projectDuration || "";
      let imageUrl = this.src;
      let additionalImages = JSON.parse(
        this.dataset.projectAdditionalImages || "[]"
      );

      let additionalImagesArray = []; // Stocke les nouveaux fichiers ajoutés

      Swal.fire({
        title: "Modifier le projet",
        width: "80%",
        heightAuto: false,
        customClass: { popup: "custom-modal-height" },
        html: `
          <div class="row">
            <div class="col-md-6 w-100">
              <div class="pb-3 flex flex-col">
                <label><strong>Titre :</strong></label>
                <input type="text" id="edit-project-title" class="swal2-input" value="${title}">
              </div>
  
              <div class="pb-3 flex flex-col">
                <label><strong>Budget :</strong></label>
                <input type="number" id="edit-project-budget" class="swal2-input" value="${budget}">
              </div>
  
              <div class="pb-3 flex flex-col">
                <label><strong>Surface :</strong></label>
                <input type="text" id="edit-project-surface" class="swal2-input" value="${surface}">
              </div>
  
              <div class="pb-3 flex flex-col">
                <label><strong>Durée (jours) :</strong></label>
                <input type="text" id="edit-project-duration" class="swal2-input" value="${duration}">
              </div>
  
              <div class="pb-3 flex flex-col">
                <label><strong>Description :</strong></label>
                <textarea id="edit-project-description" class="swal2-textarea">${description}</textarea>
              </div>
            </div>
  
            <div class="col-md-6 flex flex-col justify-between items-center">
                <div>
                  <label><strong>Image principale :</strong></label>
                  <input type="file" id="edit-project-image" accept="image/*">
                  <img id="preview-edit-project-image" src="${imageUrl}" style="max-width: 100%; height: auto; margin-top: 10px;">
                </div>
  
                <div class="mt-1">
                  <label><strong>Images additionnelles :</strong></label>
                  <input type="file" id="edit-additional-images" accept="image/*" multiple>
                  <div id="edit-additional-images-preview" style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px;">
                    ${additionalImages
                      .map(
                        (img, index) => `
                      <div style="position: relative; display: inline-block;">
                        <img src="${img}" style="max-width: 100px; height: auto;">
                        <button class="delete-old-image" data-index="${index}" style="position: absolute; top: 0; right: 0; background: red; color: white; border: none; cursor: pointer;">✖</button>
                      </div>
                    `
                      )
                      .join("")}
                  </div>
                </div>
            </div>
          </div>
          `,
        showCancelButton: true,
        confirmButtonText: "Modifier",
        cancelButtonText: "Annuler",
        didOpen: () => {
          // console.log(additionalImages);

          // 🖼 Preview de l'image principale
          document
            .getElementById("edit-project-image")
            .addEventListener("change", function (event) {
              let file = event.target.files[0];
              if (file) {
                let reader = new FileReader();
                reader.onload = function (e) {
                  let img = document.getElementById(
                    "preview-edit-project-image"
                  );
                  img.src = e.target.result;
                };
                reader.readAsDataURL(file);
              }
            });

          // 📸 Gestion des images additionnelles
          document
            .getElementById("edit-additional-images")
            .addEventListener("change", function (event) {
              let previewContainer = document.getElementById(
                "edit-additional-images-preview"
              );

              Array.from(event.target.files).forEach((file, index) => {
                let reader = new FileReader();
                reader.onload = function (e) {
                  let div = document.createElement("div");
                  div.style.position = "relative";
                  div.style.display = "inline-block";

                  let img = document.createElement("img");
                  img.src = e.target.result;
                  img.style.maxWidth = "100px";
                  img.style.height = "auto";
                  img.style.marginRight = "10px";

                  let removeBtn = document.createElement("button");
                  removeBtn.innerText = "✖";
                  removeBtn.style.position = "absolute";
                  removeBtn.style.top = "0";
                  removeBtn.style.right = "0";
                  removeBtn.style.background = "red";
                  removeBtn.style.color = "white";
                  removeBtn.style.border = "none";
                  removeBtn.style.cursor = "pointer";

                  removeBtn.addEventListener("click", function () {
                    let indexToRemove = additionalImagesArray.indexOf(file);
                    if (indexToRemove > -1) {
                      additionalImagesArray.splice(indexToRemove, 1);
                    }
                    previewContainer.removeChild(div);
                  });

                  additionalImagesArray.push(file);
                  div.appendChild(img);
                  div.appendChild(removeBtn);
                  previewContainer.appendChild(div);
                };
                reader.readAsDataURL(file);
              });

              event.target.value = "";
            });

          // Suppression des anciennes images additionnelles
          document.querySelectorAll(".delete-old-image").forEach((btn) => {
            btn.addEventListener("click", function () {
              let indexToRemove = parseInt(this.dataset.index);
              additionalImages.splice(indexToRemove, 1);
              this.parentElement.remove();
            });
          });
        },
        preConfirm: () => {
          let formData = new FormData();
          formData.append("action", "edit_project_on_profil_page");
          formData.append("user_id", ajax_object.user_id);
          formData.append("project_id", projectId);
          formData.append(
            "title",
            document.getElementById("edit-project-title").value
          );
          formData.append(
            "budget",
            document.getElementById("edit-project-budget").value
          );
          formData.append(
            "surface",
            document.getElementById("edit-project-surface").value
          );
          formData.append(
            "duration",
            document.getElementById("edit-project-duration").value
          );
          formData.append(
            "description",
            document.getElementById("edit-project-description").value
          );
          formData.append(
            "removed_old_images",
            JSON.stringify(additionalImages)
          );

          let mainImage =
            document.getElementById("edit-project-image").files[0];
          if (mainImage) {
            formData.append("image", mainImage);
          }

          additionalImagesArray.forEach((file, index) => {
            formData.append(`additional_images[${index}]`, file);
          });

          console.log(
            "Données envoyées à WordPress :",
            Object.fromEntries(formData)
          );

          return fetch(ajax_object.ajax_url, {
            method: "POST",
            body: formData,
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.success) {
                Swal.fire(
                  "Modifié !",
                  "Le projet a été mis à jour avec succès.",
                  "success"
                ).then(() => {
                  location.reload();
                });
              } else {
                Swal.fire(
                  "Erreur",
                  "Impossible de modifier le projet.",
                  "error"
                );
              }
            });
        },
      });
    });
  });
});
