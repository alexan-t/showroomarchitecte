export function Menu() {
  const menuButton = document.getElementById("menu-button");
  const icon = document.querySelector(".menu.icon");
  const menuContainer = document.querySelector(".menu-header-links-container");
  const body = document.querySelector("body");

  if (menuButton) {
    menuButton.addEventListener("click", () => {
      const isExpanded = menuButton.getAttribute("aria-expanded") === "true";
      menuButton.setAttribute("aria-expanded", !isExpanded);
      icon.classList.toggle("open");
      menuContainer.classList.toggle("open");

      // Ajouter ou retirer la classe no-scroll
      if (!isExpanded) {
        body.classList.add("no-scroll");
      } else {
        body.classList.remove("no-scroll");
      }
    });
  }
}
