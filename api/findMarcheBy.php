<?php
header('Content-Type: application/json');

require_once('../config/config.php');
require_once('../inc/fonction.php');

// Récupération des paramètres GET
$critere = $_GET['critereSpatial'] ?? null;
$valeur = $_GET['valeur'] ?? null;



// Si critère spatial : rayon ou near → attendre des coordonnées
if (in_array($critere, ['rayon', 'near'])) {
    $lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
    $lng = isset($_GET['lng']) ? floatval($_GET['lng']) : null;

    if ($critere === 'rayon') {
        $distance = isset($_GET['distance']) ? floatval($_GET['distance']) : null;
        $valeur = [
        
  'lat' => isset($_GET['lat']) ? floatval($_GET['lat']) : null,
  'lon' => isset($_GET['lng']) ? floatval($_GET['lng']) : null,
  'distance' => isset($_GET['valeur']) ? floatval($_GET['valeur']) : null,
];
        
    } else { // near
        $valeur = [
            'lat' => $lat,
            'lon' => $lng
        ];
    }
}

// Appel de la fonction
if ($critere && $valeur) {
    $marches = findMarcheBy($critere, $valeur);

    echo json_encode([
        'status' => 'success',
        'critere' => $critere,
        'resultat' => $marches
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Critère ou valeur manquant'
    ]);
}
?>