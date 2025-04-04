<?php
global $wpdb;
$table_name = $wpdb->prefix . 'professional_det';

// Récupérer uniquement les valeurs uniques des architectes ayant is_page_public = 1
$locations = $wpdb->get_col("SELECT DISTINCT city FROM $table_name WHERE is_page_public = 1 AND city IS NOT NULL AND city != '' ORDER BY city ASC");
$architecte_type = $wpdb->get_col("SELECT DISTINCT architecte_type FROM $table_name WHERE is_page_public = 1 AND architecte_type IS NOT NULL AND architecte_type != ''");
$budgets = $wpdb->get_col("SELECT DISTINCT budget_moyen_chantiers FROM $table_name WHERE is_page_public = 1 AND budget_moyen_chantiers IS NOT NULL AND budget_moyen_chantiers > 0 ORDER BY budget_moyen_chantiers ASC");
$experiences = $wpdb->get_col("SELECT DISTINCT annees_experience FROM $table_name WHERE is_page_public = 1 AND annees_experience IS NOT NULL AND annees_experience > 0 ORDER BY annees_experience ASC");

?>

<div class="filter-pro items-center row">
    <!-- Bouton Appliquer -->
    <!-- <div class="filter-apply">
        <label class="label-title">Filtres appliqués :</label>
        <ul id="applied-filters">
            <li>Aucun filtre appliqué</li>
        </ul>
    </div> -->



    <!-- Filtre par Ville -->
    <div class="col-md-3">
        <p>Localisation</p>
        <div class="filter-location">
            <select class="input-select bold-500" id="location">
                <option value="">Toute localisation</option>
                <?php foreach ($locations as $location): ?>
                <option value="<?php echo esc_attr($location); ?>"><?php echo esc_html($location); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Filtre par Type d'Architecte -->
    <div class="col-md-3">
        <p>Type d'architecte</p>
        <select class="input-select bold-500" id="architect-type">
            <option value="">Tout type</option>
            <?php
        $types_disponibles = ['Architecte', 'Architecte intérieur', "Architecte diplômé d'État", 'Architecte paysagiste'];
        
        foreach ($types_disponibles as $type) :
        ?>
            <option value="<?php echo esc_attr($type); ?>"><?php echo esc_html($type); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Filtre par Budget -->
    <div class="col-md-3">
        <p>Budget Moyen</p>
        <div class="filter-budget">
            <div class="budget-inputs flex gap-1">
                <div class="col-md-5">
                    <input class="bold-500" type="number" id="budget-min" name="budget-min" placeholder="Min" min="0">
                </div>
                <div class="col-md-5">
                    <input class="bold-500" type="number" id="budget-max" name="budget-max" placeholder="Max" min="0">
                </div>
            </div>
        </div>
    </div>


    <!-- Filtre par Expérience -->
    <div class="col-md-3">
        <div class="filter-experience">
            <p>Experience (Années)</p>
            <div class="experience-inputs flex gap-1">
                <div class="col-md-5">
                    <input class="bold-500" type="number" id="experience-min" name="experience-min" placeholder="Min"
                        min="0">
                </div>
                <div class="col-md-5">
                    <input class="bold-500" type="number" id="experience-max" name="experience-max" placeholder="Max"
                        min="0">
                </div>
            </div>
        </div>
    </div>
</div>