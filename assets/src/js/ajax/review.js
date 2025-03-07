document.addEventListener("DOMContentLoaded", function () {
  const reviewTextarea = document.querySelector("#user_review");
  const reviewButton = document.querySelector(".btn-blue");
  const reviewsContainer = document.querySelector(".reviews-list");
  const reviewSlider = document.querySelector("#reviews-slider");
  //SLIDER AVIS
  if (reviewSlider) {
    new Splide("#reviews-slider", {
      type: "slide", // Carousel en boucle
      perPage: 1, // Une seule slide affichée, contenant 2 avis
      perMove: 1, // Défilement d'une slide à la fois
      gap: "30px", // Espacement entre les slides
      autoplay: true, // Défilement automatique
      interval: 5000, // 5 secondes entre chaque slide
      pauseOnHover: true, // Pause lorsqu'on survole
      arrows: true, // Flèches de navigation activées
      pagination: true, // Afficher les points de pagination
    }).mount();

    let professionalId = new URLSearchParams(window.location.search).get("id");

    if (!reviewButton || !professionalId) return;

    function fetchUserReview() {
      fetch(
        `${ajax_object.ajax_url}?action=get_user_review&professional_id=${professionalId}`
      )
        .then((response) => response.json())
        .then((data) => {
          if (
            data.success &&
            typeof data.review !== "undefined" &&
            data.review !== null
          ) {
            reviewTextarea.value = data.review;
            reviewButton.textContent = "Modifier votre avis";
            reviewButton.dataset.action = "update";
          } else {
            reviewTextarea.value = "";
            reviewButton.dataset.action = "add";
          }
        })
        .catch((error) => console.error("Erreur AJAX:", error));
    }

    function manageReview(actionType, reviewId = null, elementToRemove = null) {
      let reviewText = reviewTextarea.value.trim();

      if (
        (actionType === "add" || actionType === "update") &&
        reviewText === ""
      ) {
        Swal.fire({
          icon: "warning",
          title: "Oops...",
          text: "Veuillez écrire un avis avant d'envoyer.",
        });
        return;
      }

      let formData = new FormData();
      formData.append("action", "manage_review");
      formData.append("professional_id", professionalId);
      formData.append("review_text", reviewText);
      formData.append("action_type", actionType);
      if (reviewId) formData.append("review_id", reviewId);
      formData.append("security", ajax_object.nonce);

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
              text: data.data.message,
              timer: 2000,
              showConfirmButton: false,
            }).then(() => {
              if (actionType === "delete" && elementToRemove) {
                elementToRemove.remove(); // Supprime l'avis de la page immédiatement
              } else {
                location.reload();
              }
            });
          } else {
            console.error("Erreur:", data);
            Swal.fire({
              icon: "error",
              title: "Erreur",
              text: data.data.message,
            });
          }
        })
        .catch((error) => {
          console.error("Erreur:", error);
          Swal.fire({
            icon: "error",
            title: "Erreur",
            text: "Une erreur est survenue. Veuillez réessayer.",
          });
        });
    }

    reviewButton.addEventListener("click", function () {
      manageReview(reviewButton.dataset.action);
    });

    // Suppression d'un avis
    if (reviewsContainer) {
      reviewsContainer.addEventListener("click", function (event) {
        if (event.target.closest(".delete-review")) {
          let button = event.target.closest(".delete-review");
          let reviewId = button.dataset.id;
          let reviewItem = button.closest(".review-item");

          Swal.fire({
            title: "Êtes-vous sûr ?",
            text: "Cette action est irréversible.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Oui, supprimer",
            cancelButtonText: "Annuler",
          }).then((result) => {
            if (result.isConfirmed) {
              manageReview("delete", reviewId, reviewItem);
            }
          });
        }
      });
    }

    fetchUserReview();

    const maxLength = 100;
    if (!reviewTextarea) return;

    reviewTextarea.addEventListener("input", function () {
      if (reviewTextarea.value.length > maxLength) {
        reviewTextarea.value = reviewTextarea.value.substring(0, maxLength);
      }
      document.querySelector("#charCount").textContent =
        reviewTextarea.value.length + "/" + maxLength;
    });
  }
});
