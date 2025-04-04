<?php
$user_id = get_current_user_id();
$user_type = get_user_meta($user_id, 'user_type', true);

$diplome = get_user_meta($user_id, 'diplome_principal', true);
$experience = get_user_meta($user_id, 'annees_experience', true);
$budget = get_user_meta($user_id, 'budget_moyen_chantiers', true);
$motivation = get_user_meta($user_id, 'motivation_metier', true);

// Récupérer les types d'architectes déjà sélectionnés (stockés sous forme de tableau)
$architect_types = get_user_meta($user_id, 'architecte_type', true);
// Génération d'un nonce pour sécuriser la requête AJAX
$nonce = wp_create_nonce('update_pro_infos_nonce');
?>

<h2 class="color-<?php echo esc_attr($user_type); ?> uppercase text-center">Mes informations professionnelles</h2>
<p class="text-center color-gray-dark">
    Ces informations seront visibles par les professionnels que vous contactez ou qui souhaitent vous contacter.
</p>

<div class="row">
    <div class="col-md-12">
        <form id="pro-info-form" class="flex flex-col gap-2">
            <input type="hidden" name="security" value="<?php echo $nonce; ?>">

            <div class="flex flex-col">
                <label for="diplome_principal">Diplôme principal :</label>
                <input type="text" id="diplome_principal" name="diplome_principal" class="form-control custom-input"
                    value="<?php echo esc_attr($diplome); ?>" required>
            </div>

            <div class="flex flex-col">
                <label for="annees_experience">Années d’expérience :</label>
                <input type="number" id="annees_experience" name="annees_experience" class="form-control custom-input"
                    value="<?php echo esc_attr($experience); ?>" min="0" required>
            </div>

            <div class="flex flex-col">
                <label for="budget_moyen_chantiers">Budget moyen pour mes chantiers (€) :</label>
                <input type="number" id="budget_moyen_chantiers" name="budget_moyen_chantiers"
                    class="form-control custom-input" value="<?php echo esc_attr($budget); ?>" min="0" step="100"
                    required>
            </div>

            <div class="flex flex-col">
                <label for="motivation_metier">Pourquoi je fais ce métier :</label>
                <textarea id="motivation_metier" name="motivation_metier" class="form-control custom-input" rows="5"
                    required><?php echo esc_textarea($motivation); ?></textarea>
            </div>

            <div class="flex flex-col">
                <label>Type d'Architecte :</label>
                <div class="flex gap-2">
                    <?php
                    $types = ['Architecte', 'Architecte intérieur', "Architecte diplômé d'État", 'Architecte paysagiste'];

                    // Si la valeur enregistrée est dans un tableau, on récupère la première (juste au cas où)
                    $normalize = function($str) {
                        $str = trim($str);
                        $str = stripslashes($str);
                        $str = html_entity_decode($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        $str = str_replace(['’', '´'], "'", $str);
                        return $str;
                    };
                    
                    $selected_type = '';
                    if (is_array($architect_types)) {
                        $selected_type = isset($architect_types[0]) ? $architect_types[0] : '';
                    } elseif (is_string($architect_types)) {
                        $selected_type = $architect_types;
                    }
                    


                    foreach ($types as $type) : ?>
                    <label>
                        <input type="radio" name="architecte_type" value="<?php echo esc_attr($type); ?>"
                            <?php checked($normalize($type), $selected_type); ?>>
                        <?php echo esc_html($type); ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-<?php echo $user_type ?>">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</div>