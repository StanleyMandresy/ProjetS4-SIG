<?php
include("../config/config.php");


function getMarcheBy($critere, $valeur, $surfaceMin = null, $surfaceMax = null) {
    global $pdo; // rendre accessible la variable $pdo déclarée dans config.php

    $sql = "SELECT DISTINCT m.id, m.nom, m.surface, m.type_couverture, m.jours_ouverts, m.description, m.photo_url, ST_AsText(m.geom) AS geom
            FROM marche m
            LEFT JOIN marche_type_produit mtp ON m.id = mtp.marche_id
            LEFT JOIN type_produits tp ON tp.id = mtp.type_id
            WHERE 1=1";

    $params = [];

    switch ($critere) {
        case 'nom':
            $sql .= " AND LOWER(m.nom) LIKE LOWER(:valeur)";
            $params[':valeur'] = '%' . $valeur . '%';
            break;
        case 'type_couverture':
            $sql .= " AND LOWER(m.type_couverture) = LOWER(:valeur)";
            $params[':valeur'] = $valeur;
            break;
        case 'jour_ouverture':
            $sql .= " AND LOWER(m.jours_ouverts) LIKE LOWER(:valeur)";
            $params[':valeur'] = '%' . $valeur . '%';
            break;
       case 'produit_vendu':
        if (!is_array($valeur) &&  count($valeur) > 0) {
            $valeur = [$valeur];
        }

        $placeholders = [];
        foreach ($valeur as $i => $libelle) {
            $ph = ":produit$i";
            $placeholders[] = $ph;
            $params[$ph] = $libelle;
        }
        $in = implode(",", $placeholders);
        $sql .= " AND tp.libelle IN ($in)";
    

            break;
    }

    if (!empty($surfaceMin)) {
    $sql .= " AND m.surface >= :surfaceMin";
    $params[':surfaceMin'] = $surfaceMin;
    }

    if (!empty($surfaceMax)) {
        $sql .= " AND m.surface <= :surfaceMax";
        $params[':surfaceMax'] = $surfaceMax;
    }


    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


?>