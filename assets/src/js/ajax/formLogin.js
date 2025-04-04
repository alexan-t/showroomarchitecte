document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("#connexionForm");
  if (!form) return; // Si le formulaire n'est pas présent, on ne fait rien
  // =======================
  // 🔔 1. Vérifie si l'URL contient un paramètre "activation"
  //    Exemple : /connexion/?activation=success
  // =======================
  handleActivationAlert();

  // =======================
  // 💾 2. Pré-remplit le champ email si "se souvenir de moi" avait été coché
  // =======================
  restoreRememberedEmail();

  let loginAttempts = 0; // Compte les tentatives échouées

  // =======================
  // 📤 3. Gestion de la soumission du formulaire de connexion
  // =======================
  form.addEventListener("submit", async (e) => {
    e.preventDefault(); // Empêche l'envoi classique du formulaire
    await handleLogin(form);
  });

  // =======================
  // 🔑 4. Gestion du lien "mot de passe oublié"
  // =======================
  const forgotLink = document.querySelector("#forgot-password-link");
  if (forgotLink) {
    forgotLink.addEventListener("click", handleForgotPassword);
  }

  // =======================
  // 📌 5. FONCTIONS UTILITAIRES
  // =======================

  // 💬 Affiche une alerte si le compte vient d’être activé ou si le lien est invalide
  function handleActivationAlert() {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get("activation");

    if (status === "success") {
      Swal.fire({
        icon: "success",
        title: "Compte activé",
        text: "Votre compte est maintenant activé. Vous pouvez vous connecter.",
        confirmButtonText: "OK",
      });
    } else if (status === "failed") {
      Swal.fire({
        icon: "error",
        title: "Erreur d’activation",
        text: "Le lien d’activation est invalide ou a expiré.",
        confirmButtonText: "Fermer",
      });
    }
  }

  // 🔁 Restaure l'email sauvegardé si "Se souvenir" est activé
  function restoreRememberedEmail() {
    const emailField = document.querySelector('input[name="username"]');
    const rememberCheckbox = document.querySelector("#remember-me");

    if (localStorage.getItem("savedEmail")) {
      emailField.value = localStorage.getItem("savedEmail");
      rememberCheckbox.checked = true;
    }
  }

  // 🔐 Gère la connexion à l'envoi du formulaire
  async function handleLogin(form) {
    const formData = new FormData(form);
    formData.append("action", "login_user");
    formData.append("security", formLogin.nonce); // Token de sécurité WordPress
    formData.append(
      "remember",
      document.querySelector("#remember-me").checked ? "1" : "0"
    );

    const email = form.querySelector('input[name="username"]').value;
    const remember = document.querySelector("#remember-me").checked;

    // Sauvegarde l’email dans le navigateur si "se souvenir" est coché
    if (remember) {
      localStorage.setItem("savedEmail", email);
    } else {
      localStorage.removeItem("savedEmail");
    }

    try {
      const response = await fetch(formLogin.ajaxurl, {
        method: "POST",
        body: formData,
      });

      const data = await response.json();

      if (data.success) {
        // ✅ Connexion réussie
        Swal.fire({
          icon: "success",
          title: data.data.message,
          toast: true,
          position: "top-end",
          showConfirmButton: false,
          timer: 2000,
        });

        setTimeout(() => {
          window.location.href = data.data.redirect_url; // Redirection
        }, 2000);
      } else {
        // ❌ Compte non activé
        if (data.data.need_activation) {
          Swal.fire({
            title: "Compte inactif",
            text: data.data.message,
            icon: "warning",
            confirmButtonText: "OK",
          }).then(() => {
            window.location.reload(); // Recharge la page pour régénérer le nonce
          });
          return;
        }

        // ❌ Erreur de connexion (mauvais identifiants par exemple)
        Swal.fire({
          icon: "error",
          title: "Erreur",
          text: data.data.message,
          toast: true,
          position: "top-end",
          showConfirmButton: false,
          timer: 3000,
        });

        // 🔁 Après 3 échecs, propose de réinitialiser le mot de passe
        if (data.data.message.includes("Mot de passe incorrect")) {
          loginAttempts++;
          if (loginAttempts >= 3) {
            await suggestPasswordReset(email);
            loginAttempts = 0;
          }
        }
      }
    } catch (error) {
      showServerError();
    }
  }

  // 💡 Propose à l'utilisateur de réinitialiser son mot de passe
  async function suggestPasswordReset(email) {
    const result = await Swal.fire({
      title: "Trop de tentatives",
      text: "Souhaitez-vous réinitialiser votre mot de passe ?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Oui, réinitialiser",
      cancelButtonText: "Annuler",
    });

    if (result.isConfirmed) {
      await sendPasswordReset(email);
    }
  }

  // 📧 Envoie l'email de réinitialisation de mot de passe
  async function sendPasswordReset(email) {
    const formData = new FormData();
    formData.append("action", "forgot_password");
    formData.append("security", formLogin.nonce);
    formData.append("email", email);

    try {
      const response = await fetch(formLogin.ajaxurl, {
        method: "POST",
        body: formData,
      });

      const data = await response.json();

      if (data.success) {
        Swal.fire({
          icon: "success",
          title: "Email envoyé",
          text: data.data.message,
          confirmButtonText: "OK",
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Erreur",
          text: data.data.message,
          confirmButtonText: "Fermer",
        });
      }
    } catch (error) {
      showServerError();
    }
  }

  // 🔐 Lien "Mot de passe oublié" (demande manuelle)
  async function handleForgotPassword(e) {
    e.preventDefault();

    const { value: email } = await Swal.fire({
      title: "Réinitialisation du mot de passe",
      input: "email",
      inputLabel: "Entrez votre adresse e-mail",
      inputPlaceholder: "exemple@email.com",
      confirmButtonText: "Envoyer",
      cancelButtonText: "Annuler",
      showCancelButton: true,
      inputValidator: (value) => {
        if (!value) return "Veuillez entrer un e-mail.";
        const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!pattern.test(value)) return "Adresse e-mail invalide.";
      },
    });

    if (email) {
      await sendPasswordReset(email);
    }
  }

  // ❌ Alerte en cas d’erreur serveur inattendue
  function showServerError() {
    Swal.fire({
      icon: "error",
      title: "Erreur serveur",
      text: "Une erreur est survenue, veuillez réessayer.",
      toast: true,
      position: "top-end",
      showConfirmButton: false,
      timer: 3000,
    });
  }
});
