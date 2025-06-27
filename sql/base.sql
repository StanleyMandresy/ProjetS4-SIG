-- === 1. Extension PostGIS ===
CREATE EXTENSION IF NOT EXISTS postgis;
CREATE EXTENSION postgis_topology;

-- === 2. Table des marchés ===
CREATE TABLE marche (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    surface NUMERIC(10,2),
    type_couverture VARCHAR(50),     -- Ex: 'plein air', 'couvert'
    jours_ouverts VARCHAR(50),       -- Ex: 'lundi-mercredi-vendredi'
    description TEXT,
    photo_url TEXT,
    geom GEOMETRY(Point, 4326)       -- Coordonnées du marché
);

INSERT INTO marche (nom, surface, type_couverture, jours_ouverts, description, photo_url, geom) VALUES


-- Mahajanga
('Marché Mangarivotra', 1200.00, 'plein air', 'tous les jours', 'Marché proche du bord de mer.','/uploads/marches/mangarivotra.jfif', ST_SetSRID(ST_MakePoint(46.3167, -15.7167), 4326)),


-- Diego-Suarez
('Marché Tanambao', 1000.00, 'plein air', 'mardi-jeudi-samedi', 'Marché en périphérie urbaine.', '/uploads/marches/tanambao.png', ST_SetSRID(ST_MakePoint(49.2917, -12.2781), 4326)),

-- Antananarivo
('Marché Analakely', 1500.00, 'couvert', 'lundi-mardi-mercredi-jeudi-vendredi-samedi', 'Marché emblématique du centre-ville.', '/uploads/marches/Analakely.jfif', ST_SetSRID(ST_MakePoint(47.5162, -18.9106), 4326)),
('Marché Sabotsy Namehana', 800.00, 'mixte', 'mardi-jeudi-samedi', 'Marché hebdomadaire de Namehana.', '/uploads/marches/sabotsy namehana.jfif', ST_SetSRID(ST_MakePoint(47.5534, -18.8765), 4326)),
('Marché Sabotsy Antsirabe', 1200.00, 'ouvert', 'mercredi-vendredi-dimanche', 'Grand marché spécialisé en produits locaux.', '/uploads/marches/sabotsy antsirabe.jfif', ST_SetSRID(ST_MakePoint(47.0326, -19.8659), 4326)),

-- Toamasina
('Marché Bazar Be Toamasina', 2000.00, 'couvert', 'lundi-mardi-mercredi-jeudi-vendredi-samedi', 'Principal marché de Toamasina près du port.', '/uploads/marches/BazarBeToamasina.jfif', ST_SetSRID(ST_MakePoint(49.3958, -18.1554), 4326)),
('Marché Pochard', 750.00, 'ouvert', 'tous les jours', 'Marché artisanal.', '/uploads/marches/pochard.jfif', ST_SetSRID(ST_MakePoint( 47.522249079034836,-18.901760510743607), 4326)),

-- Toliara
('Marché Bazar Be Toliara', 1800.00, 'mixte', 'lundi-mercredi-vendredi-dimanche', 'Marché coloré de la capitale du Sud.', '/uploads/marches/BazarBeToliara.jpg', ST_SetSRID(ST_MakePoint(43.6855, -23.3546), 4326)),

-- Fianarantsoa
('Marché Ambalavao', 950.00, 'ouvert', 'mardi-jeudi-samedi', 'Connu pour les boeufs.', '/uploads/marches/ambalavao.jpg', ST_SetSRID(ST_MakePoint(46.9333, -21.8333), 4326)),
('Marché Ankihiry', 600.00, 'couvert', 'lundi-mercredi-vendredi', 'Marché spécialisé en produits agricoles.', '/uploads/marches/ankihiriry.jpg', ST_SetSRID(ST_MakePoint(47.0833, -21.9167), 4326)),

-- Autres
('Marché de la Digue', 1100.00, 'ouvert', 'tous les jours', 'Marché artisanal.', '/uploads/marches/digue.jpg', ST_SetSRID(ST_MakePoint(47.4790523736544,-18.876757006250603), 4326)),
('Marché aux Fleurs Ivandry', 500.00, 'couvert', 'vendredi-samedi-dimanche', 'Spécialisé en plantes et fleurs locales.', '/uploads/marches/flowermarket ivandry.jpg', ST_SetSRID(ST_MakePoint(47.5333, -18.9167), 4326)),
('Marché aux Fleurs Anosy', 700.00, 'mixte', 'jeudi-vendredi-samedi', 'Coloré marché floral près du lac Anosy.', '/uploads/marches/marche fleur anosy.jpeg', ST_SetSRID(ST_MakePoint(47.5219, -18.9178), 4326)),
('Marché Isaka', 850.00, 'ouvert', 'lundi-mercredi-vendredi', 'Marché rural typique.', '/uploads/marches/isaka.jfif', ST_SetSRID(ST_MakePoint(47.6667, -19.3333), 4326)),
('Marché Coum', 650.00, 'couvert', 'mardi-jeudi-samedi', 'Petit marché communautaire.', '/uploads/marches/coum.jfif', ST_SetSRID(ST_MakePoint(47.7833, -20.2500), 4326));



-- === 3. Table des types de produits ===
CREATE TABLE type_produits (
    id SERIAL PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL
);
INSERT INTO type_produits (libelle) VALUES
('Legumes'),
('Fruits'),
('Viande'),
('Poisson'),
('Volaille'),
('Artisanat'),
('Fleur'),
('Boeuf');



-- === 4. Table de relation N-N entre marché et types de produits ===
CREATE TABLE marche_type_produit (
    id SERIAL PRIMARY KEY,
    marche_id INT REFERENCES marche(id) ON DELETE CASCADE,
    type_id INT REFERENCES type_produits(id) ON DELETE CASCADE
);




INSERT INTO marche_type_produit (marche_id, type_id) VALUES
-- Marché Mangarivotra (1)
(1, 2), (1, 3), (1, 4),
-- Marché Tanambao (2)
(2, 1), (2, 2), (2, 3),
-- Marché Analakely (3)
(3, 1), (3, 2), (3, 3), (3, 4), (3, 5), (3, 6),
-- Marché Sabotsy Namehana (4)
(4, 1), (4, 2), (4, 3),
-- Marché Sabotsy Antsirabe (5)
(5, 1), (5, 2), (5, 8),
-- Marché Bazar Be Toamasina (6)
(6, 1), (6, 2), (6, 3), (6, 4), (6, 5), (6, 6),
-- Marché Pochard (7)
(7, 6),
-- Marché Bazar Be Toliara (8)
(8, 1), (8, 2), (8, 3), (8, 4), (8, 6),
-- Marché Ambalavao (9)
(9, 8),
-- Marché Ankihiry (10)
(10, 1), (10, 2),
-- Marché de la Digue (11)
(11, 6),
-- Marché aux Fleurs Ivandry (12)
(12, 7),
-- Marché aux Fleurs Anosy (13)
(13, 7),
-- Marché Isaka (14)
(14, 1), (14, 2), (14, 3),
-- Marché Coum (15)
(15, 6);





-- === 5. Index spatial pour la recherche rapide ===
CREATE INDEX marche_geom_idx ON marche USING GIST (geom);

--== 6. importer les fichiers shapefile dans shapefile-import
CREATE TABLE admin (
    id SERIAL PRIMARY KEY,
    pseudo VARCHAR(100) NOT NULL,
    motdepasse VARCHAR(255) NOT NULL
);

INSERT INTO admin (pseudo,motdepasse) VALUES
('admin','admin');
