<?php
// api/updateMarche.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Méthode non autorisée. Utilisez POST.'
    ]);
    exit;
}

// Inclure la config et les fonctions
require_once('../config/config.php');
require_once('../inc/fonction.php');

try {
    // Récupérer l'ID du marché
    $id = $_POST['id'] ?? null;
    
    if (!$id || !is_numeric($id)) {
        throw new Exception('ID du marché requis et doit être numérique');
    }
    
    $id = intval($id);
    
    // Vérifier que le marché existe
    $marcheExistant = getMarcheById($id);
    if (!$marcheExistant) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'Marché non trouvé'
        ]);
        exit;
    }
    
    // Récupérer les données du formulaire
    $nom = trim($_POST['nom'] ?? '');
    $surface = !empty($_POST['surface']) ? floatval($_POST['surface']) : null;
    $type_couverture = trim($_POST['type_couverture'] ?? '');
    $jours_ouverts = trim($_POST['jours_ouverts'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $latitude = !empty($_POST['latitude']) ? floatval($_POST['latitude']) : null;
    $longitude = !empty($_POST['longitude']) ? floatval($_POST['longitude']) : null;
    
    // Validation des données obligatoires
    if (empty($nom)) {
        throw new Exception('Le nom du marché est requis');
    }
    
    // Gestion de l'upload de photo
    $photo_url = $marcheExistant['photo_url']; // Garder l'ancienne photo par défaut
    $anciennePhoto = null;
    
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/marches/';
        
        // Créer le dossier s'il n'existe pas
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileInfo = pathinfo($_FILES['photo']['name']);
        $extension = strtolower($fileInfo['extension']);
        
        // Vérifier l'extension
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception('Format de fichier non autorisé. Utilisez : ' . implode(', ', $allowedExtensions));
        }
        
        // Vérifier la taille (max 5MB)
        if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
            throw new Exception('Le fichier est trop volumineux. Taille maximum : 5MB');
        }
        
        // Générer un nom unique
        $fileName = 'marche_' . time() . '_' . uniqid() . '.' . $extension;
        $uploadPath = $uploadDir . $fileName;
        
        // Déplacer le fichier
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
            // Sauvegarder l'ancienne photo pour suppression ultérieure
            $anciennePhoto = $marcheExistant['photo_url'];
            $photo_url = 'uploads/marches/' . $fileName;
        } else {
            throw new Exception('Erreur lors de l\'upload de la photo');
        }
    }
    
    // Mettre à jour le marché
    $success = updateMarche(
        $id,
        $nom,
        $surface,
        $type_couverture ?: null,
        $jours_ouverts ?: null,
        $description ?: null,
        $photo_url,
        $latitude,
        $longitude
    );
    
    if ($success) {
        // Supprimer l'ancienne photo si une nouvelle a été uploadée
        if ($anciennePhoto && file_exists('../' . $anciennePhoto)) {
            unlink('../' . $anciennePhoto);
        }
        
        // Récupérer le marché mis à jour pour le retourner
        $marcheModifie = getMarcheById($id);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Marché mis à jour avec succès',
            'data' => $marcheModifie
        ]);
    } else {
        throw new Exception('Aucune modification effectuée');
    }
    
} catch (Exception $e) {
    // Supprimer la nouvelle photo en cas d'erreur (si elle a été uploadée)
    if (isset($fileName) && file_exists($uploadDir . $fileName)) {
        unlink($uploadDir . $fileName);
    }
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>