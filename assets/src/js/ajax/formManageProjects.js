document.addEventListener("DOMContentLoaded", () => {
  console.log(ajax_object);
  if (typeof ajax_object === "undefined") {
    console.error("Erreur: ajaxObject non défini !");
    return;
  }

  const handleAction = (
    selector,
    action,
    confirmMessage,
    successMessage,
    errorMessage
  ) => {
    document.querySelectorAll(selector).forEach((button) => {
      button.addEventListener("click", (event) => {
        event.preventDefault();

        const projectId = button.getAttribute("data-id");

        Swal.fire({
          title: "Êtes-vous sûr ?",
          text: confirmMessage,
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Oui",
          cancelButtonText: "Annuler",
        }).then((result) => {
          if (result.isConfirmed) {
            button.classList.add("disabled");

            fetch(ajax_object.ajaxurl, {
              method: "POST",
              headers: {
                "Content-Type": "application/x-www-form-urlencoded",
              },
              body: new URLSearchParams({
                action: action,
                project_id: projectId,
                manage_project_nonce: ajax_object.manage_project_nonce || "",
              }),
            })
              .then((response) => response.json())
              .then((data) => {
                if (data.success) {
                  Swal.fire({
                    title: "Succès",
                    text: successMessage || data.data,
                    icon: "success",
                  }).then(() => {
                    location.reload();
                  });
                } else {
                  Swal.fire({
                    title: "Erreur",
                    text:
                      errorMessage || data.data || "Une erreur est survenue.",
                    icon: "error",
                  });
                }
              })
              .catch((error) => {
                console.error("Erreur réseau :", error);
                Swal.fire({
                  title: "Erreur réseau",
                  text: "Impossible de contacter le serveur.",
                  icon: "error",
                });
              })
              .finally(() => {
                button.classList.remove("disabled");
              });
          }
        });
      });
    });
  };

  // Gestion de l'archivage
  handleAction(
    '[data-id][href="#"][class*="archive"]',
    "manage_archive_project",
    "Ce projet sera archivé.",
    "Le projet a été archivé avec succès.",
    "Impossible d'archiver ce projet."
  );

  // Gestion de la suppression
  handleAction(
    '[data-id][href="#"][class*="trash"]',
    "manage_delete_project",
    "Ce projet sera supprimé définitivement.",
    "Le projet a été supprimé avec succès.",
    "Impossible de supprimer ce projet."
  );
});
