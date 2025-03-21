export function Menu() {
  const menuToggle = document.querySelector(".nav-btn");
  const menuContainer = document.querySelector(".container-menu");
  const body = document.body;
  const menuToggleWrapper = document.querySelector(".menu_toggle");
  const menuLogo = document.querySelector(".menu-header-logo");

  menuToggle.addEventListener("click", function (event) {
    event.stopPropagation(); // Empêche le clic sur le bouton de déclencher la fermeture immédiate
    menuContainer.classList.toggle("active");
    body.classList.toggle("menu-open");
    menuToggle.classList.toggle("nav-on");
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
    }
  });

  // Gestion du scroll pour ajouter/enlever .is-fixed
  const initialOffsetTop = menuToggleWrapper.offsetTop;

  window.addEventListener("scroll", () => {
    if (window.scrollY > initialOffsetTop) {
      menuToggleWrapper.classList.add("is-fixed");
      menuLogo.classList.add("hidden");
    } else {
      menuToggleWrapper.classList.remove("is-fixed");
      menuLogo.classList.remove("hidden");
    }
  });

  let menuToggleUser = document.querySelector(".menu-toggle_user");
  let navigation = document.querySelector(".navigation");

  menuToggleUser.onclick = function () {
    navigation.classList.toggle("active");
  };
}
