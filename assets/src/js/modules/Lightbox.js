let lightboxInstance; // Variable globale pour stocker l'instance
export function Lightbox() {
  // Vérifier si GLightbox est bien importé
  if (typeof GLightbox === "undefined") {
    console.error("❌ ERREUR: GLightbox n'est pas chargé !");
    return;
  }

  // Initialisation et stockage de l'instance dans lightboxInstance
  lightboxInstance = GLightbox({
    selector: ".glightbox",
    touchNavigation: true,
    loop: true,
    closeButton: true,
    zoomable: true,
    draggable: true,
    autoplayVideos: true,
  });
}
