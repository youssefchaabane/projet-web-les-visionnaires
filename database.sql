-- =====================================================
-- Base de données complète pour le projet ECOSAVE
-- Système : MySQL / MariaDB (XAMPP)
-- =====================================================

CREATE DATABASE IF NOT EXISTS `projet-web` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `projet-web`;

SET FOREIGN_KEY_CHECKS=0;

-- 1. Table Utilisateur (Authentification et profil)
DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE `utilisateur` (
  `id_user` INT AUTO_INCREMENT PRIMARY KEY,
  `nom_prenom` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `mot_de_passe` VARCHAR(255) NOT NULL,
  `date_creation` DATE NOT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'utilisateur',
  `est_actif` TINYINT(1) NOT NULL DEFAULT 1,
  `niveau_activite` VARCHAR(100) NOT NULL DEFAULT '',
  `regime_alimentaire` VARCHAR(100) NOT NULL DEFAULT '',
  `objectif_sante` VARCHAR(255) NOT NULL DEFAULT '',
  `objectif_eco` VARCHAR(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tables pour la Gestion Médicale (Allergies et Traitements)
DROP TABLE IF EXISTS `allergie`;
CREATE TABLE `allergie` (
  `id_allergie` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `niveau_danger` ENUM('faible', 'modere', 'eleve', 'critique') DEFAULT 'modere',
  `symptomes` TEXT,
  `type` VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `utilisateur_allergie`;
CREATE TABLE `utilisateur_allergie` (
  `id_user` INT NOT NULL,
  `id_allergie` INT NOT NULL,
  PRIMARY KEY (`id_user`, `id_allergie`),
  FOREIGN KEY (`id_user`) REFERENCES `utilisateur`(`id_user`) ON DELETE CASCADE,
  FOREIGN KEY (`id_allergie`) REFERENCES `allergie`(`id_allergie`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `traitement`;
CREATE TABLE `traitement` (
  `id_traitement` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(255) NOT NULL,
  `type_traitement` VARCHAR(100),
  `dosage` VARCHAR(100),
  `duree` VARCHAR(100),
  `effets_secondaires` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `allergie_traitement`;
CREATE TABLE `allergie_traitement` (
  `id_allergie` INT NOT NULL,
  `id_traitement` INT NOT NULL,
  PRIMARY KEY (`id_allergie`, `id_traitement`),
  FOREIGN KEY (`id_allergie`) REFERENCES `allergie`(`id_allergie`) ON DELETE CASCADE,
  FOREIGN KEY (`id_traitement`) REFERENCES `traitement`(`id_traitement`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tables pour le Stock et l'Inventaire
DROP TABLE IF EXISTS `categorie`;
CREATE TABLE `categorie` (
  `id_cat` INT AUTO_INCREMENT PRIMARY KEY,
  `nom_cat` VARCHAR(255) NOT NULL,
  `description_cat` TEXT,
  `lieu_stockage` VARCHAR(255),
  `temp_conseille` VARCHAR(50),
  `delai_alerte_jours` INT DEFAULT 30,
  `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `date_modification` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `produit`;
CREATE TABLE `produit` (
  `id_prod` INT AUTO_INCREMENT PRIMARY KEY,
  `nom_prod` VARCHAR(255) NOT NULL,
  `date_expiration` DATE,
  `poids_produit` DECIMAL(10,2),
  `quantite_dispo` INT DEFAULT 0,
  `id_cat` INT,
  FOREIGN KEY (`id_cat`) REFERENCES `categorie`(`id_cat`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tables pour les Recettes Gastronomiques
DROP TABLE IF EXISTS `rec_recette`;
CREATE TABLE `rec_recette` (
  `id_recette` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `nombre_personnes` INT DEFAULT 2,
  `temps_preparation` INT DEFAULT 0,
  `temps_cuisson` INT DEFAULT 0,
  `difficulte` ENUM('facile', 'moyen', 'difficile') DEFAULT 'moyen',
  `calories_totales` INT DEFAULT 0,
  `image_url` VARCHAR(255),
  `id_user` INT,
  `date_creation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_user`) REFERENCES `utilisateur`(`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `rec_detail_recette`;
CREATE TABLE `rec_detail_recette` (
  `id_detail` INT AUTO_INCREMENT PRIMARY KEY,
  `id_recette` INT NOT NULL,
  `ingredient` VARCHAR(255),
  `quantite` VARCHAR(100),
  `etape` TEXT,
  FOREIGN KEY (`id_recette`) REFERENCES `rec_recette`(`id_recette`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Tables pour l'Empreinte Carbone (ECOSAVE)
DROP TABLE IF EXISTS `eco_recette`;
CREATE TABLE `eco_recette` (
  `id_recette` INT AUTO_INCREMENT PRIMARY KEY,
  `nom` VARCHAR(100) NOT NULL,
  `description` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `eco_facteur_emission`;
CREATE TABLE `eco_facteur_emission` (
  `id_facteur` INT AUTO_INCREMENT PRIMARY KEY,
  `categorie_aliment` VARCHAR(100) NOT NULL,
  `co2_par_kg` DECIMAL(10,2) NOT NULL,
  `source_donnee` VARCHAR(255),
  `date_derniere_maj` DATE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `eco_analyse_carbone`;
CREATE TABLE `eco_analyse_carbone` (
  `id_analyse` INT AUTO_INCREMENT PRIMARY KEY,
  `score_co2_total` DECIMAL(10,2) NOT NULL,
  `niveau_impact` ENUM('bas', 'moyen', 'eleve') NOT NULL,
  `date_calcul` DATE,
  `methode_calcul` VARCHAR(100),
  `id_recette` INT NOT NULL,
  FOREIGN KEY (`id_recette`) REFERENCES `eco_recette`(`id_recette`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Tables pour les Publications et Commentaires
DROP TABLE IF EXISTS `publication`;
CREATE TABLE `publication` (
  `id_pub` INT AUTO_INCREMENT PRIMARY KEY,
  `titre` VARCHAR(255) NOT NULL,
  `contenu` TEXT NOT NULL,
  `date_publication` DATETIME NOT NULL,
  `media_url` VARCHAR(512),
  `id_user` INT NOT NULL,
  FOREIGN KEY (`id_user`) REFERENCES `utilisateur`(`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `commentaire`;
CREATE TABLE `commentaire` (
  `id_commentaire` INT AUTO_INCREMENT PRIMARY KEY,
  `id_pub` INT NOT NULL,
  `note` INT,
  `contenu` TEXT NOT NULL,
  `date_commentaire` DATETIME NOT NULL,
  `likes_count` INT DEFAULT 0,
  FOREIGN KEY (`id_pub`) REFERENCES `publication`(`id_pub`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;

-- 6. Insertion de données de test
INSERT INTO `utilisateur` (`nom_prenom`, `email`, `mot_de_passe`, `date_creation`, `role`, `est_actif`, `niveau_activite`, `regime_alimentaire`, `objectif_sante`, `objectif_eco`) VALUES 
('Administrateur ECOSAVE', 'admin@ecosave.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', CURDATE(), 'admin', 1, 'Modéré', 'Aucun', 'Garder la forme', 'Réduire les déchets'),
('Jean Dupont', 'jean.dupont@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', CURDATE(), 'utilisateur', 1, 'Actif', 'Végétarien', 'Manger sain', 'Zéro déchet');

INSERT INTO `categorie` (`nom_cat`, `description_cat`, `lieu_stockage`, `temp_conseille`) VALUES 
('Produits Frais', 'Fruits, légumes et laitages', 'Réfrigérateur', '4°C'),
('Épicerie', 'Pâtes, riz, épices et conserves', 'Placard Sec', 'Ambiante'),
('Surgelés', 'Produits congelés', 'Congélateur', '-18°C');

INSERT INTO `eco_facteur_emission` (`categorie_aliment`, `co2_par_kg`, `source_donnee`, `date_derniere_maj`) VALUES 
('Bœuf', 27.0, 'ADEME', '2024-01-15'),
('Agneau', 24.0, 'ADEME', '2024-01-15'),
('Porc', 7.6, 'ADEME', '2024-01-15'),
('Poulet', 6.9, 'ADEME', '2024-01-15'),
('Lentilles', 0.9, 'ADEME', '2024-02-10'),
('Pâtes', 1.2, 'ADEME', '2024-02-10'),
('Œufs', 4.8, 'ADEME', '2024-03-01'),
('Tomates', 2.1, 'ADEME', '2024-03-01');
