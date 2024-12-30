import Splide from "@splidejs/splide";
import { AutoScroll } from "@splidejs/splide-extension-auto-scroll";

export function Carousel() {
  const talentcarousel = document.querySelector("#talentCarousel");
  if (talentcarousel) {
    new Splide(talentcarousel, {
      type: "slide", // Carousel en boucle
      perPage: 3, // 3 slides visibles par page
      gap: "1rem", // Espacement entre les slides
      pagination: true, // Affiche la pagination (points)
      arrows: false, // Affiche les flèches de navigation
      breakpoints: {
        768: {
          perPage: 2, // 1 slide visible sur mobile
          gap: "0.5rem",
        },
      },
    }).mount();
  }
}
