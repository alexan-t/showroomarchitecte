//////////////// MODAL PART
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

          let project = data.data.project;

          // Construire les images supplémentaires pour le carousel
          let imagesHTML = `
                  <div class="splide" id="image-carousel">
                      <div class="splide__track">
                          <ul class="splide__list">
                              <li class="splide__slide">
                                  <img class="thumbnail glightbox" src="${project.image}" alt="Image principale">
                              </li>`;

          project.additional_images.forEach((image) => {
            imagesHTML += `
                      <li class="splide__slide">
                          <img class="thumbnail glightbox" src="${image}" alt="Image du Projet">
                      </li>`;
          });

          imagesHTML += `</ul></div></div>`;

          // Affichage du modal avec SweetAlert2
          Swal.fire({
            title: project.title || "Détails du projet",
            width: "80%",
            heightAuto: false,
            customClass: {
              popup: "custom-modal-height",
            },
            showClass: {
              popup: "swal2-show-custom",
            },
            hideClass: {
              popup: "swal2-hide-custom",
            },
            html: `
                      <div class="row">
                          <div class="col-md-6">
                              ${imagesHTML}
                          </div>
                          <div class="col-md-6 flex flex-col justify-center items-center">
                              <div class="project-infos">
                                  <p><span class="bold">Type :</span> ${
                                    project.title || "Non renseigné"
                                  }</p>
                                  <p><span class="bold">Description :</span> ${
                                    project.description || "Non renseignée"
                                  }</p>
                                  <p><span class="bold">Budget :</span> ${
                                    project.budget || "Non renseigné"
                                  } €</p>
                                  <p><span class="bold">Surface :</span> ${
                                    project.surface || "Non renseignée"
                                  } m²</p>
                                  <p><span class="bold">Durée :</span> ${
                                    project.duration || "Non renseignée"
                                  } jours</p>
                              </div>
                          </div>
                      </div>
                  `,
            showCloseButton: false,
            confirmButtonText: "Fermer",
            didOpen: () => {
              new Splide("#image-carousel", {
                type: "loop",
                perPage: 1,
                autoplay: true,
                pagination: true,
                arrows: false,
              }).mount();
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
