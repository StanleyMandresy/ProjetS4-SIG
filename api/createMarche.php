<?php
// api/createMarche.php

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
    $photo_url = null;
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
            $photo_url = 'uploads/marches/' . $fileName;
        } else {
            throw new Exception('Erreur lors de l\'upload de la photo');
        }
    }
    
    // Créer le marché
    $marcheId = createMarche(
        $nom,
        $surface,
        $type_couverture ?: null,
        $jours_ouverts ?: null,
        $description ?: null,
        $photo_url,
        $latitude,
        $longitude
    );
    
    if ($marcheId) {
        // Récupérer le marché créé pour le retourner
        $nouveauMarche = getMarcheById($marcheId);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Marché créé avec succès',
            'data' => $nouveauMarche,
            'id' => $marcheId
        ]);
    } else {
        throw new Exception('Erreur lors de la création du marché');
    }
    
} catch (Exception $e) {
    // Supprimer le fichier uploadé en cas d'erreur
    if (isset($photo_url) && $photo_url && file_exists('../' . $photo_url)) {
        unlink('../' . $photo_url);
    }
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>