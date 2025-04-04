import Lenis from "lenis";

export const lenis = new Lenis({
  wrapper: document.querySelector(".lenis"),
  content: document.querySelector(".lenis"),
  duration: 2,
  easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
  smooth: true,
});

function raf(time) {
  lenis.raf(time);
  requestAnimationFrame(raf);
}

requestAnimationFrame(raf);

// ✅ Le rendre accessible globalement
window.lenis = lenis;

document.addEventListener("click", (event) => {
  const btn = event.target.closest(".pagination-btn");
  if (btn) {
    lenis.scrollTo(0, { duration: 0, immediate: true });
    lenis.resize();
  }
});
