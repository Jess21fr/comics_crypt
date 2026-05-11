-- ============================================================
-- COMICSVAULT - Schéma MySQL complet
-- PHP 8.x / MySQL 8.x
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `comicsvault`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `comicsvault`;

-- ------------------------------------------------------------
-- TABLES DE RÉFÉRENCE
-- ------------------------------------------------------------

CREATE TABLE `publishers_vf` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(150) NOT NULL,
  `website`    VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `publishers_vo` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(150) NOT NULL,
  `website`    VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `collections` (
  `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`            VARCHAR(150) NOT NULL,
  `publisher_vo_id` INT UNSIGNED DEFAULT NULL,
  `description`     TEXT DEFAULT NULL,
  `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_collection_publisher_vo`
    FOREIGN KEY (`publisher_vo_id`) REFERENCES `publishers_vo`(`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `formats` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`        VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- SÉRIES ET VOLUMES DE SÉRIE (gestion des reboots)
-- ------------------------------------------------------------

CREATE TABLE `series` (
  `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title`           VARCHAR(255) NOT NULL,
  `publisher_vo_id` INT UNSIGNED NOT NULL,
  `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_series_publisher_vo`
    FOREIGN KEY (`publisher_vo_id`) REFERENCES `publishers_vo`(`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Un "volume" de série correspond à une ère/reboot (ex. Avengers Vol.1 1963-1996)
-- Plusieurs volumes peuvent coexister pour une même série (plusieurs #1)
CREATE TABLE `series_volumes` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `series_id`     INT UNSIGNED NOT NULL,
  `volume_number` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `year_start`    YEAR DEFAULT NULL,
  `year_end`      YEAR DEFAULT NULL,       -- NULL = en cours
  `notes`         TEXT DEFAULT NULL,
  `created_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_series_volume` (`series_id`, `volume_number`),
  CONSTRAINT `fk_sv_series`
    FOREIGN KEY (`series_id`) REFERENCES `series`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- ÉPISODES VO (issues)
-- ------------------------------------------------------------

CREATE TABLE `issues` (
  `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `series_volume_id` INT UNSIGNED NOT NULL,
  `number`           VARCHAR(20) NOT NULL,    -- VARCHAR car : #0, #0.5, Annual #1...
  `title`            VARCHAR(255) DEFAULT NULL,
  `cover_url`        VARCHAR(500) DEFAULT NULL,
  `pub_date`         DATE DEFAULT NULL,       -- CRITÈRE DE TRI CHRONOLOGIQUE
  -- Fascicule US possédé directement (sans passer par un tome)
  `owned_single`     TINYINT(1) NOT NULL DEFAULT 0,
  `read_single`      TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_issue` (`series_volume_id`, `number`),
  CONSTRAINT `fk_issue_series_volume`
    FOREIGN KEY (`series_volume_id`) REFERENCES `series_volumes`(`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TOMES (volumes physiques VF ou VO)
-- ------------------------------------------------------------

CREATE TABLE `volumes` (
  `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `is_vf`            TINYINT(1) NOT NULL DEFAULT 1,  -- 1=VF, 0=VO
  -- Éditeurs
  `publisher_vf_id`  INT UNSIGNED DEFAULT NULL,
  `publisher_vo_id`  INT UNSIGNED NOT NULL,
  -- Classification
  `collection_id`    INT UNSIGNED DEFAULT NULL,
  `format_id`        INT UNSIGNED DEFAULT NULL,
  `series_id`        INT UNSIGNED DEFAULT NULL,       -- série principale du tome
  -- Identification
  `tome_number`      SMALLINT UNSIGNED DEFAULT NULL,
  `tome_title`       VARCHAR(255) DEFAULT NULL,
  `isbn`             VARCHAR(20) DEFAULT NULL,
  -- Média
  `cover_url`        VARCHAR(500) DEFAULT NULL,
  `pages`            SMALLINT UNSIGNED DEFAULT NULL,
  `pub_date`         DATE DEFAULT NULL,
  -- Type de distribution
  `distribution`     ENUM('kiosque','librairie','both') DEFAULT 'librairie',
  -- Statut collection
  `in_collection`    TINYINT(1) NOT NULL DEFAULT 0,
  `wanted`           TINYINT(1) NOT NULL DEFAULT 0,
  `is_read`          TINYINT(1) NOT NULL DEFAULT 0,
  `price_publisher`  DECIMAL(6,2) DEFAULT NULL,       -- prix éditeur
  `notes`            TEXT DEFAULT NULL,
  `created_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_volume_pub_vf`
    FOREIGN KEY (`publisher_vf_id`) REFERENCES `publishers_vf`(`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_volume_pub_vo`
    FOREIGN KEY (`publisher_vo_id`) REFERENCES `publishers_vo`(`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_volume_collection`
    FOREIGN KEY (`collection_id`) REFERENCES `collections`(`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_volume_format`
    FOREIGN KEY (`format_id`) REFERENCES `formats`(`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_volume_series`
    FOREIGN KEY (`series_id`) REFERENCES `series`(`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLE PIVOT : Tome <-> Épisodes (N-N)
-- Résout : même épisode dans plusieurs tomes VF différents
-- ------------------------------------------------------------

CREATE TABLE `volume_issues` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `volume_id`  INT UNSIGNED NOT NULL,
  `issue_id`   INT UNSIGNED NOT NULL,
  `sort_order` TINYINT UNSIGNED DEFAULT 0,  -- ordre des épisodes dans le tome
  UNIQUE KEY `uq_volume_issue` (`volume_id`, `issue_id`),
  CONSTRAINT `fk_vi_volume`
    FOREIGN KEY (`volume_id`) REFERENCES `volumes`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_vi_issue`
    FOREIGN KEY (`issue_id`) REFERENCES `issues`(`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- DONNÉES DE RÉFÉRENCE PAR DÉFAUT
-- ------------------------------------------------------------

INSERT INTO `publishers_vo` (`name`, `website`) VALUES
  ('DC Comics',    'https://www.dc.com'),
  ('Marvel Comics','https://www.marvel.com'),
  ('Image Comics', 'https://imagecomics.com'),
  ('Valiant',      'https://valiantentertainment.com'),
  ('Dark Horse',   'https://www.darkhorse.com'),
  ('IDW Publishing','https://idwpublishing.com');

INSERT INTO `publishers_vf` (`name`, `website`) VALUES
  ('Urban Comics',  'https://www.urban-comics.com'),
  ('Panini Comics', 'https://www.paninicomics.fr'),
  ('Delcourt',      'https://www.editions-delcourt.fr'),
  ('Bliss Comics',  'https://www.blisscomics.fr'),
  ('Glénat',        'https://www.glenat.com');

INSERT INTO `collections` (`name`, `publisher_vo_id`) VALUES
  ('Vertigo',          (SELECT id FROM publishers_vo WHERE name='DC Comics')),
  ('Black Label',      (SELECT id FROM publishers_vo WHERE name='DC Comics')),
  ('MAX',              (SELECT id FROM publishers_vo WHERE name='Marvel Comics')),
  ('Energon Universe', (SELECT id FROM publishers_vo WHERE name='Image Comics'));

INSERT INTO `formats` (`name`) VALUES
  ('Softcover'),
  ('Hardcover'),
  ('Omnibus'),
  ('100% Marvel'),
  ('Marvel Pocket'),
  ('Nomad'),
  ('DC Absolute'),
  ('DC Deluxe');

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- VUE PRINCIPALE : ordre de lecture chronologique
-- ============================================================

CREATE OR REPLACE VIEW `v_reading_order` AS
SELECT
  v.id                                          AS volume_id,
  v.is_vf,
  v.tome_number,
  v.tome_title,
  v.cover_url,
  v.in_collection,
  v.wanted,
  v.is_read,
  v.price_publisher,
  v.distribution,
  s.title                                       AS series_title,
  pvo.name                                      AS publisher_vo,
  pvf.name                                      AS publisher_vf,
  c.name                                        AS collection_name,
  f.name                                        AS format_name,
  MIN(i.pub_date)                               AS earliest_issue_date,
  MAX(i.pub_date)                               AS latest_issue_date,
  COUNT(vi.issue_id)                            AS issue_count,
  GROUP_CONCAT(
    CONCAT(ser.title,' #',i.number)
    ORDER BY i.pub_date ASC
    SEPARATOR ', '
  )                                             AS issues_list
FROM `volumes` v
LEFT JOIN `publishers_vf`  pvf ON pvf.id = v.publisher_vf_id
LEFT JOIN `publishers_vo`  pvo ON pvo.id = v.publisher_vo_id
LEFT JOIN `collections`    c   ON c.id   = v.collection_id
LEFT JOIN `formats`        f   ON f.id   = v.format_id
LEFT JOIN `series`         s   ON s.id   = v.series_id
LEFT JOIN `volume_issues`  vi  ON vi.volume_id = v.id
LEFT JOIN `issues`         i   ON i.id   = vi.issue_id
LEFT JOIN `series_volumes` sv  ON sv.id  = i.series_volume_id
LEFT JOIN `series`         ser ON ser.id = sv.series_id
GROUP BY v.id
ORDER BY earliest_issue_date ASC, v.tome_number ASC;

-- VUE : fascicules US possédés sans tome (intégrés à la chronologie)
CREATE OR REPLACE VIEW `v_single_issues_reading_order` AS
SELECT
  i.id,
  i.number,
  i.title,
  i.cover_url,
  i.pub_date,
  i.owned_single,
  i.read_single,
  sv.volume_number,
  ser.title   AS series_title,
  pvo.name    AS publisher_vo
FROM `issues` i
JOIN `series_volumes` sv  ON sv.id  = i.series_volume_id
JOIN `series`         ser ON ser.id = sv.series_id
JOIN `publishers_vo`  pvo ON pvo.id = ser.publisher_vo_id
WHERE i.owned_single = 1
ORDER BY i.pub_date ASC;