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
-- Antananarivo
('Marché Analakely', 1500.00, 'couvert', 'lundi-mardi-mercredi-jeudi-vendredi-samedi', 'Marché emblématique du centre-ville.', NULL, ST_SetSRID(ST_MakePoint(47.5162, -18.9106), 4326)),

-- Toamasina
('Marché Bazary Be', 2000.00, 'plein air', 'tous les jours', 'Marché populaire et très fréquenté.', NULL, ST_SetSRID(ST_MakePoint(49.4042, -18.1492), 4326)),

-- Fianarantsoa
('Marché Isaka', 950.00, 'couvert', 'mardi-jeudi-samedi', 'Marché typique de la région Betsileo.', NULL, ST_SetSRID(ST_MakePoint(47.0834, -21.4536), 4326)),

-- Mahajanga
('Marché Mangarivotra', 1200.00, 'plein air', 'tous les jours', 'Marché proche du bord de mer.', NULL, ST_SetSRID(ST_MakePoint(46.3167, -15.7167), 4326)),

-- Toliara
('Marché Bazar Be Toliara', 1100.00, 'couvert', 'lundi-mercredi-vendredi', 'Marché central de Toliara.', NULL, ST_SetSRID(ST_MakePoint(43.6847, -23.3505), 4326)),

-- Antsirabe
('Marché Sabotsy', 1400.00, 'couvert', 'samedi', 'Marché du samedi très animé.', NULL, ST_SetSRID(ST_MakePoint(47.0333, -19.8667), 4326)),

-- Diego-Suarez
('Marché Tanambao', 1000.00, 'plein air', 'mardi-jeudi-samedi', 'Marché en périphérie urbaine.', NULL, ST_SetSRID(ST_MakePoint(49.2917, -12.2781), 4326));





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
('Epices');



-- === 4. Table de relation N-N entre marché et types de produits ===
CREATE TABLE marche_type_produit (
    id SERIAL PRIMARY KEY,
    marche_id INT REFERENCES marche(id) ON DELETE CASCADE,
    type_id INT REFERENCES type_produits(id) ON DELETE CASCADE
);

-- Le marché 1 (Analakely) vend Légumes, Fruits, Viande
INSERT INTO marche_type_produit (marche_id, type_id) VALUES
(6, 1), -- Légumes
(6, 4), -- Fruits
(6, 3); -- Viande

-- Le marché 2 (Mahamasina) vend Poisson, Épices, Volaille
INSERT INTO marche_type_produit (marche_id, type_id) VALUES
(2, 4), -- Poisson
(2, 6),
(2, 5); -- Volaille

-- Le marché 3 (Toamasina) vend Artisanat local, Chaussures, Bijoux
 -- Bijoux

INSERT INTO marche_type_produit (marche_id, type_id) VALUES
(7, 1); -- Artisanat local



-- === 5. Index spatial pour la recherche rapide ===
CREATE INDEX marche_geom_idx ON marche USING GIST (geom);

--== 6. importer les fichiers shapefile dans shapefile-import
