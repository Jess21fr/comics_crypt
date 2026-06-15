-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 16 juin 2026 à 00:11
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `comics_crypt`
--

-- --------------------------------------------------------

--
-- Structure de la table `api_requests`
--

CREATE TABLE `api_requests` (
  `id` int(11) NOT NULL,
  `endpoint` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `api_requests`
--

INSERT INTO `api_requests` (`id`, `endpoint`, `created_at`) VALUES
(1, 'search', '2026-06-15 23:23:01'),
(2, 'search', '2026-06-15 23:29:27'),
(3, 'search', '2026-06-15 23:38:45');

-- --------------------------------------------------------

--
-- Structure de la table `publishers`
--

CREATE TABLE `publishers` (
  `id` int(10) UNSIGNED NOT NULL,
  `publisher_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `last_sync` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `publishers`
--

INSERT INTO `publishers` (`id`, `publisher_id`, `name`, `logo`, `actif`, `last_sync`) VALUES
(1, 31, 'Marvel', 'publisher_31.gif', 1, '2026-06-12 22:30:55'),
(2, 10, 'DC Comics', 'publisher_10.jpg', 1, '2026-06-12 22:31:19'),
(3, 4788, 'Urban Comics', 'publisher_4788.jpeg', 1, '2026-06-12 22:32:18'),
(4, 2245, 'Panini France', 'publisher_2245.jpg', 1, '2026-06-12 22:37:17'),
(5, 2923, 'Delcourt', 'publisher_2923.jpg', 1, '2026-06-12 22:37:40'),
(6, 513, 'Image', 'publisher_513.png', 1, '2026-06-12 22:38:27'),
(7, 364, 'Dark Horse Comics', 'publisher_364.jpg', 1, '2026-06-13 12:10:08'),
(8, 2932, 'Le Téméraire', 'publisher_2932.png', 1, '2026-06-13 13:17:19'),
(9, 2579, 'Paperback - Casterman', 'publisher_2579.jpg', 1, '2026-06-13 14:49:43'),
(10, 1133, 'Semic', 'publisher_1133.jpg', 1, '2026-06-13 14:51:19');

-- --------------------------------------------------------

--
-- Structure de la table `series`
--

CREATE TABLE `series` (
  `id` int(10) UNSIGNED NOT NULL,
  `series_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_year` int(11) DEFAULT NULL,
  `count_of_issues` int(10) UNSIGNED DEFAULT NULL,
  `publisher_id` int(10) UNSIGNED NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `last_sync` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `series`
--

INSERT INTO `series` (`id`, `series_id`, `name`, `start_year`, `count_of_issues`, `publisher_id`, `logo`, `actif`, `last_sync`) VALUES
(3, 4596, 'The Infinity Gauntlet', 1991, 6, 31, '4596.jpg', 1, '2026-06-15 21:46:24'),
(5, 4795, 'The Infinity War', 1992, 6, 31, '4795.jpg', 1, '2026-06-15 21:47:57'),
(6, 5019, 'The Infinity Crusade', 1993, 6, 31, '5019.jpg', 1, '2026-06-15 22:44:52');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `api_requests`
--
ALTER TABLE `api_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `endpoint` (`endpoint`),
  ADD KEY `created_at` (`created_at`);

--
-- Index pour la table `publishers`
--
ALTER TABLE `publishers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_publisher_id` (`publisher_id`);

--
-- Index pour la table `series`
--
ALTER TABLE `series`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_series_id` (`series_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `api_requests`
--
ALTER TABLE `api_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `publishers`
--
ALTER TABLE `publishers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `series`
--
ALTER TABLE `series`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
