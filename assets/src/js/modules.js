import { Menu } from "./modules/Menu.js";
import { Carousel } from "./modules/Carousel.js";
import { Lightbox } from "./modules/Lightbox.js";
import { animateHome } from "./modules/animations/HomeAnimation.js";
import { lenis } from "./modules/lenis";

export function initComponents() {
  Menu(lenis);
  Carousel();
  Lightbox();

  // Initialiser explicitement Lenis
  if (lenis) {
    lenis.resize();
  }

  // ✅ Lancer l'animation home uniquement si on est sur la page d'accueil
  if (document.body.classList.contains("home")) {
    animateHome();
  }
}
