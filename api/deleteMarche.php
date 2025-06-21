<?php
// api/deleteMarche.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gérer les requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Vérifier que la méthode est POST ou DELETE
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Méthode non autorisée. Utilisez POST ou DELETE.'
    ]);
    exit;
}

// Inclure la config et les fonctions
require_once('../config/config.php');
require_once('../inc/fonction.php');

try {
    // Récupérer l'ID à supprimer
    $input = json_decode(file_get_contents('php://input'), true);
    $id = null;
    
    // Vérifier différentes sources pour l'ID
    if (isset($input['id'])) {
        $id = $input['id'];
    } elseif (isset($_POST['id'])) {
        $id = $_POST['id'];
    } elseif (isset($_GET['id'])) {
        $id = $_GET['id'];
    }
    
    // Validation de l'ID
    if (empty($id) || !is_numeric($id)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'ID du marché requis et doit être un nombre valide.'
        ]);
        exit;
    }
    
    // Vérifier si le marché existe avant suppression
    $sql_check = "SELECT id, nom, photo_url FROM marche WHERE id = :id";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt_check->execute();
    $marche = $stmt_check->fetch();
    
    if (!$marche) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Marché non trouvé.'
        ]);
        exit;
    }
    
    // Supprimer la photo s'il y en a une
    if (!empty($marche['photo_url'])) {
        $photo_path = '../' . $marche['photo_url'];
        if (file_exists($photo_path)) {
            unlink($photo_path);
        }
    }
    
    // Supprimer le marché
    $result = deleteMarche($id);
    
    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Marché "' . $marche['nom'] . '" supprimé avec succès.',
            'deleted_id' => $id
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Aucun marché supprimé. Vérifiez l\'ID.'
        ]);
    }
    
} catch (Exception $e) {
    error_log("Erreur suppression marché : " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur lors de la suppression : ' . $e->getMessage()
    ]);
}
?>