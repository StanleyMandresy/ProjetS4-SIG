<?php
header('Content-Type: application/json');

// Inclure la config et les fonctions
require_once('../config/config.php');
require_once('../inc/fonction.php');


// Récupérer les paramètres (GET ou POST)
$critere = $_GET['critere'] ?? null;
$valeur = $_GET['valeur'] ?? null;



$surfaceMin = isset($_GET['surfaceMin']) ? floatval($_GET['surfaceMin']) : null;
$surfaceMax = isset($_GET['surfaceMax']) ? floatval($_GET['surfaceMax']) : null;

// Pour 'produit_vendu', attendre un tableau (JSON string)
if ($critere === 'produit_vendu') {
    if (isset($_GET['valeur'])) {
        // Si c'est déjà un tableau (cas valeur[]=x&valeur[]=y)
        if (is_array($_GET['valeur'])) {
            $valeur = $_GET['valeur'];
        } 
        // Sinon si c'est une string JSON, on décode
        elseif (is_string($_GET['valeur'])) {
            $decoded = json_decode($_GET['valeur'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $valeur = $decoded;
            } else {
                $valeur = [$_GET['valeur']];
            }
        } else {
            $valeur = [];
        }
    } else {
        $valeur = [];
    }
}
// Appeler la fonction
$marches = getMarcheBy($critere, $valeur, $surfaceMin, $surfaceMax);

// Retourner les résultats JSON
echo json_encode([
    'status' => 'success',
    'data' => $marches
]);
