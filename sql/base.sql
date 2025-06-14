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

-- === 3. Table des types de produits ===
CREATE TABLE type_produits (
    id SERIAL PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL
);
INSERT INTO type_produits (libelle) VALUES
('Légumes'),
('Fruits'),
('Viande'),
('Poisson'),
('Volaille'),
('Épices'),
('Produits laitiers'),
('Pâtisseries'),
('Vêtements'),
('Chaussures'),
('Articles de ménage'),
('Artisanat local'),
('Plantes et fleurs'),
('Électronique'),
('Téléphones'),
('Accessoires'),
('Bijoux'),
('Produits cosmétiques'),
('Outils'),
('Matériaux de construction');


-- === 4. Table de relation N-N entre marché et types de produits ===
CREATE TABLE marche_type_produit (
    id SERIAL PRIMARY KEY,
    marche_id INT REFERENCES marche(id) ON DELETE CASCADE,
    type_id INT REFERENCES type_produits(id) ON DELETE CASCADE
);

-- === 5. Index spatial pour la recherche rapide ===
CREATE INDEX marche_geom_idx ON marche USING GIST (geom);

--== 6. importer les fichiers shapefile dans shapefile-import
