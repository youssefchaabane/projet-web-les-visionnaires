-- Migration pour le module PUB (Publications et Commentaires)
USE `projet-web`;

SET FOREIGN_KEY_CHECKS=0;

-- 1. Table Publication
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

-- 2. Table Commentaire
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

-- 3. Données de test
INSERT INTO `publication` (`titre`, `contenu`, `date_publication`, `id_user`) VALUES 
('Lancement d’ECOSAVE', 'Bienvenue sur notre nouvelle plateforme dédiée à l’écologie et la nutrition !', NOW(), 1),
('Réduire son empreinte carbone', 'Voici 10 conseils simples pour réduire votre impact au quotidien...', NOW(), 1);

INSERT INTO `commentaire` (`id_pub`, `note`, `contenu`, `date_commentaire`) VALUES 
(1, 5, 'Super initiative, bravo !', NOW()),
(1, 4, 'Hâte de voir les prochaines fonctionnalités.', NOW());
