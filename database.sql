-- Création de la base de données
CREATE DATABASE IF NOT EXISTS `gestion_allergies` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `gestion_allergies`;

-- Suppression de la table existante si elle existe pour la recréer proprement
DROP TABLE IF EXISTS `utilisateur`;

-- Création de la table utilisateur
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

-- Insertion d'un compte administrateur de test (mot de passe: password)
-- Le mot de passe ci-dessous est hashé avec BCRYPT (password_hash)
INSERT INTO `utilisateur` (
  `nom_prenom`, `email`, `mot_de_passe`, `date_creation`, `role`, `est_actif`, `niveau_activite`, `regime_alimentaire`, `objectif_sante`, `objectif_eco`
) VALUES (
  'Administrateur ECOSAVE',
  'admin@ecosave.com',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- 'password' par défaut
  CURDATE(),
  'admin',
  1,
  'Modéré',
  'Aucun',
  'Garder la forme',
  'Réduire les déchets'
);

-- Insertion d'un utilisateur de test (mot de passe: password)
INSERT INTO `utilisateur` (
  `nom_prenom`, `email`, `mot_de_passe`, `date_creation`, `role`, `est_actif`, `niveau_activite`, `regime_alimentaire`, `objectif_sante`, `objectif_eco`
) VALUES (
  'Jean Dupont',
  'jean.dupont@email.com',
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- 'password' par défaut
  CURDATE(),
  'utilisateur',
  1,
  'Actif',
  'Végétarien',
  'Manger sain',
  'Zéro déchet'
);
