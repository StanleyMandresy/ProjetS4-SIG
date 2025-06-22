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
function findMarcheBy($critere, $valeur) {
    global $pdo;

    $params = [':valeur' => $valeur];

    switch ($critere) {
           case 'province':
            $sql = "
                SELECT m.*,ST_AsText(m.geom) AS geom
                FROM marche m
                JOIN province p ON ST_Contains(p.geom, m.geom)
                WHERE LOWER(p.des_provin) = LOWER(:valeur)
            ";
            break;

        case 'region':
            $sql = "
                SELECT m.*,ST_AsText(m.geom) AS geom
                FROM marche m
                JOIN region r ON ST_Contains(r.geom, m.geom)
                WHERE LOWER(r.des_region) = LOWER(:valeur)
            ";
            break;

        case 'district':
            $sql = "
                SELECT m.*,ST_AsText(m.geom) AS geom
                FROM marche m
                JOIN district d ON ST_Contains(d.geom, m.geom)
                WHERE LOWER(d.des_fiv) = LOWER(:valeur)
            ";
            break;

        case 'commune':
            $sql = "
                SELECT m.*,ST_AsText(m.geom) AS geom
                FROM marche m
                JOIN communes c ON ST_Contains(c.geom, m.geom)
                WHERE LOWER(c.des_commun) = LOWER(:valeur)
            ";
            break;

        case 'rayon':
         
            if (is_array($valeur) && isset($valeur['lat'], $valeur['lon'], $valeur['distance'])) {
                $sql = "
                    SELECT m.*, ST_AsText(m.geom) AS geom, ST_DistanceSphere(m.geom, ST_MakePoint(:lon, :lat)) AS distance
                    FROM marche m
                    WHERE ST_DistanceSphere(m.geom, ST_MakePoint(:lon, :lat)) <= :distance
                    ORDER BY distance ASC
                ";
                 $params = [
                    ':lat' => $valeur['lat'],
                    ':lon' => $valeur['lon'],
                    ':distance' => $valeur['distance'] * 1000
                ];
         

            } else return [];
      
            break;

        case 'near':
            if (is_array($valeur) && isset($valeur['lat'], $valeur['lon'])) {
               $sql = "
                        SELECT m.*, ST_AsText(m.geom) AS geom, ST_DistanceSphere(m.geom, ST_MakePoint(:lon, :lat)) as distance
                        FROM marche m
                        ORDER BY distance ASC
                        LIMIT 1
                    ";

                $params = [
                    ':lat' => $valeur['lat'],
                    ':lon' => $valeur['lon']
                ];
            } else return [];

            break;

        default:
            return [];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function createMarche($nom, $surface = null, $type_couverture = null, $jours_ouverts = null, $description = null, $photo_url = null, $latitude = null, $longitude = null) {
    global $pdo;
    
    try {
        // Définir la requête SQL selon la présence des coordonnées
        if ($latitude !== null && $longitude !== null) {
            $sql = "INSERT INTO marche (nom, surface, type_couverture, jours_ouverts, description, photo_url, geom) 
                    VALUES (:nom, :surface, :type_couverture, :jours_ouverts, :description, :photo_url, 
                    ST_SetSRID(ST_MakePoint(:longitude, :latitude), 4326))";
        } else {
            $sql = "INSERT INTO marche (nom, surface, type_couverture, jours_ouverts, description, photo_url, geom) 
                    VALUES (:nom, :surface, :type_couverture, :jours_ouverts, :description, :photo_url, NULL)";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':surface', $surface);
        $stmt->bindParam(':type_couverture', $type_couverture);
        $stmt->bindParam(':jours_ouverts', $jours_ouverts);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':photo_url', $photo_url);
        
        // Lier les paramètres de géolocalisation seulement s'ils sont présents
        if ($latitude !== null && $longitude !== null) {
            $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR);
            $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        throw new Exception("Erreur lors de la création du marché : " . $e->getMessage());
    }
}


/*
    * READ - Récupérer tous les marchés
*/


function getAllMarches() {
    global $pdo;
    
    try {
        $sql = "SELECT id, nom, surface, type_couverture, jours_ouverts, description, photo_url,
                    ST_X(geom) as longitude, ST_Y(geom) as latitude
                    FROM marche 
                ORDER BY nom";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw new Exception("Erreur lors de la récupération des marchés : " . $e->getMessage());
    }
}


/*
    * UPDATE - Mettre à jour un marché
*/


function updateMarche($id, $nom, $surface = null, $type_couverture = null, $jours_ouverts = null, $description = null, $photo_url = null, $latitude = null, $longitude = null) {
    global $pdo;
    
    try {
        // Utiliser deux requêtes différentes selon la présence des coordonnées
        if ($latitude !== null && $longitude !== null) {
            $sql = "UPDATE marche 
                    SET nom = :nom, 
                        surface = :surface, 
                        type_couverture = :type_couverture, 
                        jours_ouverts = :jours_ouverts, 
                        description = :description, 
                        photo_url = :photo_url,
                        geom = ST_SetSRID(ST_MakePoint(:longitude, :latitude), 4326)
                    WHERE id = :id";
        } else {
            $sql = "UPDATE marche 
                    SET nom = :nom, 
                        surface = :surface, 
                        type_couverture = :type_couverture, 
                        jours_ouverts = :jours_ouverts, 
                        description = :description, 
                        photo_url = :photo_url
                    WHERE id = :id";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':surface', $surface);
        $stmt->bindParam(':type_couverture', $type_couverture);
        $stmt->bindParam(':jours_ouverts', $jours_ouverts);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':photo_url', $photo_url);
        
        // Lier les paramètres de géolocalisation seulement s'ils sont présents
        if ($latitude !== null && $longitude !== null) {
            $stmt->bindParam(':latitude', $latitude, PDO::PARAM_STR);
            $stmt->bindParam(':longitude', $longitude, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        throw new Exception("Erreur lors de la mise à jour du marché : " . $e->getMessage());
    }
}

/*
    * DELETE - Supprimer un marché
*/


function deleteMarche($id) {
    global $pdo;
    
    try {
        $sql = "DELETE FROM marche WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        throw new Exception("Erreur lors de la suppression du marché : " . $e->getMessage());
    }
}


/*
    * READ - Récupérer un marché par son ID
*/


function getMarcheById($id) {
    global $pdo;
    
    try {
        $sql = "SELECT id, nom, surface, type_couverture, jours_ouverts, description, photo_url,
                    ST_X(geom) as longitude, ST_Y(geom) as latitude
                    FROM marche 
                WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw new Exception("Erreur lors de la récupération du marché : " . $e->getMessage());
    }


}
function getZones($type) {
    global $pdo;

    // Correspondance entre le type demandé et la table + champ de description
    $tables = [
        'province' => ['table' => 'province', 'champ' => 'des_provin'],
        'region'   => ['table' => 'region', 'champ' => 'des_region'],
        'district' => ['table' => 'district', 'champ' => 'des_fiv'],
        'commune'  => ['table' => 'communes', 'champ' => 'des_commun']
    ];

    if (!isset($tables[$type])) {
        return [];
    }

    $table = $tables[$type]['table'];
    $champ = $tables[$type]['champ'];

    $sql = "SELECT DISTINCT $champ AS nom FROM $table ORDER BY $champ";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}


?>