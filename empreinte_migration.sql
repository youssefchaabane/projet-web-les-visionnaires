-- =====================================================
-- Intégration gestion-fact-emis dans gestion_allergies
-- Tables: recette, facteur_emission, analyse_carbone
-- =====================================================

USE `projet-web`;

SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE IF NOT EXISTS recette (
    id_recette INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS facteur_emission (
    id_facteur INT AUTO_INCREMENT PRIMARY KEY,
    categorie_aliment VARCHAR(100) NOT NULL,
    co2_par_kg DECIMAL(10,2) NOT NULL,
    source_donnee VARCHAR(255),
    date_derniere_maj DATE,
    INDEX idx_categorie (categorie_aliment)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS analyse_carbone (
    id_analyse INT AUTO_INCREMENT PRIMARY KEY,
    score_co2_total DECIMAL(10,2) NOT NULL,
    niveau_impact ENUM('bas', 'moyen', 'eleve') NOT NULL,
    date_calcul DATE,
    methode_calcul VARCHAR(100),
    id_recette INT NOT NULL,
    FOREIGN KEY (id_recette) REFERENCES recette(id_recette) ON DELETE CASCADE,
    INDEX idx_impact (niveau_impact),
    INDEX idx_recette (id_recette)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;

-- Données de base
INSERT IGNORE INTO recette (id_recette, nom, description) VALUES
(1, 'Salade de Lentilles', 'Salade riche en protéines végétales et faible impact carbone'),
(2, 'Burger de Bœuf', 'Burger classique avec frites'),
(3, 'Pasta Pesto', 'Pâtes au basilic frais et pignons'),
(4, 'Poulet Rôti', 'Poulet fermier aux herbes de provence');

INSERT IGNORE INTO facteur_emission (categorie_aliment, co2_par_kg, source_donnee, date_derniere_maj) VALUES
('Bœuf', 27.0, 'ADEME', '2024-01-15'),
('Agneau', 24.0, 'ADEME', '2024-01-15'),
('Porc', 7.6, 'ADEME', '2024-01-15'),
('Poulet', 6.9, 'ADEME', '2024-01-15'),
('Saumon', 6.0, 'ADEME', '2024-02-10'),
('Lentilles', 0.9, 'ADEME', '2024-02-10'),
('Pâtes', 1.2, 'ADEME', '2024-02-10'),
('Riz', 2.7, 'ADEME', '2024-02-10'),
('Fromage', 13.5, 'ADEME', '2024-03-01'),
('Lait', 3.2, 'ADEME', '2024-03-01'),
('Œufs', 4.8, 'ADEME', '2024-03-01'),
('Tomates', 2.1, 'ADEME', '2024-03-01'),
('Pommes de terre', 0.3, 'ADEME', '2024-03-01'),
('Carottes', 0.4, 'ADEME', '2024-03-01'),
('Pomme', 0.4, 'ADEME', '2024-03-01');

INSERT IGNORE INTO analyse_carbone (score_co2_total, niveau_impact, date_calcul, methode_calcul, id_recette) VALUES
(0.45, 'bas', '2024-03-01', 'Analyse simplifiée par ingrédient', 1),
(12.5, 'eleve', '2024-03-02', 'Cycle de vie complet', 2),
(1.2, 'bas', '2024-03-03', 'Estimation rapide', 3),
(4.8, 'moyen', '2024-03-04', 'Calcul basé sur le poids', 4);

