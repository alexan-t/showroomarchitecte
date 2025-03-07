<?php
global $wpdb;

if (!isset($_GET['lat']) || !isset($_GET['lng'])) {
    echo json_encode([]);
    exit;
}

$latitude = floatval($_GET['lat']);
$longitude = floatval($_GET['lng']);
$max_distance_km = 50; // Rayon de recherche (50 km)

$table_name = $wpdb->prefix . 'userinfos';

$query = $wpdb->prepare("
    SELECT user_id, first_name, last_name, city, 
    (6371 * acos(cos(radians(%f)) * cos(radians(latitude)) * cos(radians(longitude) - radians(%f)) + sin(radians(%f)) * sin(radians(latitude)))) AS distance
    FROM $table_name
    HAVING distance < %d
    ORDER BY distance ASC
    LIMIT 10
", $latitude, $longitude, $latitude, $max_distance_km);

$results = $wpdb->get_results($query);

echo json_encode($results);
exit;