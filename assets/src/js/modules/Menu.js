export function Menu() {
  const menuToggle = document.querySelector(".nav-btn");
  const menuContainer = document.querySelector(".container-menu");
  const body = document.body;

  menuToggle.addEventListener("click", function () {
    menuContainer.classList.toggle("active");
    body.classList.toggle("menu-open");
    menuToggle.classList.toggle("nav-on");
  });
}
