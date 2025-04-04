document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".profil-project-image").forEach((image) => {
    image.addEventListener("click", function () {
      let projectId = this.getAttribute("data-project-id");
      let userpage_id = this.getAttribute("data-userpage-id");

      fetch(ajax_object.ajax_url, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
          action: "get_project_details",
          user_id: userpage_id,
          project_id: projectId,
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (!data.success || !data.data || !data.data.project) {
            Swal.fire("Erreur", "Projet introuvable.", "error");
            return;
          }

          const project = data.data.project;

          // Construction des images avec GLightbox
          let lightboxHTML = `<div class="container-fluid">`;

          // Image principale
          lightboxHTML += `
            <div class="row mb-3">
              <div class="col-md-12">
                <a href="${project.image}" class="glightbox" data-gallery="project-${projectId}">
                  <img 
                    src="${project.image}" 
                    alt="Image principale" 
                    style="width: 100%; max-height: 300px; object-fit: cover; border-radius: 12px;"
                  />
                </a>
              </div>
            </div>
          `;

          // Miniatures (additional_images)
          if (project.additional_images.length > 0) {
            lightboxHTML += `<div class="row justify-content-start g-2">`;

            project.additional_images.forEach((img, index) => {
              lightboxHTML += `
                <div class="col-md-3 col-sm-6">
                  <a href="${img}" class="glightbox" data-gallery="project-${projectId}">
                    <img 
                      src="${img}" 
                      alt="Image ${index + 1}" 
                      style="width: 100%; height: 100px; object-fit: cover; border-radius: 6px;"
                    />
                  </a>
                </div>
              `;
            });

            lightboxHTML += `</div>`;
          }

          lightboxHTML += `</div>`;

          // SweetAlert2 avec les images + infos
          Swal.fire({
            title: project.title || "Détails du projet",
            width: window.innerWidth > 768 ? "40vw" : "90vw",
            customClass: {
              popup: "custom-modal-height",
            },
            html: `
              <div class="project-modal-container">
                <div style="display:flex; flex-direction:column; gap:1rem;">
                  
                  <!-- Images principales + miniatures -->
                  <div style="width:100%; display:flex; flex-direction:column; align-items:center;">
                    ${lightboxHTML}
                  </div>

                  <!-- Description -->
                  <div class="pt-4" style="text-align:left;">
                    <h4>Description</h4>
                    <p>${project.description || "Non renseignée"}</p>
                  </div>

                  <!-- Infos projet -->
                  <div class="project-details" style="display: flex; flex-wrap: wrap; gap: 20px;">
                    <div style="flex:1; min-width: 200px;">
                      <ion-icon name="construct-outline"></ion-icon>
                      <strong> Type de projet</strong>
                      <p>${project.type || "Rénovation résidentielle"}</p>
                    </div>
                    <div style="flex:1; min-width: 200px;">
                      <ion-icon name="resize-outline"></ion-icon>
                      <strong> Surface</strong>
                      <p>${project.surface || "Non renseignée"} m²</p>
                    </div>
                    <div style="flex:1; min-width: 200px;">
                      <ion-icon name="logo-euro"></ion-icon>
                      <strong> Budget</strong>
                      <p>${project.budget || "Non renseigné"} €</p>
                    </div>
                    <div style="flex:1; min-width: 200px;">
                      <ion-icon name="time-outline"></ion-icon>
                      <strong> Durée</strong>
                      <p>${project.duration || "Non renseignée"}</p>
                    </div>
                  </div>
                </div>
              </div>
            `,
            confirmButtonText: "Fermer",
            didOpen: () => {
              GLightbox({
                selector: `.glightbox[data-gallery="project-${projectId}"]`,
              });
            },
          });
        })
        .catch((error) => {
          console.error("❌ Erreur AJAX :", error);
          Swal.fire(
            "Erreur",
            "Problème de connexion avec le serveur.",
            "error"
          );
        });
    });
  });
});
