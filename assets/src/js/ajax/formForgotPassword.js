document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("#forgotPasswordForm");
  if (!form) return;

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    formData.append("action", "reset_user_password");
    formData.append("security", formLogin.nonce); // Assuré via wp_localize_script

    try {
      const response = await fetch(formLogin.ajaxurl, {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (result.success) {
        Swal.fire({
          icon: "success",
          title: "Succès",
          text: result.data.message,
          timer: 2000,
          showConfirmButton: false,
        });

        setTimeout(() => {
          window.location.href = result.data.redirect;
        }, 2000);
      } else {
        Swal.fire({
          icon: "error",
          title: "Erreur",
          text: result.data.message,
          confirmButtonText: "Fermer",
        });
      }
    } catch (error) {
      Swal.fire({
        icon: "error",
        title: "Erreur réseau",
        text: "Une erreur est survenue, veuillez réessayer.",
        confirmButtonText: "Fermer",
      });
    }
  });
});
