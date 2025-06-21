<?php
// api/getAllMarches.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Inclure la config et les fonctions
require_once('../config/config.php');
require_once('../inc/fonction.php');

try {
    // Récupérer tous les marchés
    $marches = getAllMarches();
    
    // Retourner les résultats JSON
    echo json_encode([
        'status' => 'success',
        'data' => $marches,
        'total' => count($marches)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>