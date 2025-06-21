<?php
// api/getMarcheById.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Inclure la config et les fonctions
require_once('../config/config.php');
require_once('../inc/fonction.php');

try {
    // Récupérer l'ID depuis les paramètres GET
    $id = $_GET['id'] ?? null;
    
    if (!$id || !is_numeric($id)) {
        throw new Exception('ID du marché requis et doit être numérique');
    }
    
    // Récupérer le marché
    $marche = getMarcheById(intval($id));
    
    if (!$marche) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Marché non trouvé'
        ]);
        exit;
    }
    
    // Retourner le résultat JSON
    echo json_encode([
        'status' => 'success',
        'data' => $marche
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>