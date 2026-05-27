-- ------------------------------------------------------------
-- 1) Création de la base si elle n'existe pas
-- ------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `comics_crypt`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE `comics_crypt`;

-- ------------------------------------------------------------
-- 2) Table univers
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `univers`;

CREATE TABLE `univers` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 3) Table publishers
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `publishers`;

CREATE TABLE `publishers` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `country` VARCHAR(5) DEFAULT NULL,
  `actif` TINYINT(1) NOT NULL DEFAULT 0,
  `logo` VARCHAR(255) DEFAULT NULL,
  `publisher_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_publisher_id` (`publisher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 4) Table series
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `series`;

CREATE TABLE `series` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(255) NOT NULL,
  `volume` VARCHAR(50) DEFAULT NULL,
  `nbre_episodes` INT(10) UNSIGNED DEFAULT NULL,
  `date_debut` DATE DEFAULT NULL,
  `date_fin` DATE DEFAULT NULL,
  `id_comicsorg` INT(10) UNSIGNED NOT NULL,
  `publisher_id` INT(10) UNSIGNED DEFAULT NULL,
  `univers_id` INT(10) UNSIGNED DEFAULT NULL,
  `logo` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_id_comicsorg` (`id_comicsorg`),
  KEY `idx_publisher_id` (`publisher_id`),
  KEY `idx_univers_id` (`univers_id`),
  CONSTRAINT `fk_series_publisher`
      FOREIGN KEY (`publisher_id`)
      REFERENCES `publishers` (`publisher_id`)
      ON UPDATE CASCADE
      ON DELETE SET NULL,
  CONSTRAINT `fk_series_univers`
      FOREIGN KEY (`univers_id`)
      REFERENCES `univers` (`id`)
      ON UPDATE CASCADE
      ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
