<?php 

?>



<div class="filters-container">
    <h3 class="bold-400">Filtres</h3>

    <div class="py-1">
        <label for="search">Recherche</label>
        <input type="text" id="search" placeholder="Rechercher un projet...">
    </div>

    <div class="py-1">
        <label for="city">Ville</label>
        <select class="bold-500" id="city">
            <option value="">Toutes les villes</option>
            <!-- Ajoute dynamiquement les villes ici -->
        </select>
    </div>

    <div class="py-1">
        <fieldset>
            <legend>Budget</legend>
            <div class="flex gap-1">
                <input type="checkbox" name="budget" value="0-10000"><label class="bold-500"> 0 - 10k€</label>
            </div>
            <div class="flex gap-1">
                <input type="checkbox" name="budget" value="10000-50000"><label class="bold-500"> 10k - 50k€</label>
            </div>
            <div class="flex gap-1">
                <input type="checkbox" name="budget" value="50000+"><label class="bold-500"> 50k€ +</label>
            </div>
        </fieldset>
    </div>

    <div class="py-1">
        <fieldset>
            <legend>Surface</legend>
            <div class="flex gap-1">
                <input type="radio" name="surface" value="petite"><label class="bold-500">Petite</label>
            </div>
            <div class="flex gap-1">
                <input type="radio" name="surface" value="moyenne"><label class="bold-500"> Moyenne</label>
            </div>
            <div class="flex gap-1">
                <input type="radio" name="surface" value="grande"> <label class="bold-500">Grande</label>
            </div>
        </fieldset>
    </div>

    <div class="py-1">
        <label for="type_bien">Type de bien</label>
        <select class="bold-500" id="type_bien">
            <option value="">Tous les types</option>
            <option value="maison">Maison</option>
            <option value="appartement">Appartement</option>
            <option value="local">Local pro</option>
            <option value="autre">Autre</option>
        </select>
    </div>

    <div class="py-1">
        <label for="type_projet">Type de projet</label>
        <select class="bold-500" id="type_projet">
            <option value="">Tous les projets</option>
            <option value="renovation">Rénovation</option>
            <option value="construction">Construction neuve</option>
            <option value="extension">Extension</option>
            <option value="amenagement">Aménagement intérieur</option>
        </select>
    </div>

    <div class="py-1">
        <label for="sort_date">Trier par date</label>
        <select class="bold-500" id="sort_date">
            <option value="desc">Les plus récents</option>
            <option value="asc">Les plus anciens</option>
        </select>
    </div>

    <!-- Ajout du bouton reset -->
    <div class="py-2">
        <button id="reset-filters" class="rounded-5 flex w-100 justify-center btn btn-outline-dark">Réinitialiser les
            filtres</button>
    </div>

</div>