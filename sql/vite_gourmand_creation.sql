-- =========================================================
-- Vite & Gourmand — création de la base de données
-- Compatible MySQL 8 et MariaDB 10.11
-- À importer dans la base vite_gourmand, AVANT vite_gourmand_donnees.sql
-- Attention : supprime les tables existantes avant de les recréer.
-- =========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `avis`;
DROP TABLE IF EXISTS `suivi_commande`;
DROP TABLE IF EXISTS `commande_plat`;
DROP TABLE IF EXISTS `commande`;
DROP TABLE IF EXISTS `menu_plat`;
DROP TABLE IF EXISTS `plat_allergene`;
DROP TABLE IF EXISTS `menu`;
DROP TABLE IF EXISTS `plat`;
DROP TABLE IF EXISTS `utilisateur`;
DROP TABLE IF EXISTS `horaire`;
DROP TABLE IF EXISTS `allergene`;
DROP TABLE IF EXISTS `regime`;
DROP TABLE IF EXISTS `theme`;
DROP TABLE IF EXISTS `role`;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------
-- Tables de référence
-- ---------------------------------------------------------

CREATE TABLE `role` (
  `role_id` INT NOT NULL AUTO_INCREMENT,
  `libelle` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `theme` (
  `theme_id` INT NOT NULL AUTO_INCREMENT,
  `libelle` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`theme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `regime` (
  `regime_id` INT NOT NULL AUTO_INCREMENT,
  `libelle` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`regime_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `allergene` (
  `allergene_id` INT NOT NULL AUTO_INCREMENT,
  `libelle` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`allergene_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `horaire` (
  `horaire_id` INT NOT NULL AUTO_INCREMENT,
  `jour` VARCHAR(10) NOT NULL,
  `heure_ouverture` TIME DEFAULT NULL,
  `heure_fermeture` TIME DEFAULT NULL,
  PRIMARY KEY (`horaire_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Utilisateurs
-- ---------------------------------------------------------

CREATE TABLE `utilisateur` (
  `utilisateur_id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `nom` VARCHAR(50) NOT NULL,
  `prenom` VARCHAR(50) NOT NULL,
  `telephone` VARCHAR(20) NOT NULL,
  `ville` VARCHAR(100) NOT NULL,
  `pays` VARCHAR(50) NOT NULL,
  `adresse_postale` VARCHAR(255) NOT NULL,
  `role_id` INT NOT NULL,
  `actif` TINYINT(1) NOT NULL DEFAULT 1,
  `reset_token_hash` VARCHAR(64) DEFAULT NULL,
  `reset_expiration` DATETIME DEFAULT NULL,
  PRIMARY KEY (`utilisateur_id`),
  UNIQUE KEY `email_unique` (`email`),
  KEY `fk_utilisateur_role` (`role_id`),
  CONSTRAINT `fk_utilisateur_role` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Catalogue : plats et menus
-- ---------------------------------------------------------

CREATE TABLE `plat` (
  `plat_id` INT NOT NULL AUTO_INCREMENT,
  `titre_plat` VARCHAR(100) NOT NULL,
  `photo` VARCHAR(255) NOT NULL,
  `type_plat` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`plat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `menu` (
  `menu_id` INT NOT NULL AUTO_INCREMENT,
  `titre` VARCHAR(100) NOT NULL,
  `nombre_personne_minimum` INT NOT NULL,
  `prix_par_personne` DECIMAL(10,2) NOT NULL,
  `description` TEXT NOT NULL,
  `quantite_restante` INT NOT NULL,
  `theme_id` INT NOT NULL,
  `regime_id` INT NOT NULL,
  `conditions` TEXT NOT NULL,
  `photo` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`menu_id`),
  KEY `fk_menu_theme` (`theme_id`),
  KEY `fk_menu_regime` (`regime_id`),
  CONSTRAINT `fk_menu_theme` FOREIGN KEY (`theme_id`) REFERENCES `theme` (`theme_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_menu_regime` FOREIGN KEY (`regime_id`) REFERENCES `regime` (`regime_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Association CONTIENT : les allergènes de chaque plat
CREATE TABLE `plat_allergene` (
  `plat_id` INT NOT NULL,
  `allergene_id` INT NOT NULL,
  PRIMARY KEY (`plat_id`, `allergene_id`),
  KEY `fk_platallergene_allergene` (`allergene_id`),
  CONSTRAINT `fk_platallergene_plat` FOREIGN KEY (`plat_id`) REFERENCES `plat` (`plat_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_platallergene_allergene` FOREIGN KEY (`allergene_id`) REFERENCES `allergene` (`allergene_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Association COMPOSE : les plats proposés dans chaque menu
CREATE TABLE `menu_plat` (
  `menu_id` INT NOT NULL,
  `plat_id` INT NOT NULL,
  PRIMARY KEY (`menu_id`, `plat_id`),
  KEY `fk_menuplat_plat` (`plat_id`),
  CONSTRAINT `fk_menuplat_menu` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_menuplat_plat` FOREIGN KEY (`plat_id`) REFERENCES `plat` (`plat_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Commandes, suivi et avis
-- ---------------------------------------------------------

CREATE TABLE `commande` (
  `numero_commande` VARCHAR(20) NOT NULL,
  `date_commande` DATETIME NOT NULL,
  `date_prestation` DATE NOT NULL,
  `heure_livraison` TIME NOT NULL,
  `adresse_livraison` VARCHAR(255) NOT NULL,
  `ville_livraison` VARCHAR(100) NOT NULL,
  `distance_km` DECIMAL(10,2) NOT NULL,
  `prix_menu` DECIMAL(10,2) NOT NULL,
  `nombre_personne` INT NOT NULL,
  `prix_livraison` DECIMAL(10,2) NOT NULL,
  `statut` VARCHAR(50) NOT NULL,
  `pret_materiel` TINYINT(1) NOT NULL DEFAULT 0,
  `restitution_materiel` TINYINT(1) NOT NULL DEFAULT 0,
  `utilisateur_id` INT NOT NULL,
  `menu_id` INT NOT NULL,
  PRIMARY KEY (`numero_commande`),
  KEY `fk_commande_utilisateur` (`utilisateur_id`),
  KEY `fk_commande_menu` (`menu_id`),
  CONSTRAINT `fk_commande_utilisateur` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_commande_menu` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Association CHOISIT : les plats choisis, avec la quantité
CREATE TABLE `commande_plat` (
  `numero_commande` VARCHAR(20) NOT NULL,
  `plat_id` INT NOT NULL,
  `quantite` INT NOT NULL,
  PRIMARY KEY (`numero_commande`, `plat_id`),
  KEY `fk_commandeplat_plat` (`plat_id`),
  CONSTRAINT `fk_commandeplat_commande` FOREIGN KEY (`numero_commande`) REFERENCES `commande` (`numero_commande`),
  CONSTRAINT `fk_commandeplat_plat` FOREIGN KEY (`plat_id`) REFERENCES `plat` (`plat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Historique des changements d'état de chaque commande
CREATE TABLE `suivi_commande` (
  `suivi_id` INT NOT NULL AUTO_INCREMENT,
  `statut` VARCHAR(50) NOT NULL,
  `date_heure` DATETIME NOT NULL,
  `motif` VARCHAR(255) DEFAULT NULL,
  `mode_contact` VARCHAR(50) DEFAULT NULL,
  `numero_commande` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`suivi_id`),
  KEY `fk_suivi_commande` (`numero_commande`),
  CONSTRAINT `fk_suivi_commande` FOREIGN KEY (`numero_commande`) REFERENCES `commande` (`numero_commande`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Un avis au plus par commande (clé UNIQUE)
CREATE TABLE `avis` (
  `avis_id` INT NOT NULL AUTO_INCREMENT,
  `note` TINYINT NOT NULL,
  `description` TEXT NOT NULL,
  `statut` VARCHAR(20) NOT NULL,
  `numero_commande` VARCHAR(20) NOT NULL,
  `utilisateur_id` INT NOT NULL,
  PRIMARY KEY (`avis_id`),
  UNIQUE KEY `numero_commande_unique` (`numero_commande`),
  KEY `fk_avis_utilisateur` (`utilisateur_id`),
  CONSTRAINT `fk_avis_commande` FOREIGN KEY (`numero_commande`) REFERENCES `commande` (`numero_commande`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_avis_utilisateur` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;