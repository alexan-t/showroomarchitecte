import gsap from "gsap";

export function Menu(lenis) {
  const menuToggle = document.querySelector(".nav-btn");
  const menuContainer = document.querySelector(".container-menu");
  const body = document.body;
  const menuToggleWrapper = document.querySelector(".menu_toggle");
  const menuLogo = document.querySelector(".menu-header-logo");

  menuToggle.addEventListener("click", function (event) {
    event.stopPropagation();
    const isMenuOpen = menuContainer.classList.toggle("active");
    body.classList.toggle("menu-open");
    menuToggle.classList.toggle("nav-on");

    // 👇 Désactive ou réactive le scroll Lenis
    if (isMenuOpen) {
      lenis.stop(); // stoppe le scroll smooth
    } else {
      lenis.start(); // redémarre le scroll smooth
    }
  });

  // Ferme le menu si clic en dehors
  document.addEventListener("click", function (event) {
    if (
      !menuContainer.contains(event.target) &&
      !menuToggle.contains(event.target)
    ) {
      menuContainer.classList.remove("active");
      body.classList.remove("menu-open");
      menuToggle.classList.remove("nav-on");

      // 👇 Réactive le scroll si fermé via clic extérieur
      lenis.start();
    }
  });

  // ✅ Animation au scroll avec GSAP
  let isHidden = false;

  if (lenis && menuToggleWrapper && menuLogo) {
    lenis.on("scroll", ({ scroll }) => {
      const threshold = menuToggleWrapper.offsetTop;

      if (scroll > threshold && !isHidden) {
        gsap.to(menuLogo, {
          y: -100,
          opacity: 0,
          duration: 0.4,
          ease: "power2.out",
        });
        isHidden = true;
      } else if (scroll <= threshold && isHidden) {
        gsap.to(menuLogo, {
          y: 0,
          opacity: 1,
          duration: 0.4,
          ease: "power2.out",
        });
        isHidden = false;
      }
      // Ajout / suppression de la classe is-fixed
      if (scroll > 0) {
        menuToggleWrapper.classList.add("is-fixed");
      } else {
        menuToggleWrapper.classList.remove("is-fixed");
      }
    });
  }

  // Toggle menu utilisateur
  const menuToggleUser = document.querySelector(".menu-toggle_user");
  const navigation = document.querySelector(".navigation");

  if (menuToggleUser) {
    menuToggleUser.addEventListener("click", function () {
      navigation.classList.toggle("active");
    });
  }
}
