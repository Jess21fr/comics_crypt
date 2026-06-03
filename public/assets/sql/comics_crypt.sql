-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20260517.41b0c9ceb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 03, 2026 at 02:43 PM
-- Server version: 8.4.3
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `comics_crypt`
--

-- --------------------------------------------------------

--
-- Table structure for table `langue`
--

CREATE TABLE `langue` (
  `id` int NOT NULL,
  `id_comicsorg` int NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `nom_court` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `drapeau` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `langue`
--

INSERT INTO `langue` (`id`, `id_comicsorg`, `nom`, `nom_court`, `drapeau`) VALUES
(1, 72, 'Français', 'FR', 'france_flag.svg'),
(2, 225, 'États-Unis', 'US', 'usa_flag.svg'),
(3, 39, 'Royaume-Uni', 'UK', 'uk_flag.png');

-- --------------------------------------------------------

--
-- Table structure for table `publishers`
--

CREATE TABLE `publishers` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `year_began` int DEFAULT NULL,
  `year_ended` int DEFAULT NULL,
  `notes` text,
  `url` varchar(255) DEFAULT NULL,
  `country` varchar(5) DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '0',
  `logo` varchar(255) DEFAULT NULL,
  `publisher_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `publishers`
--

INSERT INTO `publishers` (`id`, `name`, `year_began`, `year_ended`, `notes`, `url`, `country`, `actif`, `logo`, `publisher_id`) VALUES
(4, 'Urban Comics', 2012, NULL, 'Fondé en 2012 par Media-Participations pour l\'exploitation du catalogue et des personnages de DC Comics. Son activité s\'étend à d\'autres éditeurs américains par la suite.', 'http://www.urban-comics.com/', '72', 0, NULL, 7819),
(5, 'Delcourt', 1986, NULL, 'The legal name of the company is Guy DELCOURT Productions.', 'http://www.editions-delcourt.fr/', '72', 0, NULL, 4035),
(6, 'Bliss Comics', 2016, NULL, 'Bliss Comics is a comics publisher dedicated to publishing Valiant Entertainment\'s comics in France.', 'http://www.bliss-comics.com/', '72', 0, NULL, 12164),
(7, 'Panini France', 1997, NULL, 'Some cover scans and issue dates from Howard Drake and Michel Racaud of comicsvf.com', 'http://www.paninicomics.fr/', '72', 0, NULL, 4475),
(8, 'Semic S.A.', 1989, NULL, 'Editeur français qui reprit les éditions Lug en 1989. Ce groupe appartenait au groupe scandinave Semic group.En 1998, Semic France est racheté par le groupe Tournon qui crée Semic S.A. En 2005, le groupe Tournon dissout la société. La marque « Semic » continue d\'appartenir à Tournon.\r\nDepuis 2011, le nom est à nouveau utilisé par une des sociétés du groupe, « Semic Distribution », chargée de commercialiser des produits dérivés, notamment pour le compte de Marvel. \r\n\r\n[French publisher who successor of LUG in 1989. This group belonged to the Scandinavian group Semic Group. In 1998, Semic France was bought by the Tournon group which created Semic S.A. In 2005, the Tournon group dissolves the company. The \"Semic\" brand continues to belong to Tournon. Since 2011, the name has been used again by one of the group\'s companies, \"Semic distribution\", responsible for marketing derivative products, in particular on behalf of Marvel.]', '', '72', 0, NULL, 4543),
(9, 'Éditions Le Téméraire', 1996, 1999, '', 'https://web.archive.org/web/20001007035638/http://www.yiu.net/temeraire/index.htm', '72', 0, NULL, 7318),
(10, 'Marvel', 1939, NULL, 'Marvel was started in 1939 by Martin Goodman under a number of corporation and imprint names that have collectively become known as Timely, and by 1951 had generally become known as Atlas. The \"Marvel era\" began in 1961, the year that the company launched The Fantastic Four and other superhero titles created by Stan Lee, Jack Kirby, Steve Ditko and many others. The Marvel brand, which had been used over the years, was solidified as the company\'s primary brand.\r\n\r\nMarvel counts among its characters such well-known superheroes as Spider-Man, Iron Man, the Hulk, Thor, Captain America, Ant-Man, the Wasp, Black Widow, Wolverine, Captain Marvel, Black Panther, Doctor Strange, Ghost Rider, Blade, Daredevil, the Punisher and Deadpool. Superhero teams exist such as the Avengers, the X-Men, the Fantastic Four and the Guardians of the Galaxy as well as supervillains including Doctor Doom, Magneto, Thanos, Loki, Green Goblin, Kingpin, Red Skull, Ultron, the Mandarin, MODOK, Doctor Octopus, Kang, Dormammu, Annihilus and Galactus. Most of Marvel\'s fictional characters operate in a single reality known as the Marvel Universe, with most locations mirroring real-life places; many major characters are based in New York City. Additionally, Marvel has published several licensed properties from other companies. This includes Star Wars comics twice from 1977 to 1986 and again since 2015.\r\n\r\nDistribution variants:\r\n\r\nWhitman: For issues cover dated February 1977 to around May 1979, Marvel produced non-newsstand editions that were sold mainly in packages. Some, but not all of these, were sold by Western and, because of the branding on the similar DC and Gold Key comics, are generally known as Whitman variants. For the most part they have a square diamond price and either a barcode or a white space where the barcode should be. Some issues from this time have the narrower diamond but it is not currently known if this means they were distributed differently or if it was simply because of the design of the original cover. There is definitely uncertainty between the end of the Whitman comics and the beginning of the Direct comics.\r\n\r\nSpeculation has it that in June 1977, Marvel Comics wanted to test the market to see if comic buyers would accept a five cent price increase. Marvel released a 35 cent variant for every 30 cent comic they produced from June to October 1977. This was a total of 184 different comics. These 35 cent variants were printed in much smaller numbers than the normal 30 cent counterpart and were believed to have been shipped to only six distribution locations to be sold in those specific areas. (Mark Gordon 2010-02-16, revised by Ramon Schenk 2013-02-27).\r\n\r\nDirect: Starting with issues cover dated around April 1979, Marvel started producing non-newsstand editions to be sold in the direct market. For the most part they have a narrower diamond price and the barcode has a line through it (until February 1980 when they started putting a Spider-Man head and eventually other artwork in the UPC box area).\r\n\r\nDirect Edition: In July 1993, Marvel started putting a barcode on the issues sold to the Direct market. They included the words \"Direct Edition\" in the barcode box.', 'http://marvel.com', '225', 1, NULL, 78),
(11, 'DC', 1935, NULL, 'The company commonly referred to as \"DC\" has a somewhat complex beginning.  Its earliest predecessor was founded by Major Malcolm Wheeler-Nicholson as National Allied Publications, Inc.  Eventually, circumstances led Wheeler-Nicholson to form a partnership with printer Harry Donenfeld and eventually accountant Jack Liebowitz to produce his third title, Detective Comics.  This partnership was Detective Comics, Inc. and was the first direct corporate predecessor of today\'s DC.\r\n\r\nBy 1938 mounting debts forced Wheeler-Nicholson to sell his share of Detective Comics, Inc. and then to declare bankruptcy, losing control of his own Nicholson Publishing Co., Inc., the final direct successor to National Allied.  Donenfeld, Liebowitz and Donenfeld\'s silent partner Paul Sampliner promptly bought Nicholson Publishing\'s two titles at bankruptcy auction and transferred them over to Detective Comics, Inc.\r\n\r\nLater, Max Charles Gaines would partner with Liebowitz to start up All-American Comics, sharing distribution and staff with Donenfeld\'s operation.  Except for issues cover-dated 1945, All-American books would share the same logos and branding as Detective Comics, Inc. and its corporate siblings.  For 1945, All-American behaved more as a separate publisher.  Also during that year, Gaines published his first title using Educational Comics, Inc. as the publishing company with an \"EC\" logo.  By the end of the year Gaines sold his share of All-American back to Liebowitz and Donenfeld and went on to build EC into a separate company.\r\n\r\nEventually, Donenfeld and Liebowitz consolidated all of their companies, including those from All-American, into National Comics Publications, Inc.  Prior to that consolidation the companies group as follows:\r\n\r\nOwned solely by Major Malcolm Wheeler-Nicholson:\r\n\r\n - National Allied Publications, Inc. (1935)\r\n - National Allied Newspaper Syndicate, Inc. (1935 - 1936)\r\n - More Fun Magazine, Inc. (1936)\r\n - Nicholson Publishing Co., Inc. (1936 - 1938)\r\n\r\nA few issues were published by \"A.I. Menin, rec. Nicholson Publishing Co., Inc.\" during the bankruptcy proceedings in 1938.\r\n\r\nOwned by Donenfeld, Liebowitz and/or Sampliner (Detective Comics, Inc. was initially co-owned by Wheeler-Nicholson and Liebowitz but the former was forced out in 1938):\r\n\r\n - Detective Comics, Inc. (1937 - 1947)\r\n - Superman, Inc. (1940 - 1947)\r\n - World\'s Best Comic Company (1941 - 1947)\r\n - Tilsam Publications (1943 - 1948)\r\n\r\nAll-American companies under M. C. Gaines and Jack Liebowitz:\r\n\r\n - All-American Comics, Inc. (1939 - 1946)\r\n - Jolaine Publications, Inc. (1941 - 1946)\r\n - J. R. Publishing Co. (1941 - 1947)\r\n - Gainlee Publishing Co. (1942 - 1946)\r\n - Wonder Woman Publishing Co., Inc. (1942 - 1947)', 'http://www.dccomics.com/', '225', 0, NULL, 54),
(12, 'Dark Horse', 1986, NULL, 'Founded by Mike Richardson. \r\n\r\nEmployee roster listed at https://www.comics.org/changeset/7773737/compare/ for\r\n\r\n* Hellcyon (Dark Horse, 2010 series) #1 (April 2010)\r\n* Buffy the Vampire Slayer: Riley (Dark Horse, 2010 Series) #[nn] (August 2010)\r\n* Call of Duty: Black Ops III (Dark Horse, 2015 series) #1 (November 2015)', 'https://www.darkhorse.com/', '225', 0, NULL, 512),
(13, 'Valiant Entertainment', 2007, NULL, 'Not the same company as Valiant/Acclaim.  As of December 2023, Alien Books is the publisher for all Valiant Entertainment superhero comics.', 'http://www.ValiantUniverse.com/', '225', 0, NULL, 4052);

-- --------------------------------------------------------

--
-- Table structure for table `series`
--

CREATE TABLE `series` (
  `id` int UNSIGNED NOT NULL,
  `series_id` int UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `sort_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `format` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `color` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dimensions` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `paper_stock` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `binding` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `publishing_format` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `publication_type` int DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `tracking_notes` text COLLATE utf8mb4_general_ci,
  `year_began` int DEFAULT NULL,
  `year_ended` int DEFAULT NULL,
  `year_began_uncertain` tinyint(1) DEFAULT '0',
  `year_ended_uncertain` tinyint(1) DEFAULT '0',
  `publication_dates` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `first_issue` int DEFAULT NULL,
  `last_issue` int DEFAULT NULL,
  `issue_count` int DEFAULT NULL,
  `country` int UNSIGNED DEFAULT NULL,
  `language` int UNSIGNED DEFAULT NULL,
  `publisher` int UNSIGNED DEFAULT NULL,
  `external_link` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `univers`
--

CREATE TABLE `univers` (
  `id` int UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `langue`
--
ALTER TABLE `langue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `publishers`
--
ALTER TABLE `publishers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_publisher_id` (`publisher_id`);

--
-- Indexes for table `series`
--
ALTER TABLE `series`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `series_id` (`series_id`);

--
-- Indexes for table `univers`
--
ALTER TABLE `univers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `langue`
--
ALTER TABLE `langue`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `publishers`
--
ALTER TABLE `publishers`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `series`
--
ALTER TABLE `series`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `univers`
--
ALTER TABLE `univers`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
