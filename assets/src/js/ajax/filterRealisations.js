document.addEventListener("DOMContentLoaded", function () {
  const filters = ["#search", "#budget", "#surface", "#duration"];

  filters.forEach((selector) => {
    const el = document.querySelector(selector);
    if (!el) return;

    el.addEventListener("change", () => applyRealisationFilters(1));
    if (el.tagName === "INPUT") {
      el.addEventListener("keyup", () => applyRealisationFilters(1));
    }
  });

  window.applyRealisationFilters = function (page = 1) {
    const data = {
      action: "filter_realisation",
      page: page,
      search: document.querySelector("#search")?.value || "",
      budget: document.querySelector("#budget")?.value || "",
      surface: document.querySelector("#surface")?.value || "",
      duration: document.querySelector("#duration")?.value || "",
    };

    fetch(ajax_object.ajax_url, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams(data),
    })
      .then((res) => res.text())
      .then((html) => {
        const container = document.querySelector(
          ".archive-realisation .col-md-12.mt-5"
        );
        container.innerHTML = html;

        if (window.lenis) window.lenis.resize();

        // Si aucun projet détecté (ex: contenu vide ou pattern spécifique)
        if (!html.trim()) {
          container.innerHTML = `
            <div id="empty-lottie" style="width:300px; margin:50px auto;"></div>
            <p style="text-align:center; font-weight:500;">Aucune Réalisation trouvée</p>
            `;
          lottie.loadAnimation({
            container: document.getElementById("empty-lottie"),
            renderer: "svg",
            loop: true,
            autoplay: true,
            path: ajax_object.lottie_empty,
          });
          return;
        }

        // Réattache les événements de pagination
        document.querySelectorAll(".pagination-btn").forEach((btn) => {
          btn.addEventListener("click", function () {
            const page = parseInt(this.dataset.page);
            applyRealisationFilters(page);
          });
        });

        // Réinitialise Glightbox à chaque changement de DOM
        if (typeof GLightbox !== "undefined") {
          GLightbox({
            selector: ".glightbox",
            skin: "clean",
            openEffect: "zoom",
            closeEffect: "fade",
            slideEffect: "slide",
            moreText: "Voir plus",
            moreLength: 80,
            touchNavigation: true,
            keyboardNavigation: true,
            closeOnOutsideClick: true,
            descPosition: "left", // 👈 ici on affiche les descriptions en bas
            loop: true,
            zoomable: true,
            draggable: true,
            dragAutoSnap: true,
          });
        }
      })
      .catch((err) => console.error("Erreur AJAX : ", err));
  };

  applyRealisationFilters(1); // Initialisation
});
