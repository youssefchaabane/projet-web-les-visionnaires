-- ====================================================
-- Gestion du Stock - Schéma Complet
-- Base de données: gestion_stock
-- ====================================================

-- Créer la base de données
CREATE DATABASE IF NOT EXISTS gestion_stock 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Sélectionner la base
USE gestion_stock;

-- ====================================================
-- Table 1: categorie
-- ====================================================
CREATE TABLE IF NOT EXISTS categorie (
    id_cat INT AUTO_INCREMENT PRIMARY KEY,
    nom_cat VARCHAR(100) NOT NULL UNIQUE,
    description_cat TEXT,
    lieu_stockage VARCHAR(100),
    temp_conseille VARCHAR(50),
    delai_alerte_jours INT DEFAULT 30,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nom_cat (nom_cat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================
-- Table 2: produit
-- ====================================================
CREATE TABLE IF NOT EXISTS produit (
    id_prod INT AUTO_INCREMENT PRIMARY KEY,
    nom_prod VARCHAR(100) NOT NULL,
    date_expiration DATE,
    poids_produit DECIMAL(10, 3),
    quantite_dispo INT NOT NULL DEFAULT 0,
    id_cat INT NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cat) REFERENCES categorie(id_cat) ON DELETE RESTRICT,
    INDEX idx_nom_prod (nom_prod),
    INDEX idx_id_cat (id_cat),
    INDEX idx_quantite_dispo (quantite_dispo),
    INDEX idx_date_expiration (date_expiration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================
-- Données Test: Categories (5 items)
-- ====================================================
INSERT INTO categorie (nom_cat, description_cat, lieu_stockage, temp_conseille, delai_alerte_jours) VALUES
('Électronique', 'Appareils électriques et électroniques', 'Armoire A1', '15-25°C', 60),
('Fournitures de Bureau', 'Stylos, cahiers, classeurs, etc.', 'Étagère B2', '10-30°C', 90),
('Alimentation', 'Produits alimentaires et boissons', 'Réfrigérateur R1', '2-8°C', 7),
('Textile', 'Vêtements et tissus', 'Placard T3', '15-25°C', 180),
('Matériel de Nettoyage', 'Produits de nettoyage et désinfectants', 'Stockage N1', '5-25°C', 60);

-- ====================================================
-- Données Test: Produits (20 items)
-- ====================================================
INSERT INTO produit (nom_prod, date_expiration, poids_produit, quantite_dispo, id_cat) VALUES
('Souris sans fil', NULL, 0.150, 45, 1),
('Clavier AZERTY', NULL, 0.800, 32, 1),
('Écran 27 pouces', NULL, 5.500, 12, 1),
('Webcam 1080p', NULL, 0.300, 28, 1),
('Stylos Bic (boite 50)', NULL, 0.450, 150, 2),
('Cahiers A4', NULL, 0.500, 200, 2),
('Classeurs 4 anneaux', NULL, 0.300, 85, 2),
('Post-it 100 feuilles', NULL, 0.080, 120, 2),
('Café moulu 250g', '2027-04-18', 0.250, 95, 3),
('Thé vert 20 sachets', '2026-12-30', 0.050, 60, 3),
('Huile d\'olive 500ml', '2027-08-15', 0.500, 40, 3),
('Sucre en poudre 1kg', NULL, 1.000, 75, 3),
('T-shirt blanc XL', NULL, 0.200, 25, 4),
('Chemise bleu marine M', NULL, 0.250, 15, 4),
('Jeans noir 32', NULL, 0.600, 18, 4),
('Serviettes de bain', NULL, 0.450, 50, 4),
('Détergent linge 1L', '2026-09-20', 1.100, 100, 5),
('Désinfectant multi-surfaces', '2026-10-10', 0.550, 70, 5),
('Éponges grattantes', '2026-11-30', 0.100, 110, 5),
('Gants de ménage', NULL, 0.050, 80, 5);

-- ====================================================
-- Contraintes et Indexes supplémentaires
-- ====================================================

-- Aucun index supplémentaire nécessaire (déjà définis dans CREATE TABLE)
