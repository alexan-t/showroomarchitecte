import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

export function animateHome() {
  const logo = document.querySelector(".logo-img");
  const landingTitle = document.querySelector(".landing-title");
  const landingSlogan = document.querySelector(".landing-slogan");
  const menuUser = document.querySelector(".navigation");
  const menu = document.querySelector(".nav-btn");
  const account_not_connected = document.querySelector(
    ".account_not_connected"
  );
  const tl = gsap.timeline();

  gsap.fromTo(
    menu,
    {
      "will-change": " transform",
      opacity: 0,
    },
    {
      opacity: 1,
      duration: 1,
      ease: "power3.in",
    }
  );

  gsap.fromTo(
    account_not_connected,
    {
      "will-change": " transform",
      opacity: 0,
    },
    {
      opacity: 1,
      duration: 1,
      ease: "power3.in",
    }
  );

  gsap.fromTo(
    menuUser,
    {
      "will-change": " transform",
      opacity: 0,
    },
    {
      opacity: 1,
      duration: 1,
      ease: "power3.in",
    }
  );

  if (logo) {
    tl.fromTo(
      logo,
      {
        opacity: 0,
        y: 40,
        willChange: "opacity, transform",
      },
      {
        opacity: 1,
        y: 0,
        duration: 1,
        ease: "power3.out",
      }
    );
  }

  if (landingTitle) {
    tl.fromTo(
      landingTitle,
      {
        opacity: 0,
        y: 40,
        rotation: 3,
        willChange: "opacity, transform",
      },
      {
        opacity: 1,
        y: 0,
        rotation: 0,
        duration: 1.2,
        ease: "power3.out",
      }
    );
  }

  if (landingSlogan) {
    tl.fromTo(
      landingSlogan,
      {
        opacity: 0,
        y: 40,
        rotation: 3,
        willChange: "opacity, transform",
      },
      {
        opacity: 1,
        y: 0,
        rotation: 0,
        duration: 1.2,
        ease: "power3.out",
      }
    );
  }
}
