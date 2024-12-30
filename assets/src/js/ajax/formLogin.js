document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.querySelector(".connexion-form");
  const loginMessage = document.querySelector(".login_msg");
  const logAnim = document.querySelector(".log");

  if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
      e.preventDefault();

      // Animation de l'élément log
      logAnim.style.transform = "translate(-45%, -45%) scale(2)";
      logAnim.style.width = "100%";
      logAnim.style.height = "100%";

      // Attendez la fin de l'animation avant de continuer
      logAnim.addEventListener(
        "transitionend",
        () => {
          // Récupérer les données du formulaire
          const formData = new FormData(this);
          formData.append(
            "security",
            document.querySelector("#login_nonce").value
          );
          formData.append("action", "login_user");
          if (!formData.get("username") || !formData.get("password")) {
            loginMessage.innerHTML = `<p class="error-message">Veuillez remplir tous les champs.</p>`;
            loginMessage.style.opacity = "1";
            return;
          }

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
                loginMessage.innerHTML = `<p class="success-message">${data.data.message}</p>`;
                loginMessage.style.opacity = "1";

                // Redirection après succès
                setTimeout(() => {
                  window.location.href = data.data.redirect_url;
                }, 1500);
              } else {
                loginMessage.innerHTML = `<p class="error-message">${data.data.message}</p>`;
                loginMessage.style.opacity = "1";
                setTimeout(() => {
                  loginMessage.style.opacity = "0";
                  logAnim.style.transform = "translate(0, 0) scale(0)";
                  logAnim.style.width = "0";
                  logAnim.style.height = "0";
                }, 1000);
              }
            })
            .catch((error) => {
              // console.error("Erreur AJAX :", error);
              loginMessage.innerHTML = `<p class="error-message">Une erreur est survenue.</p>`;
              loginMessage.style.opacity = "1";
            });
        },
        { once: true }
      );
    });
  }
});
