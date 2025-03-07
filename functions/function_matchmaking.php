<?php 

// function get_matching_professionals() {
//     if (!is_user_logged_in()) {
//         wp_send_json_error("Vous devez être connecté pour voir les professionnels.");
//         wp_die();
//     }

//     global $wpdb;
//     $user_id = get_current_user_id();
    
//     // Récupérer le dernier projet de l'utilisateur
//     $table_name = esc_sql($wpdb->prefix . 'projects');
//     $project = $wpdb->get_row(
//         $wpdb->prepare(
//             "SELECT * FROM $table_name WHERE user_id = %d ORDER BY created_at DESC LIMIT 1",
//             $user_id
//         ),
//         ARRAY_A
//     );

//     if (!$project) {
//         wp_send_json_error("Aucun projet trouvé.");
//         wp_die();
//     }

//     // Récupérer uniquement les professionnels
//     $args = [
//         'meta_query' => [
//             [
//                 'key'     => 'user_type',
//                 'value'   => 'particulier',
//                 'compare' => '!=' // Exclure les utilisateurs dont le type est 'particulier'
//             ]
//         ],
//         'number'     => -1, // Récupérer tous les utilisateurs correspondants
//     ];
//     $professionals = get_users($args);

//     // Récupérer les coordonnées du projet
//     $project_lat = floatval($project['latitude'] ?? 0);
//     $project_lon = floatval($project['longitude'] ?? 0);

//     // Initialiser un tableau pour stocker les résultats avec un score de pertinence
//     $results = [];

//     foreach ($professionals as $pro) {
//         $score = 0;
//         $pro_id = $pro->ID;
//         $meta = get_user_meta($pro_id);

//         // Vérifier le type d'architecte
//         $architect_types = get_user_meta($pro_id, 'architecte_type', true);
//         if (!is_array($architect_types)) {
//             $architect_types = json_decode($architect_types, true) ?: [];
//         }

//         if (in_array($project['search'], $architect_types)) {
//             $score += 5;
//         }

//         // Vérifier la distance entre la ville du projet et celle du professionnel
//         $pro_lat = floatval($meta['latitude'][0] ?? 0);
//         $pro_lon = floatval($meta['longitude'][0] ?? 0);
//         $distance = null;

//         if ($project_lat && $project_lon && $pro_lat && $pro_lon) {
//             $distance = haversine_distance($project_lat, $project_lon, $pro_lat, $pro_lon);

//             // Attribuer des points en fonction de la distance
//             if ($distance <= 10) {
//                 $score += 5; // Très proche
//             } elseif ($distance <= 50) {
//                 $score += 3; // Proche
//             } elseif ($distance <= 100) {
//                 $score += 2; // Moyenne distance
//             } else {
//                 $score += 1; // Loin
//             }
//         }

//         // Comparer le budget
//         $budgetPro = floatval($meta['budget_moyen_chantiers'][0] ?? 0);
//         $budgetProjet = floatval($project['budget']);
//         if ($budgetPro >= $budgetProjet) {
//             $score += 3;
//         }

//         // Comparer l'expérience
//         $experiencePro = intval($meta['annees_experience'][0] ?? 0);
//         if ($experiencePro >= 5) {
//             $score += 3;
//         } elseif ($experiencePro >= 2) {
//             $score += 2;
//         } else {
//             $score += 1;
//         }

//         // Ajouter des points bonus selon le type de professionnel
//         $pro_type = get_user_meta($pro_id, 'user_type', true);
//         if ($pro_type === 'pro-premium') {
//             $score += 1; // +1 point pour pro-premium
//         } elseif ($pro_type === 'pro-gold') {
//             $score += 2; // +2 points pour pro-gold
//         }

//         $profile_image = get_user_meta($pro_id, 'profile_image', true);

//         // Ajouter uniquement si un score est attribué
//         if ($score > 0) {
//             $results[] = [
//                 'id' => $pro_id,
//                 'name' => $pro->display_name,
//                 'score' => $score,
//                 'distance' => isset($distance) ? round($distance, 2) . ' km' : 'N/A',
//                 'profile_url' => site_url('/architectes/profil/?id=' . $pro_id),
//                 'city' => $meta['city'][0] ?? '',
//                 'budget_moyen' => $budgetPro,
//                 'experience' => $experiencePro,
//                 'photo' => !empty($profile_image) ? $profile_image : get_avatar_url($pro_id),
//                 'pro_type' => $pro_type,
//             ];
//         }
//     }

//     // Trier les résultats par score décroissant
//     usort($results, function ($a, $b) {
//         return $b['score'] - $a['score'];
//     });

//     wp_send_json_success($results);
// }
// add_action('wp_ajax_get_matching_professionals', 'get_matching_professionals');

function get_matching_professionals() {
    if (!is_user_logged_in()) {
        wp_send_json_error("Vous devez être connecté pour voir les professionnels.");
        wp_die();
    }

    global $wpdb;
    $user_id = get_current_user_id();
    
    // Récupérer le dernier projet de l'utilisateur
    $table_name = esc_sql($wpdb->prefix . 'projects');
    $project = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d ORDER BY created_at DESC LIMIT 1",
            $user_id
        ),
        ARRAY_A
    );

    if (!$project) {
        wp_send_json_error("Aucun projet trouvé.");
        wp_die();
    }

    // Récupérer uniquement les professionnels avec is_page_public = true
    $args = [
        'meta_query' => [
            'relation' => 'AND',
            [
                'key'     => 'user_type',
                'value'   => 'particulier',
                'compare' => '!=' 
            ],
            [
                'key'     => 'is_page_public',
                'value'   => '1',
                'compare' => '='
            ]
        ],
        'number'     => -1, 
    ];
    $professionals = get_users($args);

    // Récupérer les coordonnées du projet
    $project_lat = floatval($project['latitude'] ?? 0);
    $project_lon = floatval($project['longitude'] ?? 0);

    // Initialiser un tableau pour stocker les résultats avec un score de pertinence
    $results = [];
    $premium_professionals = [];

    foreach ($professionals as $pro) {
        $score = 0;
        $pro_id = $pro->ID;
        $meta = get_user_meta($pro_id);

        // Vérifier le type d'architecte
        $architect_types = get_user_meta($pro_id, 'architecte_type', true);
        if (!is_array($architect_types)) {
            $architect_types = json_decode($architect_types, true) ?: [];
        }

        if (in_array($project['search'], $architect_types)) {
            $score += 5;
        }

        // Vérifier la distance entre la ville du projet et celle du professionnel
        $pro_lat = floatval($meta['latitude'][0] ?? 0);
        $pro_lon = floatval($meta['longitude'][0] ?? 0);
        $distance = null;

        if ($project_lat && $project_lon && $pro_lat && $pro_lon) {
            $distance = haversine_distance($project_lat, $project_lon, $pro_lat, $pro_lon);

            // Attribuer des points en fonction de la distance
            if ($distance <= 10) {
                $score += 5;
            } elseif ($distance <= 50) {
                $score += 3;
            } elseif ($distance <= 100) {
                $score += 2;
            } else {
                $score += 1;
            }
        }

        // Comparer le budget
        $budgetPro = floatval($meta['budget_moyen_chantiers'][0] ?? 0);
        $budgetProjet = floatval($project['budget']);
        if ($budgetPro >= $budgetProjet) {
            $score += 3;
        }

        // Comparer l'expérience
        $experiencePro = intval($meta['annees_experience'][0] ?? 0);
        if ($experiencePro >= 5) {
            $score += 3;
        } elseif ($experiencePro >= 2) {
            $score += 2;
        } else {
            $score += 1;
        }

        // Ajouter des points bonus selon le type de professionnel
        $pro_type = get_user_meta($pro_id, 'user_type', true);
        if ($pro_type === 'pro-premium') {
            $score += 1;
            $premium_professionals[] = $pro;
        } elseif ($pro_type === 'pro-gold') {
            $score += 2;
            $premium_professionals[] = $pro;
        }

        $profile_image = get_user_meta($pro_id, 'profile_image', true);

        if ($score > 0) {
            $results[] = [
                'id' => $pro_id,
                'name' => $pro->display_name,
                'score' => $score,
                'distance' => isset($distance) ? round($distance, 2) . ' km' : 'N/A',
                'profile_url' => site_url('/architectes/profil/?id=' . $pro_id),
                'city' => $meta['city'][0] ?? '',
                'budget_moyen' => $budgetPro,
                'experience' => $experiencePro,
                'photo' => !empty($profile_image) ? $profile_image : get_avatar_url($pro_id),
                'pro_type' => $pro_type,
            ];
        }
    }

    // Trier les résultats par score décroissant
    usort($results, fn($a, $b) => $b['score'] - $a['score']);

    // Diviser en sections
    $top_professionals = array_slice($results, 0, 5);
    $potential_interests = array_slice($results, 5, 5);
    $random_premium = array_slice(array_filter($premium_professionals, function($pro) use ($results) {
        return !in_array($pro->ID, array_column($results, 'id'));
    }), 0, 5);

    wp_send_json_success([
        'top_professionals' => $top_professionals,
        'potential_interests' => $potential_interests,
        'random_premium' => array_map(fn($pro) => [
            'id' => $pro->ID,
            'name' => $pro->display_name,
            'profile_url' => site_url('/architectes/profil/?id=' . $pro->ID),
            'city' => get_user_meta($pro->ID, 'city', true) ?? '',
            'photo' => get_user_meta($pro->ID, 'profile_image', true) ?: get_avatar_url($pro->ID),
            'pro_type' => get_user_meta($pro->ID, 'user_type', true)
        ], $random_premium)
    ]);
}
add_action('wp_ajax_get_matching_professionals', 'get_matching_professionals');




// function get_matching_professionals_by_project() {
//     if (!is_user_logged_in()) {
//         wp_send_json_error("Vous devez être connecté pour rechercher des professionnels.");
//         wp_die();
//     }

//     global $wpdb;
//     $user_id = get_current_user_id();
//     $project_id = intval($_POST['project_id'] ?? 0);

//     if (!$project_id) {
//         wp_send_json_error("ID de projet invalide.");
//         wp_die();
//     }

//     // Récupérer le projet sélectionné
//     $table_name = $wpdb->prefix . 'projects';
//     $project = $wpdb->get_row(
//         $wpdb->prepare(
//             "SELECT * FROM $table_name WHERE id = %d AND user_id = %d",
//             $project_id,
//             $user_id
//         ),
//         ARRAY_A
//     );

//     if (!$project) {
//         wp_send_json_error("Projet introuvable.");
//         wp_die();
//     }

//     // Récupérer uniquement les professionnels (exclure les particuliers)
//     $args = [
//         'meta_query' => [
//             [
//                 'key'     => 'user_type',
//                 'value'   => 'particulier',
//                 'compare' => '!=' // Exclure les utilisateurs dont le type est 'particulier'
//             ]
//         ],
//         'number'     => -1, // Récupérer tous les utilisateurs correspondants
//     ];
//     $professionals = get_users($args);

//     // Récupérer les coordonnées du projet
//     $project_lat = floatval($project['latitude'] ?? 0);
//     $project_lon = floatval($project['longitude'] ?? 0);

//     // Initialiser un tableau pour stocker les résultats avec un score de pertinence
//     $results = [];

//     foreach ($professionals as $pro) {
//         $score = 0;
//         $pro_id = $pro->ID;
//         $meta = get_user_meta($pro_id);

//         // Vérifier le type d'architecte
//         $architect_types = get_user_meta($pro_id, 'architecte_type', true);
//         if (!is_array($architect_types)) {
//             $architect_types = json_decode($architect_types, true) ?: [];
//         }

//         if (in_array($project['search'], $architect_types)) {
//             $score += 5;
//         }

//         // Vérifier la distance entre la ville du projet et celle du professionnel
//         $pro_lat = floatval($meta['latitude'][0] ?? 0);
//         $pro_lon = floatval($meta['longitude'][0] ?? 0);

//         if ($project_lat && $project_lon && $pro_lat && $pro_lon) {
//             $distance = haversine_distance($project_lat, $project_lon, $pro_lat, $pro_lon);

//             // Attribuer des points en fonction de la distance
//             if ($distance <= 10) {
//                 $score += 5; // Très proche
//             } elseif ($distance <= 50) {
//                 $score += 3; // Proche
//             } elseif ($distance <= 100) {
//                 $score += 2; // Moyenne distance
//             } else {
//                 $score += 1; // Loin
//             }
//         }

//         // Comparer le budget
//         $budgetPro = floatval($meta['budget_moyen_chantiers'][0] ?? 0);
//         $budgetProjet = floatval($project['budget']);
//         if ($budgetPro >= $budgetProjet) {
//             $score += 3;
//         }

//         // Comparer l'expérience
//         $experiencePro = intval($meta['annees_experience'][0] ?? 0);
//         if ($experiencePro >= 5) {
//             $score += 3;
//         } elseif ($experiencePro >= 2) {
//             $score += 2;
//         } else {
//             $score += 1;
//         }

//         // Ajouter des points bonus selon le type de professionnel
//         $pro_type = get_user_meta($pro_id, 'user_type', true);
//         if ($pro_type === 'pro-premium') {
//             $score += 1; // +1 point pour pro-premium
//         } elseif ($pro_type === 'pro-gold') {
//             $score += 2; // +2 points pour pro-gold
//         }

//         $profile_image = get_user_meta($pro_id, 'profile_image', true);

//         // Ajouter uniquement si un score est attribué
//         if ($score > 0) {
//             $results[] = [
//                 'id' => $pro_id,
//                 'name' => $pro->display_name,
//                 'score' => $score,
//                 'distance' => isset($distance) ? round($distance, 2) . ' km' : 'N/A',
//                 'profile_url' => site_url('/architectes/profil/?id=' . $pro_id),
//                 'city' => $meta['city'][0] ?? '',
//                 'budget_moyen' => $budgetPro,
//                 'experience' => $experiencePro,
//                 'photo' => !empty($profile_image) ? $profile_image : get_avatar_url($pro_id),
//                 'pro_type' => $pro_type,
//             ];
//         }
//     }

//     // Trier les résultats par score décroissant
//     usort($results, function ($a, $b) {
//         return $b['score'] - $a['score'];
//     });

//     wp_send_json_success($results);
// }
// add_action('wp_ajax_get_matching_professionals_by_project', 'get_matching_professionals_by_project');

function get_matching_professionals_by_project() {
    if (!is_user_logged_in()) {
        wp_send_json_error("Vous devez être connecté pour rechercher des professionnels.");
        wp_die();
    }

    global $wpdb;
    $user_id = get_current_user_id();
    $project_id = intval($_POST['project_id'] ?? 0);

    if (!$project_id) {
        wp_send_json_error("ID de projet invalide.");
        wp_die();
    }

    // Récupérer le projet sélectionné
    $table_name = $wpdb->prefix . 'projects';
    $project = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d AND user_id = %d",
            $project_id,
            $user_id
        ),
        ARRAY_A
    );

    if (!$project) {
        wp_send_json_error("Projet introuvable.");
        wp_die();
    }

    // Récupérer uniquement les professionnels (exclure les particuliers)
    $args = [
        'meta_query' => [
            [
                'key'     => 'user_type',
                'value'   => 'particulier',
                'compare' => '!=' 
            ]
        ],
        'number'     => -1, 
    ];
    $professionals = get_users($args);

    // $args = [
    //     'meta_query' => [
    //         'relation' => 'AND', // On s'assure que les deux conditions sont remplies
    //         [
    //             'key'     => 'user_type',
    //             'value'   => 'particulier',
    //             'compare' => '!=' 
    //         ],
    //         [
    //             'key'     => 'is_page_public',
    //             'value'   => '1', // WordPress stocke les booléens en tant que chaînes '1' pour true
    //             'compare' => '='
    //         ]
    //     ],
    //     'number' => -1, // Récupérer tous les professionnels correspondants
    // ];
    
    $professionals = get_users($args);
    

    // Initialiser un tableau pour stocker les résultats
    $results = [];
    $premium_professionals = [];

    foreach ($professionals as $pro) {
        $score = 0;
        $pro_id = $pro->ID;
        $meta = get_user_meta($pro_id);

        // Vérification du type de professionnel
        $pro_type = get_user_meta($pro_id, 'user_type', true);
        if ($pro_type === 'pro-premium' || $pro_type === 'pro-gold') {
            $premium_professionals[] = $pro;
        }

        // Calcul du score selon les critères
        if (!empty($project['search']) && in_array($project['search'], json_decode($meta['architecte_type'][0] ?? '[]', true))) {
            $score += 5;
        }
        
        if (!empty($meta['latitude'][0]) && !empty($meta['longitude'][0])) {
            $distance = haversine_distance(
                floatval($project['latitude']), floatval($project['longitude']),
                floatval($meta['latitude'][0]), floatval($meta['longitude'][0])
            );
            $score += ($distance <= 10) ? 5 : (($distance <= 50) ? 3 : (($distance <= 100) ? 2 : 1));
        }

        if (floatval($meta['budget_moyen_chantiers'][0] ?? 0) >= floatval($project['budget'])) {
            $score += 3;
        }

        if (intval($meta['annees_experience'][0] ?? 0) >= 5) {
            $score += 3;
        }

        if ($pro_type === 'pro-premium') {
            $score += 1;
        } elseif ($pro_type === 'pro-gold') {
            $score += 2;
        }

        $pro_id = $pro->ID;

        // Générer le bouton en utilisant le shortcode
        $contact_button = do_shortcode('[prendre_contact_button user_id="' . $pro_id . '"]');

        if ($score > 0) {
            $results[] = [
                'id' => $pro_id,
                'name' => $pro->display_name,
                'score' => $score,
                'profile_url' => site_url('/architectes/profil/?id=' . $pro_id),
                'city' => $meta['city'][0] ?? '',
                'photo' => get_user_meta($pro_id, 'profile_image', true) ?: get_avatar_url($pro_id),
                'pro_type' => $pro_type,
                'contact_button' => $contact_button
            ];
        }
    }

    // Trier les professionnels par score décroissant
    usort($results, fn($a, $b) => $b['score'] - $a['score']);

    // Diviser les résultats en trois sections
    $top_professionals = array_slice($results, 0, 5);
    $potential_interests = array_slice($results, 5, 5);
    $random_premium = array_slice(array_filter($premium_professionals, function($pro) use ($results) {
        return !in_array($pro->ID, array_column($results, 'id'));
    }), 0, 5);


    wp_send_json_success([
        'top_professionals' => $top_professionals,
        'potential_interests' => $potential_interests,
        'random_premium' => array_map(fn($pro) => [
            'id' => $pro->ID,
            'name' => $pro->display_name,
            'profile_url' => site_url('/architectes/profil/?id=' . $pro->ID),
            'city' => get_user_meta($pro->ID, 'city', true) ?? '',
            'photo' => get_user_meta($pro->ID, 'profile_image', true) ?: get_avatar_url($pro->ID),
            'pro_type' => get_user_meta($pro->ID, 'user_type', true)
        ], $random_premium)
    ]);
}
add_action('wp_ajax_get_matching_professionals_by_project', 'get_matching_professionals_by_project');



/**
 * Calcul de la distance entre deux points avec la formule de Haversine
 */
function haversine_distance($lat1, $lon1, $lat2, $lon2) {
    $earth_radius = 6371; // Rayon de la Terre en km

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earth_radius * $c; // Distance en km
}

function update_existing_users_with_coordinates() {
    global $wpdb;

    // Récupérer tous les utilisateurs professionnels qui n'ont pas encore de latitude/longitude
    $args = [
        'meta_query' => [
            'relation' => 'AND',
            [
                'key'     => 'user_type',
                'value'   => 'particulier',
                'compare' => '!=' // Exclure les utilisateurs 'particulier'
            ],
            [
                'key'     => 'latitude',
                'compare' => 'NOT EXISTS' // Vérifier si la latitude n'existe pas encore
            ],
            [
                'key'     => 'longitude',
                'compare' => 'NOT EXISTS' // Vérifier si la longitude n'existe pas encore
            ]
        ],
        'number' => -1 // Récupérer tous les utilisateurs correspondants
    ];
    
    $users = get_users($args);

    if (empty($users)) {
        error_log("Tous les utilisateurs ont déjà des coordonnées GPS.");
        return;
    }

    foreach ($users as $user) {
        $user_id = $user->ID;
        $city = get_user_meta($user_id, 'city', true);

        if (!empty($city)) {
            $city_encoded = urlencode($city);
            $api_url = "https://nominatim.openstreetmap.org/search?q={$city_encoded}&format=json&limit=1";

            $response = wp_remote_get($api_url, ['timeout' => 10]);

            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);

                if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                    $latitude = floatval($data[0]['lat']);
                    $longitude = floatval($data[0]['lon']);

                    // Mettre à jour les métadonnées de l'utilisateur
                    update_user_meta($user_id, 'latitude', $latitude);
                    update_user_meta($user_id, 'longitude', $longitude);

                    error_log("Coordonnées mises à jour pour l'utilisateur {$user_id} - {$city} : {$latitude}, {$longitude}");
                } else {
                    error_log("Échec de récupération des coordonnées pour {$city} (Utilisateur ID: {$user_id})");
                }
            } else {
                error_log("Erreur d'accès à l'API pour {$city} (Utilisateur ID: {$user_id})");
            }
        } else {
            error_log("Aucune ville renseignée pour l'utilisateur ID: {$user_id}");
        }
    }
}
// Exécuter la mise à jour une seule fois
// update_existing_users_with_coordinates();