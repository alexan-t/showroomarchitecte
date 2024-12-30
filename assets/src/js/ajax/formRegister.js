document.addEventListener("DOMContentLoaded", () => {
  const registerForm = document.querySelector(".inscription-form");
  const registerMsg = document.querySelector(".register_msg");
  const regAnim = document.querySelector(".reg");

  if (registerForm) {
    registerForm.addEventListener("submit", function (e) {
      e.preventDefault();

      // Animation de l'élément reg
      regAnim.style.transform = "translate(-45%, -45%) scale(2)";
      regAnim.style.width = "100%";
      regAnim.style.height = "100%";

      // Attendez la fin de l'animation avant de continuer
      regAnim.addEventListener(
        "transitionend",
        () => {
          // Récupérer les données du formulaire
          const formData = new FormData(this);
          formData.append(
            "security",
            document.querySelector("#register_nonce").value
          );
          formData.append("action", "register_user");

          // console.log([...formData.entries()]);

          // Appel AJAX
          fetch(formRegister.ajaxurl, {
            method: "POST",
            body: formData,
          })
            .then((response) => response.json())
            .then((data) => {
              // console.log("Réponse :", data);
              if (data.success) {
                registerMsg.innerHTML = `<p class="success-message">${data.data.message}</p>`;
              } else {
                registerMsg.innerHTML = `<p class="error-message">${data.data.message}</p>`;
              }
              registerMsg.style.opacity = "1";
            })
            .catch((error) => {
              // console.error("Erreur AJAX :", error);
              registerMsg.innerHTML = `<p class="error-message">Une erreur est survenue.</p>`;
              registerMsg.style.opacity = "1";
            });
        },
        { once: true }
      );
    });
  }
  document.body.addEventListener("click", function (e) {
    if (e.target.classList.contains("resend-email")) {
      e.preventDefault();

      const userId = e.target.dataset.userId;
      const security = e.target.dataset.security;

      if (!userId || !security) {
        console.error("Données manquantes pour le renvoi d'e-mail.");
        return;
      }

      const formData = new FormData();
      formData.append("action", "resend_password_email");
      formData.append("user_id", userId);
      formData.append("security", security);

      fetch(formRegister.ajaxurl, {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            registerMsg.innerHTML = `<p class="success-message">${data.data.message}</p>`;
            registerMsg.style.opacity = "1";
          } else {
            registerMsg.innerHTML = `<p class="error-message">${data.data.message}</p>`;
            registerMsg.style.opacity = "1";
          }
        })
        .catch((error) => {
          console.error("Erreur :", error);
          registerMsg.innerHTML = `<p class="error-message">Une erreur est survenue lors du renvoi de l'e-mail.</p>`;
        });
    }
  });
});
