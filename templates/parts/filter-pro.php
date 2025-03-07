<?php
global $wpdb;
$table_name = $wpdb->prefix . 'userinfos';

// Récupérer uniquement les valeurs uniques des architectes ayant is_page_public = 1
$locations = $wpdb->get_col("SELECT DISTINCT city FROM $table_name WHERE is_page_public = 1 AND city IS NOT NULL AND city != '' ORDER BY city ASC");
$architecte_type = $wpdb->get_col("SELECT DISTINCT architecte_type FROM $table_name WHERE is_page_public = 1 AND architecte_type IS NOT NULL AND architecte_type != ''");
$budgets = $wpdb->get_col("SELECT DISTINCT budget_moyen_chantiers FROM $table_name WHERE is_page_public = 1 AND budget_moyen_chantiers IS NOT NULL AND budget_moyen_chantiers > 0 ORDER BY budget_moyen_chantiers ASC");
$experiences = $wpdb->get_col("SELECT DISTINCT annees_experience FROM $table_name WHERE is_page_public = 1 AND annees_experience IS NOT NULL AND annees_experience > 0 ORDER BY annees_experience ASC");

?>

<div class="filter-pro">
    <!-- Bouton Appliquer -->
    <div class="mb-2 border-bottom filter-apply">
        <label class="label-title">Filtres appliqués :</label>
        <ul id="applied-filters">
            <li>Aucun filtre appliqué</li>
        </ul>
    </div>



    <!-- Filtre par Ville -->
    <div class="mb-2 border-bottom filter-location">
        <label class="label-title" for="location">Localisation</label>
        <select id="location">
            <option value="">Toutes</option>
            <?php foreach ($locations as $location): ?>
            <option value="<?php echo esc_attr($location); ?>"><?php echo esc_html($location); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Filtre par Type d'Architecte -->
    <div class="mb-2 border-bottom filter-architecte-type">
        <label class="label-title">Type d'architecte</label>
        <div class="checkbox-group">
            <?php
        $types_disponibles = ['Architecte', 'Architecte intérieur', "Architecte diplômé d'État", 'Architecte paysagiste'];
        
        foreach ($types_disponibles as $type) :
        ?>
            <div class="checkbox-item">
                <input type="checkbox" id="type-<?php echo esc_attr($type); ?>" name="architect-type[]"
                    value="<?php echo esc_attr($type); ?>">
                <label for="type-<?php echo esc_attr($type); ?>"><?php echo esc_html($type); ?></label>
            </div>
            <?php endforeach; ?>
        </div>
    </div>


    <!-- Filtre par Budget -->
    <div class="mb-2 border-bottom filter-budget">
        <label class="label-title" for="budget-min">Budget Moyen (€)</label>
        <div class="budget-inputs">
            <input type="number" id="budget-min" name="budget-min" placeholder="Min" min="0">
            <span> - </span>
            <input type="number" id="budget-max" name="budget-max" placeholder="Max" min="0">
        </div>
    </div>


    <!-- Filtre par Expérience -->
    <div class="mb-2 border-bottom filter-experience">
        <label class="label-title" for="experience-min">Années d'expérience</label>
        <div class="experience-inputs">
            <input type="number" id="experience-min" name="experience-min" placeholder="Min" min="0">
            <span> - </span>
            <input type="number" id="experience-max" name="experience-max" placeholder="Max" min="0">
        </div>
    </div>


    <div class="buttons-filter flex gap-1">
        <button class="btn btn-blue" id="apply-filters">Appliquer les filtres</button>
        <button class="btn btn-primary" id="clear-filters">Supprimer les filtres</button>
    </div>
</div>