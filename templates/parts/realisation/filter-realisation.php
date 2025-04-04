<div class="filter-realisation px-2 items-center row">
    <!-- Bouton Appliquer -->
    <!-- <div class="filter-apply">
        <label class="label-title">Filtres appliqués :</label>
        <ul id="applied-filters">
            <li>Aucun filtre appliqué</li>
        </ul>
    </div> -->

    <!-- Filtre par Mot -->
    <div class="col-md-3">
        <label for="search">Recherche</label><br>
        <input class="bold-500" type="text" name="search" id="search"
            placeholder="Rechercher par titre ou description...">
    </div>


    <!-- Filtre par Budget -->
    <div class="col-md-3">
        <label for="budget">Budget</label><br>
        <select class="bold-500" name="budget" id="budget">
            <option value="">Tous les budgets</option>
            <option value="Moins de 10 000 €">Moins de 10 000 €</option>
            <option value="10 000 - 50 000 €">10 000 - 50 000 €</option>
            <option value="Plus de 50 000 €">Plus de 50 000 €</option>
        </select>
    </div>

    <!-- Filtre par Surface -->
    <div class="col-md-3">
        <label for="surface">Surface</label><br>
        <select class="bold-500" name="surface" id="surface">
            <option value="">Toutes les surfaces</option>
            <option value="Moins de 30 m²">Moins de 30 m²</option>
            <option value="30 à 100 m²">30 à 100 m²</option>
            <option value="Plus de 100 m²">Plus de 100 m²</option>
        </select>
    </div>

    <!-- Filtre par Durée -->
    <div class="col-md-3">
        <label for="duration">Durée</label><br>
        <select class="bold-500" name="duration" id="duration">
            <option value="">Toutes les durées</option>
            <option value="1 mois">1 mois</option>
            <option value="2-6 mois">2-6 mois</option>
            <option value="Plus de 6 mois">Plus de 6 mois</option>
        </select>
    </div>



</div>