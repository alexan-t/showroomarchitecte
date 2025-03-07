<?php
/*
Template Name: Résultats de Recherche
*/
get_header();
?>

<div class="container">
    <h2>Professionnels correspondant à votre projet</h2>

    <h3 class="section-title">🔝 Meilleures correspondances</h3>
    <div id="top-professionals" class="row"></div>

    <h3 class="section-title">✨ Professionnels qui pourraient vous intéresser</h3>
    <div id="potential-interests" class="row"></div>

    <h3 class="section-title">🏅 Professionnels Premium</h3>
    <div id="premium-professionals" class="row"></div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    fetch(ajaxObject.ajaxUrl, {
            method: "POST",
            body: new URLSearchParams({
                action: "get_matching_professionals"
            }),
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateSection("top-professionals", data.data.top_professionals);
                updateSection("potential-interests", data.data.potential_interests);
                updateSection("premium-professionals", data.data.random_premium);
            } else {
                document.getElementById("top-professionals").innerHTML =
                    "<p>Aucun professionnel trouvé.</p>";
                document.getElementById("potential-interests").innerHTML = "";
                document.getElementById("premium-professionals").innerHTML = "";
            }
        })
        .catch(error => {
            console.error("Erreur lors de la récupération :", error);
            document.getElementById("top-professionals").innerHTML = "<p>Une erreur est survenue.</p>";
        });
});

/**
 * Met à jour une section avec les professionnels récupérés.
 * @param {string} sectionId - ID de la section HTML.
 * @param {Array} professionals - Liste des professionnels à afficher.
 */
function updateSection(sectionId, professionals) {
    const container = document.getElementById(sectionId);
    if (professionals.length === 0) {
        container.innerHTML = "<p>Aucun professionnel dans cette section.</p>";
        return;
    }

    container.innerHTML = professionals.map(pro => `
        <div class="col-3">
            <a href="${pro.profile_url}" target="_blank">
                <div class="card-pro">
                    <div class="avatar">
                        <img src="${pro.photo}" alt="${pro.name}">
                    </div>
                    <p class="name">${pro.name}</p>
                    <p class="city flex items-center">
                        <svg class="icon icon-xl" aria-hidden="true">
                            <use xlink:href="#marker"></use>
                        </svg>
                        <span class="color-blue">${pro.city}</span>
                    </p>
                </div>
            </a>
        </div>
    `).join("");
}
</script>

<?php get_footer(); ?>