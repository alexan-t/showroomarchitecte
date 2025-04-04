document.addEventListener("DOMContentLoaded", function () {
  const filterInputs = document.querySelectorAll(
    "#search, #city, #type_bien, #type_projet, #sort_date, input[name='budget'], input[name='surface']"
  );

  filterInputs.forEach((el) => {
    el.addEventListener("change", () => applyFilters(1));
    if (el.tagName === "INPUT" && el.type === "text") {
      el.addEventListener("keyup", () => applyFilters(1));
    }
  });

  document
    .getElementById("reset-filters")
    .addEventListener("click", function () {
      document.getElementById("search").value = "";
      document.getElementById("city").value = "";
      document.getElementById("type_bien").value = "";
      document.getElementById("type_projet").value = "";
      document.getElementById("sort_date").value = "desc";
      document
        .querySelectorAll("input[name='budget']")
        .forEach((e) => (e.checked = false));
      document
        .querySelectorAll("input[name='surface']")
        .forEach((e) => (e.checked = false));
      applyFilters(1);
    });

  window.applyFilters = function (page = 1) {
    const data = {
      action: "filter_projects",
      search: document.getElementById("search").value,
      city: document.getElementById("city").value,
      type_bien: document.getElementById("type_bien").value,
      type_projet: document.getElementById("type_projet").value,
      sort_date: document.getElementById("sort_date").value,
      page: page,
      budget: Array.from(
        document.querySelectorAll("input[name='budget']:checked")
      ).map((e) => e.value),
      surface:
        document.querySelector("input[name='surface']:checked")?.value || "",
    };

    fetch(ajax_object.ajax_url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams(data),
    })
      .then((res) => res.text())
      .then((html) => {
        document.querySelector(".list-projects .row").innerHTML = html;

        if (lenis) {
          lenis.scrollTo(0, { duration: 0, immediate: true });

          // S'il est arrêté, on le redémarre
          if (lenis.stopped) {
            lenis.start();
          }
        }

        console.log(lenis);

        // Rebind des events pagination
        document.querySelectorAll(".pagination-btn").forEach((btn) => {
          btn.addEventListener("click", function () {
            const targetPage = this.getAttribute("data-page");
            applyFilters(targetPage);
          });
        });
      })
      .catch((err) => {
        console.error("Erreur AJAX : ", err);
      });
  };

  // Lancement au chargement initial
  applyFilters(1);
});

document.querySelectorAll(".open-project-details").forEach((button) => {
  document.addEventListener("click", function (e) {
    const button = e.target.closest(".open-project-details");
    if (!button) return; // Clique en dehors, on ignore

    e.preventDefault(); // Juste au cas où

    // 👉 toute ta logique ici, avec `button` à la place de `this` :
    const name = button.dataset.name;
    const description = button.dataset.description;
    let needs = [];
    try {
      needs = JSON.parse(button.dataset.needs);
    } catch (e) {
      console.warn("Erreur de parsing des besoins :", e);
    }

    const isDesktop = window.innerWidth > 768;
    const borderClass = isDesktop ? "border-right" : "border-bottom";
    const proprietaire = button.dataset.proprietaire;
    const projet = button.dataset.projet;
    const surface = button.dataset.surface;
    const work = button.dataset.work;
    const budget = button.dataset.budget;
    const city = button.dataset.city;
    const timeline = button.dataset.timeline;
    let status = button.dataset.status;
    status = status === "active" ? "Actif" : "Status";
    const client_name = button.dataset.client_name;
    const client_img = button.dataset.client_img;
    const attachmentsRaw = button.dataset.attachments;
    let attachments = [];

    try {
      attachments = JSON.parse(attachmentsRaw);
    } catch (e) {
      console.warn("Erreur de parsing JSON des fichiers :", e);
    }

    // Séparer images et autres fichiers
    const images = [];
    const documents = [];

    attachments.forEach((file) => {
      const ext = file.split(".").pop().toLowerCase();
      if (["jpg", "jpeg", "png", "webp"].includes(ext)) {
        images.push(file);
      } else {
        documents.push(file);
      }
    });

    let leftCol = `
          <h3>Détails du projet</h3>
                  <div class="">
                      <p class="bold color-dark">Nom du Projet</p>
                      <p class="bold-500">${name}</p>
                  </div>
                  <div class="">
                      <p class="bold color-dark">Description du Projet</p>
                      <p class="bold-500">${description}</p>
                  </div>  
                  <div class="">
                      <p class="bold color-dark">Client</p>
                      <div class="flex gap-1 items-center">
                          <img src="${client_img}" alt="Image de ${client_name}" style="max-width: 50px;border-radius: 50%;" />
                          <p class="bold-500" style="margin-block-end: 0">${client_name}</p>
                      </div>
                  </div>  
                  <div class="">
                      <p class="bold color-dark">Début du projet</p>
                      <div class="flex gap-1 items-center">
                          <ion-icon name="calendar-outline"></ion-icon>
                          <p class="bold-500" style="margin-block-end: 0">${timeline}</p>
                      </div>
                  </div>
                  <div class="">
                      <p class="bold color-dark">Status</p>
                      <div class="flex gap-1 items-center">
                          <span style="background:#eee; padding:2px 8px; border-radius:8px;">${status}</span>
                      </div>
                  </div>
                  <div class="">
                      <p class="bold color-dark">Ville</p>
                      <div class="flex gap-1 items-center">
                          <ion-icon name="navigate-outline"></ion-icon>
                          <p class="bold-500" style="margin-block-end: 0">${city}</p>
                      </div>
                  </div>
                  <div class="">
                      <p class="bold color-dark">Type de travaux</p>
                      <div class="flex gap-1 items-center">
                          <ion-icon name="hammer-outline"></ion-icon>
                          <p class="bold-500" style="margin-block-end: 0">${projet}</p>
                      </div>
                  </div>
                  <div class="">
                      <p class="bold color-dark">Autre informations</p>
                      <div class="flex flex-col gap-1 justify-center">
                          <p><strong>Surface totale:</strong> ${surface} m²</p>
                          <p><strong>Surface travaux:</strong> ${work} m²</p>
                          <p><strong>Budget:</strong> ${budget}€</p>
                          <p><strong>Besoins:</strong> ${needs}</p>
                      </div>
              </div>
              
          `;

    let rightCol = `
              <h3>Documents du projet</h3>
              <div class="row">
          `;

    images.forEach((img) => {
      const name = img.split("/").pop();
      rightCol += `
                  <div class="col-md-6 p-relative" style="width: 100%;">
                      <a href="${img}" class="glightbox" data-gallery="gallery1">
                          <img src="${img}" alt="${name}" style="width: 100%; height: 200px; object-fit: cover; border-radius: 4px;" />
                      </a>
                      <p class="text-on-image">${name}</p>
                  </div>
              `;
    });
    rightCol += `</div>`;
    documents.forEach((doc) => {
      const name = doc.split("/").pop();
      rightCol += `
                  <div  style="display: flex; align-items: center; gap: 10px; background: #f5f5f5; padding: 10px; border-radius: 6px; margin-bottom: 8px;">
                      <img src="https://cdn-icons-png.flaticon.com/512/337/337946.png" alt="pdf icon" width="20" />
                      <a href="${doc}" target="_blank" style="color:#000; text-decoration:none;">${name}</a>
                  </div>
              `;
    });
    let html = "";
    if (attachments && attachments.length > 0) {
      html = `
                  <div style="display: flex; flex-wrap: wrap; gap: 40px;">
                      <div class="row">
                          <div class="col-md-6 text-left flex flex-col gap-2 ${borderClass}">${leftCol}</div>
                          <div class="col-md-6 text-left">${rightCol}</div>
                      </div>
                  </div>
              `;
      Swal.fire({
        html,
        showCloseButton: true,
        showConfirmButton: false,
        width: window.innerWidth > 768 ? "50vw" : "90vw",
        didOpen: () => {
          GLightbox({
            selector: ".glightbox",
          });
        },
      });
    } else {
      html = `
              <div>
                  <div class="container">
                      <div class="col-md-12 flex flex-col items-center gap-2">${leftCol}</div>
                  </div>
              </div>
          `;
      Swal.fire({
        html,
        showCloseButton: true,
        showConfirmButton: false,
        width: window.innerWidth > 768 ? "20vw" : "90vw",
        didOpen: () => {
          GLightbox({
            selector: ".glightbox",
          });
        },
      });
    }
  });
});
