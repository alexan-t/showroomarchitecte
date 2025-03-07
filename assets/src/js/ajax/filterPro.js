jQuery(document).ready(function ($) {
  $("#apply-filters").on("click", function () {
    let appliedFilters = [];

    let location = $("#location").val();
    let budgetMin = $("#budget-min").val();
    let budgetMax = $("#budget-max").val();
    let experienceMin = $("#experience-min").val();
    let experienceMax = $("#experience-max").val();

    // Récupérer tous les types d'architectes cochés
    let architectTypes = [];
    $("input[name='architect-type[]']:checked").each(function () {
      architectTypes.push($(this).val());
    });

    // Ajouter les filtres sélectionnés à la liste
    if (location) {
      appliedFilters.push(location);
    }

    if (architectTypes.length > 0) {
      appliedFilters.push(architectTypes.join(", "));
    }

    if (budgetMin || budgetMax) {
      appliedFilters.push(
        (budgetMin ? budgetMin + "€" : "0€") +
          " à " +
          (budgetMax ? budgetMax + "€" : "∞")
      );
    }

    if (experienceMin || experienceMax) {
      appliedFilters.push(
        (experienceMin ? experienceMin + " ans" : "0 ans") +
          " à " +
          (experienceMax ? experienceMax + " ans" : "∞ ans")
      );
    }

    // Mettre à jour la liste des filtres appliqués
    let filterList = $("#applied-filters");
    filterList.empty();

    if (appliedFilters.length > 0) {
      appliedFilters.forEach(function (filter) {
        filterList.append("<li>" + filter + "</li>");
      });
    } else {
      filterList.append("<li>Aucun filtre appliqué</li>"); // Affichage par défaut
    }

    // Envoyer les filtres en AJAX
    $.ajax({
      url: ajaxurl.ajaxurl,
      type: "POST",
      data: {
        action: "filter_professionnels",
        location: location,
        budgetMin: budgetMin,
        budgetMax: budgetMax,
        experienceMin: experienceMin,
        experienceMax: experienceMax,
        architectTypes: architectTypes,
      },
      beforeSend: function () {
        $(".professionnel-list").html("<p>Chargement...</p>");
      },
      success: function (response) {
        $(".professionnel-list").html(response);
      },
      error: function () {
        $(".professionnel-list").html(
          "<p>Erreur lors du chargement des résultats.</p>"
        );
      },
    });
  });

  // Réinitialiser les filtres et remettre "Aucun filtre appliqué"
  $("#clear-filters").on("click", function () {
    $("#location").val("");
    $("#budget-min").val("");
    $("#budget-max").val("");
    $("#experience-min").val("");
    $("#experience-max").val("");
    $("input[name='architect-type[]']").prop("checked", false);

    let filterList = $("#applied-filters");
    filterList.empty().append("<li>Aucun filtre appliqué</li>"); // Remettre le texte par défaut

    $("#apply-filters").trigger("click"); // Relancer AJAX pour afficher tous les résultats
  });
});
