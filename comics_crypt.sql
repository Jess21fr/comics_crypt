-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 16 juin 2026 à 23:08
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
  `id` int(10) UNSIGNED NOT NULL,
  `endpoint` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `api_requests`
--

INSERT INTO `api_requests` (`id`, `endpoint`, `created_at`) VALUES
(1, 'search', '2026-06-15 23:23:01'),
(2, 'search', '2026-06-15 23:29:27'),
(3, 'search', '2026-06-15 23:38:45'),
(4, 'publishers', '2026-06-16 07:31:48'),
(5, 'publishers', '2026-06-16 07:32:01'),
(6, 'search', '2026-06-16 07:38:34'),
(7, 'search', '2026-06-16 07:38:50'),
(8, 'search', '2026-06-16 09:26:20'),
(9, 'search', '2026-06-16 09:32:09'),
(10, 'search', '2026-06-16 09:32:14'),
(11, 'search', '2026-06-16 09:34:58'),
(12, 'search', '2026-06-16 11:00:32'),
(13, 'search', '2026-06-16 11:00:35'),
(14, 'search', '2026-06-16 11:00:38'),
(15, 'search', '2026-06-16 11:00:41'),
(16, 'search', '2026-06-16 11:00:44'),
(17, 'search', '2026-06-16 11:00:47'),
(18, 'search', '2026-06-16 11:00:50'),
(19, 'search', '2026-06-16 11:00:53'),
(20, 'issues', '2026-06-16 11:59:21'),
(21, 'issues', '2026-06-16 12:02:38'),
(22, 'issues', '2026-06-16 12:46:15'),
(23, 'issues', '2026-06-16 12:53:24'),
(24, 'issues', '2026-06-16 12:53:41'),
(25, 'issues', '2026-06-16 12:54:17'),
(26, 'issues', '2026-06-16 13:59:50'),
(27, 'issues', '2026-06-16 14:36:41'),
(28, 'issues', '2026-06-16 16:14:38'),
(29, 'search', '2026-06-16 17:55:56'),
(30, 'search', '2026-06-16 17:55:59'),
(31, 'search', '2026-06-16 17:56:02'),
(32, 'search', '2026-06-16 17:56:06'),
(33, 'search', '2026-06-16 17:56:09'),
(34, 'search', '2026-06-16 17:56:12'),
(35, 'search', '2026-06-16 17:56:16'),
(36, 'search', '2026-06-16 17:56:23'),
(37, 'search', '2026-06-16 17:56:29'),
(38, 'search', '2026-06-16 17:56:32'),
(39, 'search', '2026-06-16 17:56:36'),
(40, 'search', '2026-06-16 17:56:40'),
(41, 'search', '2026-06-16 17:56:44'),
(42, 'search', '2026-06-16 17:56:48'),
(43, 'search', '2026-06-16 17:56:51'),
(44, 'search', '2026-06-16 17:56:54'),
(45, 'search', '2026-06-16 17:56:57'),
(46, 'search', '2026-06-16 17:57:00'),
(47, 'search', '2026-06-16 17:57:03'),
(48, 'search', '2026-06-16 17:57:06'),
(49, 'search', '2026-06-16 17:57:10'),
(50, 'search', '2026-06-16 17:57:13'),
(51, 'search', '2026-06-16 17:57:18'),
(52, 'search', '2026-06-16 17:57:27'),
(53, 'search', '2026-06-16 17:57:34'),
(54, 'search', '2026-06-16 17:57:37'),
(55, 'search', '2026-06-16 17:57:42'),
(56, 'search', '2026-06-16 17:57:45'),
(57, 'search', '2026-06-16 17:57:48'),
(58, 'search', '2026-06-16 17:57:53'),
(59, 'search', '2026-06-16 17:57:57'),
(60, 'search', '2026-06-16 17:58:00'),
(61, 'search', '2026-06-16 17:58:04'),
(62, 'search', '2026-06-16 17:58:08'),
(63, 'search', '2026-06-16 17:58:11'),
(64, 'issues', '2026-06-16 18:16:08'),
(65, 'issues', '2026-06-16 18:16:11'),
(66, 'issues', '2026-06-16 18:16:14'),
(67, 'issues', '2026-06-16 18:16:49'),
(68, 'issues', '2026-06-16 18:16:54');

-- --------------------------------------------------------

--
-- Structure de la table `collections`
--

CREATE TABLE `collections` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `publisher_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `collections`
--

INSERT INTO `collections` (`id`, `name`, `publisher_id`) VALUES
(1, 'Marvel Events', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `formats`
--

CREATE TABLE `formats` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `issues`
--

CREATE TABLE `issues` (
  `id` int(10) UNSIGNED NOT NULL,
  `issue_id` int(10) UNSIGNED NOT NULL,
  `series_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `issue_number` varchar(50) NOT NULL,
  `cover_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `last_sync` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `issues`
--

INSERT INTO `issues` (`id`, `issue_id`, `series_id`, `name`, `issue_number`, `cover_date`, `description`, `logo`, `last_sync`) VALUES
(1, 35783, 4795, 'Chthonic Maneuvers', '1', '1992-06-30', 'When evil dopplegangers of the Marvel heroes appear, it\'s all-out war! Why has Magus unleashed them on an unsuspecting world? And is the heroes only hope...Thanos?!All over the universe an incredible energy is sensed and no one can make sense of it, but they all know it is a danger to reality itself. Thanos leaves the planet he has inhabited since the Infinity Gauntlet. Galactus meets with Eternity. Reed Richards begins to make calculations. Doctor Doom begins to forge a plan to obtain this power.Thanos finds the source of the power and marvels at it. He is soon met by the Magus. But, Thanos has changed. He now realizes the power he once possessed was not meant for beings such as himself and the Magus. Magus then shows him a glimpse of the doppelgangers he has created to defeat those who would oppose him and sends Thanos back to his dimension. Thanos realizes he will require assistance to stop the Magus\' threat to reality.Meanwhile, some of the doppelgangers have made it to Earth and found their counterparts. Some are defeated, some are not. Mr Fantastic realizes the scope of this threat and organizes a meeting, calling upon the Avengers, the X-Men, the Avengers West Coast, X-Factor, Alpha Flight and the New Warriors.Galactus makes a plan to obtain the help of a mage. Doctor Doom forges an alliance with Kang to steal this power. And finally, Thanos arrives to seek the help of Adam Warlock and the Infinity Watch.', '35783.jpg', '2026-06-16 12:46:44'),
(2, 35907, 4795, 'Ethereal Revisionism', '2', '1992-07-01', 'The evil dopplegangers infiltration of the Marvel heroes has led to mistrust and infighting...and things get worse when a Gamma bomb is detonated!All the teams have arrived at Four Freedoms Plaza for the meeting called by Mr. Fantastic. They are waiting for a few stragglers before they begin. Doom and Kang are formulating how to find the source of the energy to steal it.Back on Monster Island, Thanos meets with the Infinity Watch. In order to get information, Thanos has Pip transport them to Death\'s realm to use the Infinity Well. The Well tells them that when Adam Warlock had the Infinity Gauntlet he subconsciously split the good and evil from himself, thus reviving the Magus. The well informs them that he has great power and plans to assimilate the Earth\'s heroes with evil copies. But this is all according to the Magus\' plan and he informs Death she has trespassers.Galactus, the Silver Surfer, Nova and Doctor Strange arrive at the energy fluctuation Galactus had picked up. They are able to trace it and head off toward the true source. But Doom and Kang have placed a tracking device on Galactus\' ship and plan to follow.Hawkeye informs Spider-Man of the meeting. When Hawkeye leaves he is attacked by his doppelganger. Spider-Man stops it but is taken out by Iron Man\'s doppelganger, who now poses as Iron Man.Reed begins the meeting and tells everyone of the copy that attacked him. Wolverine enters and tells them he was attacked by a copy as well. He also tells everyone that Iron Man and Mr. Fantastic are copies. The heroes become divided and begin to battle before Daredevil also senses they are the true imposters as well. It is revealed that they are both right and the reason Reed wanted them all there before he started was to kill them and a gamma bomb is unveiled.Thanos and the Infinity Watch learn from Mole Man of the meeting on Earth. Thanos realizes this is no coincidence. They go there just in time to see the gamma bomb explode.', '35907.jpg', '2026-06-16 12:46:45'),
(3, 36025, 4795, 'Nefarious Rhapsodies', '3', '1992-08-01', 'Magus\'s plan for domination hits a deadly new level. And when the heroes track down the cause of the mayhem, they come face to face with Thanos and Warlock and the Infinity Watch. Fight!The gamma bomb has exploded, but Invisible Woman is able to contain it with a shield. Thunderstrike sends the fallout to outer space. Just then, the Magus appears through a portal with the doppelgangers of Thanos and Mr. Fantastic. They take their Iron Man double to safety. Shaman senses the portal was of sorcery.Galactus is still headed through dimensions with Dr. Strange, Nova and the Silver Surfer. They are being followed by the scheming Kang and Doctor Doom. Thanos and the Infinity Watch have reached one of the energy sources. And they continue on it\'s trail.Scarlet Witch has gathered some help for Earth\'s heroes to travel through dimensions mystically. They gather as many as they can send and leave the rest behind.Galactus is approaching his destination faster than the Magus had anticipated and he destroys Galactus\' ship. Doom and Kang are left off the trail and locate the nearest link to that dimension. They cloak when Thanos and the Infinity Watch arrive. It is here that Thanos prepares Adam Warlock for what he must do and that only he can do it, so he must.Just then they are met by Earth\'s heroes, who believe Thanos to be the cause of the events. This is all part of Magus\' grand plan and he unleashes his doppelgangers on the heroes remaining on Earth.', '36025.jpg', '2026-06-16 12:46:45'),
(4, 36152, 4795, 'Mortiferous Artifice', '4', '1992-09-01', 'The Marvel heroes are fighting a war on two fronts while the Magus schemes his way to total domination. Is the only way to save the universe...the Infinity Gauntlet?!While Earth\'s heroes battle their doppelgangers the other heroes begin battling with Thanos and the Infinity Watch. This is all according to the Magus\' plans but he has everything planned so precisely that he cannot detect the arrival of Kang and Doom. Galactus uses his great power to rebuild his ship and cause the others\' survival.Kang and Doom discover the source of the Magus\' power by breaking into his stronghold and defenses. He has five comic cubes from different dimensions. They are rigged to explode if tampered with.Galactus arrives at the battle between Earth\'s warriors and the Infinity Watch and puts them in a stasis field and transports them to his ship. Galactus uses a beam to send to Earth and destroys all of the doppelgangers. He then scans the minds of all on board and learns the truth and shares it with them. He transports Thunderstrike to Earth\'s Moon and uses his mystical hammer to maintain contact with their actuality.Thanos tells Warlock to get the gems and bring them together again. But when he does they no longer work in unison as once before. Galactus takes Gamora to have a hearing with the Living Tribunal to get the power turned back on. As he leaves the Magus makes his move and transports in and kidnaps Warlock along with the Infinity Gauntlet.They heroes remaining soon learn what Magus is doing to the universe. Every living being is in a trance and he is trying to create a new copy universe, but an evil one. They gather all their telepaths and try to awaken Earth\'s people to help them resist Magus\' takeover. Thanos meanwhile takes the Ultimate Nullifier from Galactus\' ship. But the weapon takes a noble soul to use it, and Thanos is not fit for it. He nominates Quasar to use the device.', '36152.jpg', '2026-06-16 12:46:46'),
(5, 36276, 4795, 'Psychomachia!', '5', '1992-10-01', 'The Magus is now in possession of a fully functioning Infinity Gauntlet. What chance do Adam Warlock and the Marvel heroes have now?Magus now has the Infinity Gauntlet, but it is depowered. He knows that Galactus will have it reactivated soon.Quasar is sent with the Ultimate Nullifier to take care of the Magus. He hesitates to use the device and Thanos plans to attack head on. Captain America does not like Thanos being in charge and tells him they are coming too.The Living Tribunal will not reactive the gems without the consent of Eternity. He uses Gamora to bond her to Eternity, which displeases her greatly, but is able to revive Eternity. Doctor Doom and Kang have made their move on Magus. Doom defeats the Magus and turns on Kang, defeating him as well. Doom is moments away from receiving the gauntlet when Eternity reactivates the gems. Now Magus obtains the powered gauntlet and is more than a god. He becomes used to his power much faster than Warlock or Thanos were capable of.Thanos sends the heroes to fight the revived doppelgangers, tricking them but saving their lives. He travels to meet the Magus head on when he is met by his own doppelganger, who plans to betray his master as soon as possible. They begin to battle one another.', '36276.jpg', '2026-06-16 12:46:47'),
(6, 36400, 4795, 'The Animus Engagement', '6', '1992-11-01', 'Magus triumphant! Who will win the Infinity War...and at what price?Magus now is more than a god with the Infinity Gauntlet. He know has the power to create his own universe as he sees fit.Earth\'s heroes are battling their doppelgangers and Galactus has realized the error of returning the power to the Infinity Gauntlet. Magus decides his fun is over and with the snap of his fingers he makes the doppelgangers disappear. He has horrible plans for Warlock and all the universe. He collects all those that fought against him as trophies. But he is met by Thanos, who has defeated his own doppelganger.He battles Thanos, and Thanos begins to question his mind. He distracts him long enough for Adam Warlock to escape and touch the Gauntlet. For the moment, they both control all of reality, now it is a battle of control.But the Magus has been deceived. The Gauntlet does not contain the true Reality Gem. Aware of this, Warlock is able to overpower him. Eternity and Infinity combine and it seems the universe is at an end.But Earth\'s heroes are all transported back to their homes in the blink of an eye. They all assume Warlock has the power of the gems and is now god.But back on Monster Island, Thanos and the Infinity Watch gather around a catatonic Adam Warlock. They return is soul gem in the hopes he can stay in peace. Eternity greets them and tells them that the gems placed together will never wield any power ever again, regardless of the circumstances. Thanos believes that Warlock\'s state is because he took back his evil half and his good half remains free. He claims that if ever Warlock should awaken, he may be a greater threat than he ever was. Thanos leaves to contemplate his own destiny.Meanwhile, the Magus has been trapped in Soul World. He soon realizes he is not visible to any others their and he is less than nothing.', '36400.jpg', '2026-06-16 12:46:48'),
(7, 34415, 4596, 'God', '1', '1991-07-31', 'For Thanos, the Infinity Gauntlet was the ultimate prize to be coveted above all else. With it came omnipotence. Now it\'s up to Earth\'s super heroes to make a desperate attempt to thwart this mad god\'s insane plunge into galactic self-destruction.Thanos is contemplating what is to be done now that he has the power of god. He finds that the answer is quite simple - anything he wants.Silver Surfer has crash landed in the home of Doctor Strange. He tells him the events that have transpired and hopes that it is not too late.Thanos goes to Death to seek her love and approval. With every attempt, she shows her disdain and turns her back to him. He cannot understand why she will not return his love. He creates a giant shrine to her honor, with two chairs for them to sit in an rule all of the universe. Still, she remains uninterested.He resurrects Nebula, creating a half alive half dead version of her in another attempt for Death\'s affection. She still is unimpressed. He then discovers that maybe she is spurned that he has not fulfilled his vow to destroy half the population of the universe. With one snap of his fingers, he makes it so.All over the universe half the life on any given planet disappears, human and animal alike. This is noticed widely by Spider-Man, Captain America, Hulk and SHIELD on Earth as well as by Empress S\'byll of the Kree Empire and by the Eternals on Titan. Some close to them have disappeared as well, such as Sersi, Hawkeye, Mentor and Wong.Silver Surfer senses all this death and to him it is just too much to bear.', '34415.jpg', '2026-06-16 12:53:29'),
(8, 34529, 4596, 'From Bad to Worse', '2', '1991-08-01', 'Thanos has killed half of the universe with a thought...including half of its heroes! As the remaining heroes deal with the chaos that has ensued, will the return of Adam Warlock be the universe\'s only hope?While the Avengers are cleaning up the mess from half the population disappearing, Epoch contacts Quasar the Cosmic Guardian and informs him he is needed. Epoch is with an entity that proves to be Adam Warlock.Doctor Strange is then contacted by that same entity and says he must lead the forces against Thanos. He opens his soul for Doctor Strange to see that he is no enemy. Meanwhile, Doctor Doom wishes for answers to what is happening. He attacks Strange\'s mansion and assaults Strange and the Silver Surfer. But the Surfer is in no shape to fight after his battle with Thanos. But Adam Warlock appears and tells them he has all the answers they need.Back with Death, it seems Starfox was taken by Thanos for a \'family reunion.\' Starfox tries to use his power to manipulate Thanos but it does not work and Thanos removes his mouth. He tortures them to impress Mistress Death but even this does not work. In anger, Thanos creates an incredible shockwave across the universe.On Asgard, Odin has become aware of the situation. He has gathered all the deities to discuss the problem. They all band together. They are trapped in Asgard after Thanos\' shockwave destroyed the exit.The shockwave cause much damage to Earth. The western coast of the United States falls into the ocean and a massive tsunami hits the east coast. Across the world, the landscape had changed and many places simply did not exist any longer such as Japan. Earth\'s heroes try to assess the situation and get a plan into action.', '34529.jpg', '2026-06-16 12:53:30'),
(9, 34647, 4596, 'Preparations For War', '3', '1991-09-01', 'The Earth\'s mightiest heroes assemble to take on the mad god Thanos. But can Captain America, Thor, Iron Man and others defeat someone who now controls reality itself?Earth\'s heroes have gathered and it is nearly time to begin their attack on Thanos. Doom tries to say that he should lead them but they refuse. Silver Surfer and Adam Warlock take off to another location to start the plan.They meet with Quasar and Epoch who have gathered some of the most powerful entities in the universe. Living Tribunal states that he will no cosmic crime is being committed and he will not aide them. Galactus objects to being lead by a mortal but finally agrees to go along with the plan. The Watcher also lets it be known that he will not participate.Adam Warlock and the Surfer return to lead the attack. Warlock takes Wolverine and Hulk to the side and reminds them that they are important because the goal is to kill Thanos, and they will not hold back on him.Thanos can still not get Death to fall in love with him so to spite her, he creates the object of his affection, Terraxia. He claims she will be the perfect match for him and he no longer needs Death, who leaves when this happens.Doctor Strange casts a spell allowing everyone to breathe in space and uses the Eye of Agamatto to transport them to Thanos. Silver Surfer and Adam Warlock have plans of their own. He explains that most of them will not survive and they are merely a diversion.', '34647.jpg', '2026-06-16 12:53:31'),
(10, 34880, 4596, 'Astral Conflagration', '5', '1991-11-01', 'The heroes have fallen and its up to Galactus and the other astral gods of the universe to step into the fray. And finally, the Infinity Gauntlet is wrested away from Thanos...but by whom will shock you!Thanos is under attack by several of the most powerful forces in the universe. The heavens shake with power. Billions die just for the battle to take place. It creates an inter-dimensional distortion allowing Annihilus and his forces to invade Earth. But right now, this is the least of the universe\'s troubles.One by one, Thanos defeats the entities Chronos, Master Order and Lord Chaos, Galactus, Stranger, Epoch, Mistress Love and Master Hate. Mephisto sees this as an oppurtunity and tries to take the gauntlet from Thanos but fails. Thanos is soon disheartened in the fact that Death has joined the assault against him. He entraps all the entities when he is faced with Eternity.The battle turns out to be the war for control of this reality. Thanos ends up the victor, turning into a cosmic being not unlike Eternity. But this leaves his physical form temporarily unprotected. Nebula takes the Infinity Gauntlet for herself.Doctor Strange, Warlock and Silver Surfer regroup on Earth. They devise a new plan.Now it is Nebula who possesses the greatest power in all the universe, and she is very disoriented by her recent ordeals. She seeks revenge and transports Thanos and Terraxia into space, where Terraxia dies because Thanos had not thought to make her be able to breath in space. He is then transported by Warlock to Earth so they can speak.In private, Warlock reveals that while in the soul gem he was able to learn everything about Thanos including his thoughts, dreams and secrets. He tells him that he always seeks ultimate power but does not believe he deserves it and subconsciously supplied his own defeat whenever he achieved it. Thanos agrees to help retrieve the gauntlet from Nebula.Doctor Stange has managed to gather some of the heroes who were sent to other realms by Thanos. They all attack Nebula, but even with her lack of experience the gems give her incredible power.', '34880.jpg', '2026-06-16 12:53:32'),
(11, 35000, 4596, 'The Final Confrontation', '6', '1991-12-01', 'It\'s the final battle for the Infinity Gauntlet, and with it...control of the universe. Who\'s left standing and what becomes of the universe in the conclusion to this blockbuster Marvel event.Thanos and the remaining heroes face off against Nebula who now has possession of the Infinity Gauntlet. She uses the power she now has to transform the universe back to the way it was twenty four hours ago. All of Thanos\' death and destruction has been undone. Some of those returning to life have no recollection of the events that have transpired, some feel like something is not right and others remember the events in full detail. She herself is transformed back into the horrible creature Thanos turned her into.But as Warlock reahes for the gaunlet she regains her power. The cosmic beings unleash their fury upon her. Warlock has a plan now that the gauntlet is in less experienced hands. He and the Silver Surfer return to the Soul World where Warlock becomes at one with the universe of the soul gem.Nebula manages to turn the cosmic beings into stone. Warlock manages to reach out to the other gems and the gauntlet separates from Nebula. It is now an all out battle to retrieve the fallen gauntlet and Earth\'s heroes stop Thanos from obtaining it again. The guantlet comes into the hands of Adam Warlock.Rather than face imprisonment, Thanos chooses to blow himself up with a thermal nuclear device. Adam Warlock claims himself the true possessor of the Infinity Gems and that he will be a just god. He transports himself, Gamora and Pip to two months in the future on a unnamed planet. They go to visit Thanos, who claims that three failures at such a scope are enough for him and he will live out a quiet life to reflect on the lessons of the past.', '35000.jpg', '2026-06-16 12:53:32'),
(12, 34764, 4596, 'Cosmic Battle on the Edge of the Universe', '4', '1991-10-01', 'It\'s Captain America, Iron Man and the other greatest champions of Earth taking on Thanos, who possesses the Infinity Gauntlet. And the heroes...lose?!? But what plan do Adam Warlock and the Silver Surfer have to defeat Thanos?Earth\'s heroes have descended upon Thanos is a attempt to defeat his maniacal plots. They are simply no match for Thanos and the Infinity Gauntlet. Mephisto points out that Adam Warlock and Silver Surfer are not engaging in the battle.Thanos realizes, with the manipulation of Mephisto, that he cannot impress Death with the full abilities of the gauntlet. He must show courage in the face of his enemies. He turns off all the gems power except the Power Gem to even the playing field. He still has limitless power just no cosmic awareness.Even in the limited state he has placed himself in Earth\'s heroes are no match for him but have a better chance. The might of Hulk and Drax are still not enough. Under Captain America\'s leadership they seem to have some hope.The Surfer is growing impatient. Warlock has not yet revealed his plans for the Surfer, so that Thanos cannot steal those plans from his mind.Thanos dispatches of She Hulk and Namor by encasing them in stone. He blasts Doctor Doom, whose only goal is to get the Infinity Gauntlet for himself. Thunderstrike puts up a challenge for Thanos but it\'s still not enough. Wolverine gets the drop on Thanos and buries his claws into Thanos\' chest, but even this proves not to phase him. One by one they all fall to Thanos. Thunderstrike is turned into glass and broken, Cyclops is suffocated and he turns Nova into toy blocks. Spider-Man and Iron Man are killed by Terraxia. Quasar is defeated under Thanos\' power.The final combatant is Captain America who still stands to fight. Thanos shatteres his shield in one blow. As he goes to finish him off, Warlock and the Surfer make their move. But Thanos is aware of them and the Surfer cannot manage to take the gauntlet from him.Now the big guns arrive to battle, the cosmic entities of the universe.', '34764.jpg', '2026-06-16 12:53:33'),
(13, 108552, 5019, 'Epiphany', '1', '1993-06-01', 'The Goddess has half of the Marvel Universe on her side, but if she doesn\'t get her way, she\'s ready to destroy all of it!Adam Warlock senses something wrong with the cosmos. He uses the Eternal Orb to contact Eternity. Eternity tells him that he has sensed it as well, but it is of no concern to him. He leaves the void and is soon met by the Goddess. At first he does not realize who she is and she attacks him.She shows a symbol to all the religious and faithful heroes of Earth to bring them hope. For the past time she has been gathering cosmic cubes from myriad dimensions. She has collected 10 and begins to use their immense power. She transfers a sense of enlightment to the residents of Earth and brings to contact those faithful of her choosing.She tells them all that she is for good for all beings to live in peace and harmony, in a world with no sorrow and pain. She can be for the collective good of humanity if they will all serve her. Manipulating them proved easy and every single person she contacted agrees to join her cause. She opens a trans-dimensional portal to bring them all to her.The disappearances cause much confusion with the heroes on Earth. All the teams get together and compile a list of those missing and realize the connection.The Goddess uses her power to create a new planet which she calls Paradise Omega. She brings the heroes to her and creates a massive cathedral.', '108552.jpg', '2026-06-16 12:53:47'),
(14, 37517, 5019, 'Enlightenment!', '2', '1993-07-01', 'The Fantastic Four, the Avengers, the Defenders, the X-Men, X-Factor, X-Force, the New Warriors and more are turned against each other in the Goddess\' cosmic inquisition!The remaining members of the Infinity Watch arrive at the Avengers Mansion. They begin to fight because of past incidences but soon realize they are on the same side. Mister Fantastic reveals who he believes is behind this....the Goddess.Back on Paradise Omega, Goddess reveals that she actually has 30 Cosmic Cubes and forged them into a single force which she called the Cosmic Egg. Through this she will create a cosmic group consciousness and make peace in the universe. The heroes she recruited will protect her while she is in the vulnerable state of doing this.Back at Avengers Mansion, Aurora begins to change to her other personality, which is very religious. She is automatically drawn to the Goddess. Mr. Fantastic, Iron Man and Vision all accompany her to Paradise Omega. Goddess reveals herself to them and tells them her purpose. She becomes angered when they question her and banishes the non-believers from the planet. They end up back on Earth without any trace of their previous location.Back on Earth, they truly wonder of her true intents and their is question of whether she should even be treated as a villian. They come to the conclusion that the Goddess cannot remain playing with the forces of the universe. Meanwhile, Adam Warlock turns out to have been exiled by Goddess to a distant dimension. He begins to make his journey to find the owner of the reality gem.', '37517.jpg', '2026-06-16 12:53:48'),
(15, 37658, 5019, 'The Damned', '3', '1993-08-01', 'Adam Warlock must search for answers within while he does battle with his greatest enemy...himself!Mr. Fantastic, Iron Man and Vision travel to the Moon to peak with the Watcher in order to better understand their situation. The Watcher will not tell them that he wishes the Goddess to be stopped, but this is truly what he would like to happen. He transports them back to Avengers Mansion.Goddess has successfully brainwashed the universe into believing her views. All wars and violence stop across the universe. It is still debated among the remaining heroes if she should be stopped, the main opponent of attack being Professor X. He contacts Moondragon telepathically. She rejects the Earth\'s heroes offers for peace and attacks Professor X mentally, leaving him possibly brain damaged. The heroes no longer disagree over their course of action.Meanwhile, Warlock has sought out Thanos, for they are the ones who are best suited to stop the Goddess. They are then met by Mephisto, who claims that it is in his best interests to assist them and that he knows certain secrets to the Cosmic Cubes. The only thing he wants in return for his help is one of the cubes.Witnessing all these events causes the Goddess to turn from defense to offense. She begins to plan her attack. When the Silver Surfer hears the orders to defeat his friends he refuses and thus breaks her control over him. He is immediately attacked by his companion, Firelord.Earth\'s heroes begin their travel to the Goddess for an assault. With his new found knowledge of the Cosmic Cubes, Pip makes his move. He teleports to the Goddess, and once in contact with the Cosmic Egg he turnes her into salt. He plans to become King of the Universe.', '37658.jpg', '2026-06-16 12:53:49'),
(16, 37797, 5019, 'Mortal Sins', '4', '1993-09-01', 'Earth\'s gathered champions are rocketing toward Paradise Omega, home of Adam Warlock\'s virtuous female doppleganger, the Goddess. Has she been slain? What is next for the heroes?Thanos has begun his preparations to defeat the Goddess. He enlists the help pf the Silver Surfer. Thanos and Adam Warlock have created a plan. Thanos wears Adam Warlock\'s soul gem so that Adam may travel to the Soul World to seek assistance from an unlikely ally. Thanos also needs the assistance of Professor X\'s mind powers. The Surfer is to create a distraction.They inform Earth\'s heroes about their distraction they have set up. They reluctantly agree to follow Thanos\' plan. Thanos teleports Professor X to them, who is in a catatonic state, to use his mind personally. The Surfer sets off for the distraction while Thanos retrieves a device known as Dreadnaught 666, which he had created long ago to take over the universe.Meanwhile, the Goddess has sent her Holt Guards to deal with the heroes at Avengers Mansion. They are in utter shock when they find no one there, meaning they are already on their way to Paradise Omega.The Surfer is now attempting something he never has before, to take in massive amounts of the sun\'s energy. He may possibly die from this attempt. He swells in size and energy and is guided to Paradise Omega by Hulk and Drax. The Holy Guard is defenseless against him and he destroys the moon surrounding the planet.The distraction in place, Earth\'s heroes pull their Quinjets in, which are quickly destroyed by Thor. Hulk and Drax begin to battle Thor. The remaining heroes all use escape pods to land on the planet, where the members of the Holy Guard are waiting. Thanos\' plan requires one pod to remain in orbit so he can maintain long range scanning.Meanwhile, Warlock has reached the person he seeks.......the Magus.', '37797.jpg', '2026-06-16 12:53:49'),
(17, 37950, 5019, 'Holy War!', '5', '1993-10-01', 'After teaming up with Drax The Destroyer to fight Thor, The Hulk is abandoned and left to drift in space above Paradise Omega. Can the Jade Giant make it back to the surface in time to join the action?The battle of the heroes has begun on the surface of Paradise Omega. As the heroes crash land on the planet they are all assaulted by the Holy Guard.The Goddess\' control over the Holy Guard is great, making most lose their character to battle for her and people who never kill are now trying to end their opponents lives. Almost all the heroes are caught off guard by the Holy Guard and their surprise attacks.Adam Warlock has finished his business in the Soul World. Thanos transports him to an unknown location.Thanos fires the Dreadnaught 666 upon the planets surface. he then uses the power of Professor X and the Soul Gem in an attempt to breach the Cosmic Egg and reach the Goddess. He is unable to penetrate her defenses and fails to stop her from fulfilling the final rapture, destroying the entire universe.', '37950.jpg', '2026-06-16 12:53:50'),
(18, 38098, 5019, 'Rapture', '6', '1993-11-01', 'The conclusion is here! One by one, stars in the Milky Way galaxy have begun to explode. Can the heroes save the universe? What role will Thanos come to play in the final act?The Goddess has achieved her goal. The rapture has begun and every star in the universe explodes, killing all life. This is experienced by all as a flash in the sky followed by intense heat and flames, disintegrating all. But this is merely an elaborate illusion created by Adam Warlock. He had been lurking in the Goddess\' subconscious and used the power of the Cosmic Egg to wish the illusion, thus stopping her from actually doing so.The Holy Guard has witnessed this event and are now aware of their Goddess\' true intent, they no longer serve her. Goddess no longer has the ability to carry out her plan but thinks she still has great power in the Egg and can continue her plan in stages.She is then attacked by Warlock and Thanos. Since Warlock is in his spiritual form she is forced to leave her own body to battle him. Thanos and Professor X join in the battle on the spiritual plane. Goddess is outmatched and attempts to return to the Cosmic Egg for protection. But Warlock has beaten her to it, bonding himself within the Cosmic Egg. As part of the plan, Thanos uses the Soul Gem to attack.Thanos remains with the Cosmic Egg. Warlock returns, believing that Thanos means to take the power for himself. But Thanos\' word is good and he has already set the Egg to break down to the molecular level and explode, taking the planet Paradise Omega with it. He has already arranged transportation and all of Earth\'s heroes are returned to Earth and he and Warlock are brought to Thanos\' ship.Back on Earth, the heroes blame Warlock and the Infinity Watch for all that has transpired. But Warlock is through fighting and teleports the team back to Monster Island. All those across the universe under Goddess\' control remember nothing, while the heroes remember all. Most feel used and guilty.Goddess has been trapped inside Soul World. She comes across her evil counterpart, the Magus. She attacks him, but even they cannot touch one another. They are both merely ghosts, unable to interact.Mephisto comes to Thanos for his reward for the information about the Cosmic Cubes. Thanos gives him his cube, but informs him that it has no power and Mephisto should have specified that. Mephisto is very angry and Thanos reminds him that even devils should beware a bargain with Thanos of Titan.', '38098.jpg', '2026-06-16 12:53:50'),
(19, 1079226, 161311, NULL, '1', '2025-01-01', 'THE WATCHTOWER RISES!The Justice League is back and bigger than ever! In the wake of Absolute Power and the DC All In Special, Darkseid\'s death has triggered a massive power vacuum in the DCU, and Superman, Batman, and Wonder Woman must unite like never before and expand the Justice League to encompass every hero championing the forces of good in the face of incredible evil!As our heroes work to uncover the mystery of the dark lord\'s successor, Ray Palmer\'s Atom Project triggers a race between hero and villain to control the fate of metahuman abilities on planet Earth, which threatens to destroy everything the League has built.Worlds will live, worlds will die, and a surprise is waiting in store on the last page...Do not miss the dawn of the new era of justice--it all begins here!', '1079226.jpg', '2026-06-16 14:00:13'),
(20, 1085934, 161311, NULL, '2', '2025-02-01', 'INTO THE NEST OF THE PARADEMON!As the Justice League grapples with the ramifications of the Atom Project, a strange alert brings the team to the jungles of South America...and a horrific discovery. Can the elite Justice League strike force save a village from total slaughter?Plus...the mystery of the Darkseid heir deepens, and dissension in the ranks forces Batman\'s hand.', '1085934.jpg', '2026-06-16 14:00:14'),
(21, 1096900, 161311, NULL, '4', '2025-04-01', 'THE WORLD IS ABLAZE!As the Justice League reels from the horrors in?icted by the Parademon Horde, a new threat arises across space...and time.With the team racing to put out multiple villainous fires at once, the mystery surrounding the Martian Manhunter deepens, and the techno-terrorist group Inferno makes its boldest move yet...as their secret leader is revealed!', '1096900.jpg', '2026-06-16 14:00:15'),
(22, 1100119, 161311, NULL, '5', '2025-05-01', 'THE CLOCK IS TICKING!The keys to the mysterious superterrorist organization Inferno begin to be revealed as the Justice League realizes it\'s an intergalactic threat led by one of their oldest foes!Time is running out to save the world with the help of...the Legion of Doom?', '1100119.jpg', '2026-06-16 14:00:16'),
(23, 1090821, 161311, NULL, '3', '2025-03-01', 'ENTER: THE ATOM PROJECT!The global terrorist group Inferno has taken its next step toward world dominance with an ecological disaster! Will Superman, Wonder Woman, and the others have to sacrifice the life of Swamp Thing?Plus: Plastic Man and Beast Boy work with The Atom Project to fix their scrambled superpowers!', '1090821.jpg', '2026-06-16 14:00:16'),
(24, 1106031, 161311, 'Chapter Two: Partners and Puppets', '6', '2025-06-01', '\"WE ARE YESTERDAY\" PART 2!The horrific hidden identity of the Inferno cult has been revealed, and the Legion of Doom is here to take no prisoners! But something is...off about these ferocious foes of the Justice League. They fight without wisdom, without experience--but with a secret edge?!Buckle up for the biggest clash in Justice League history as Lex Luthor, the Joker, Cheetah, Grodd, and all the vile villains of the original Legion of Doom arrive to terrorize tomorrow!', '1106031.jpg', '2026-06-16 14:00:17'),
(25, 1112944, 161311, 'Chapter Five: Rise of Gorilla God', '7', '2025-07-01', '\"WE ARE YESTERDAY\" PART FIVE (OF SIX)Unlimited no more?!The Justice League is fractured throughout time as the Legion of Doom achieves the unthinkable...the siege of the Watchtower! As Gorilla Grodd\'s attack intensifies, it\'ll be up to one hero to call in the cavalry, and it is not who you think!Destruction, redemption, and a cavalcade of chaos culminate in this penultimate chapter of \"We Are Yesterday,\" a special crossover with Batman/Superman: World\'s Finest!', '1112944.jpg', '2026-06-16 14:00:18'),
(26, 1116613, 161311, 'Chapter 6: All Hail Grodd', '8', '2025-08-01', 'THE EPIC CROSSOVER FINALE!WE ARE YESTERDAY PART six (of six)The Justice League Unlimited stands alone as a wave of chronal mayhem crashes on the shores of their Watchtower base...but all hope is not yet lost! A last-minute mayday may just be enough to stem the tide...but for how long?Plus: the fate of Gorilla Grodd and a significant step toward the next major DCU event in the grand finale of We Are Yesterday, a special crossover with Batman/Superman: World\'s Finest!', '1116613.jpg', '2026-06-16 14:00:19'),
(27, 1121446, 161311, 'We Are Yesterday, Epilogue', '9', '2025-09-01', 'APOKOLIPS IS ON THE HORIZON...AND NO ONE IS SAFE!In the aftermath of the devastating events of We Are Yesterday, the Justice League is reeling from the Legion of Doom\'s wrathful rampage! But no time to rest for the protectors of our planet, as the secrets unlocked during their time-crossed duel have brought our heroes face-to-face with a mysterious Quantum Quorum, who has reason to fear that all of creation might soon come to an end.It\'s the next big step on the path of the All In saga in the epic epilogue to We Are Yesterday!', '1121446.jpg', '2026-06-16 14:00:19'),
(28, 1128924, 161311, NULL, '10', '2025-10-01', 'THE MARCH TO APOKOLIPS CONTINUES!The Quantum Quorum of time-traveler refugees has issued its warning to the Justice League: Apokolips is coming. But just as Jor-El\'s cries fell on the deaf ears of Krypton, will the people of Earth be prepared to take drastic steps to save their world?It all comes to a head as the chaos continues, and one Leaguer is put to the ultimate test!', '1128924.jpg', '2026-06-16 14:00:20'),
(29, 1134437, 161311, NULL, '11', '2025-11-01', 'EVE OF DESTRUCTION!Something strange is happening on Earth...and the Justice League is powerless to stop it!It began with a horrific volcanic eruption in the heart of the villainstronghold nation of Zandia...and quickly spread to Count Vertigo\'s neighboring kingdom of Vlatava. The parademons that the JLU faced before are only the tip of the iceberg--the entire planet is now in peril! What signal does this point to in the Quantum Quorum\'s tournament?Do not miss this pivotal next chapter in the All In saga!', '1134437.jpg', '2026-06-16 14:00:20'),
(30, 1139070, 161311, 'The Terrific Ten, Part One', '12', '2025-12-01', 'THE TERRIFIC TEN ARE ON A DO-OR-DIE MISSION!As the tournament begins and the transformation of Earth GROWS MORE DIRE, a strange signal is detected at the heart of darkness at the core of the planet--a Terrifictech device?! Michael Holt must put together an elite Justice League squad of the most powerful time-displaced heroes from \"We Are Yesterday\" to unravel the mystery--and this mission is a oneway ticket.Get ready for a mission to hell and back in this epic tie-in to the DC K.O. event!', '1139070.jpg', '2026-06-16 14:00:21'),
(31, 1145534, 161311, 'The Terrific Ten, Part Two', '13', '2026-01-01', 'THE TERRIFIC TEN ON A DO-OR-DIE MISSION!The Earth opens up to swallow the JLU! Parademons swarm the Watchtower! The Time Trapper lies near-dead at Metamorpho’s feet?Any one of these things is catastrophic, but all three simultaneously? Can Armageddon be far behind?', '1145534.jpg', '2026-06-16 14:00:22'),
(32, 1149808, 161311, 'The Terrific Ten, Part Three', '14', '2026-02-01', 'THE FALL OF MR. TERRIFIC!As the time-displaced heroes fight for their lives against the towering might of the demonic neron, Mr. Terrific descends into the cold vacuum of space! But this time it’s by his own design...can Michael Holt team with the rogue agent behind Justice League Red to purge all emotion and coldly calculate the fate of humanity?Grab the tissues, everybody—this issue’s about to break some hearts!', '1149808.jpg', '2026-06-16 14:00:23'),
(33, 1154072, 161311, 'The Terrific Ten, Part Four', '15', '2026-03-01', 'THE FALL OF MR. TERRIFIC!The plot Neron set in motion is revealed at last—and the consequences will spell the end of heroism on planet Earth!As powered-up villains ravage what\'s left of the planet, the Terrific Ten is whittled down to a handful of survivors who can\'t possibly withstand the fires of Hell!', '1154072.jpg', '2026-06-16 14:00:24'),
(34, 1156947, 161311, 'The Terrific Ten, Part Five', '16', '2026-04-01', 'MR. TERRIFIC IN HELL!As the Earth’s mass evacuation continues, in order to save millions, Mr. Terrific and (what’s left of) his Terrific Ten must march through Hell to confront its ultimate ruler, Neron—and expose the traitor in their midst!', '1156947.jpg', '2026-06-16 14:00:24'),
(35, 1160298, 161311, 'Aftermath: Part One', '17', '2026-05-01', 'SUPER VILLAINS ON THE JLU?!In the aftermath of DC K.O., the Justice League has to work even harder to protect mankind—and that means it’s time for new blood to face new challenges!Who will join Wonder Woman and Batman to lead the new JLU?', '1160298.jpg', '2026-06-16 14:00:25'),
(36, 1163579, 161311, 'Aftermath: Part Two', '18', '2026-06-01', 'DC K.O. AFTERMATH!Unlimited?Definitely.United?Hardly.The strongest voices in the Justice League have begun granting amnesty—and membership—to super-villains, and mutiny is brewing!Plus: Guy Gardner leads a mission that will have startling consequences for the entire galaxy!', '1163579.jpg', '2026-06-16 14:00:26'),
(37, 1168257, 161311, 'Amnesty: Part One', '19', '2026-07-01', 'AT THE DAWN OF A NEW ERA…IS A SURPRISE RETURN!As the Leaguers struggle on Earth to fulfill the impossible missions mandated by the Heart of Apokolips and control the villains given amnesty, the space-faring JLU members come face-to-face with Brainiac Queen—back from the pages of Absolute Power and deadlier than ever!', '1168257.jpg', '2026-06-16 14:00:27'),
(38, 1111133, 164402, 'In the Blink of an Eye', '1', '2025-07-01', 'DAN SLOTT AND RAFAEL ALBUQUERQUE TAKE THE MAN OF STEEL TO NEW HEIGHTS!The summer of Superman heats up with a brand-new ongoing series taking the DCU by storm!When an asteroid the size of Metropolis hurtles toward collision with planet Earth, the Justice League dispatches Superman to avert the crisis--but a sinister threat lurks within that will change the world like never before, and this danger glows green. The Last Son of Krypton must risk everything to save his adopted home, the very home which now tries to kill him, from complete destruction!The Man of Steel is poised to fly like never before in this new cornerstone series, brought to life by the dazzling DC debut of writer Dan Slott (The Superior Spider-Man) and renowned artist Rafael Albuquerque (All-Star Batman)!', '1111133.jpg', '2026-06-16 14:36:53'),
(39, 1116242, 164402, 'The Gold Exchange', '2', '2025-08-01', 'ENTER: THE KRYPTO-KNIGHTS!Superman faces a brave new world--radical change sweeps the globe, and the ramifications of the Kryptonite asteroid reach a fever pitch! The epicenter of this new Kryptonite power reverberates from the Emerald City--a nation-state positioned atop a massive stockpile of this incredible natural resource and ruled with an iron fist by the Kryptonite King and his dreaded Krypto-Knights!Can Superman hope to continue his fight for truth and justice with Kryptonite flooding the streets with danger?Plus: the Daily Planet\'s expansion continues, and Gorilla City\'s representative joins the team!', '1116242.jpg', '2026-06-16 14:36:54'),
(40, 1120426, 164402, 'Good Boy', '3', '2025-09-01', 'TOYMAN RUNS ON...KRYPTONITE?!When the towering terror of Toyman once again terrorizes the streets of Metropolis, Superman and Krypto are on hand to save the day--but in this new world of Kryptonite, even the murdering machinations of Toyman are supercharged with Superman\'s Achilles\' heel! It\'s a twisted turn of events as the Man of Steel uncovers a game-changing new use for this deadly substance, and it has the capacity to...save the world?Plus: Jimmy Olsen moves to Gotham (careful, Jimmy), Tee-Nah of Gorilla City tries to fix Steve Lombard\'s laptop (careful, Tee-Nah), and Intergang floods the streets--all in the latest installment of the emerald epic you\'ll have to read to believe!', '1120426.jpg', '2026-06-16 14:36:55'),
(41, 1127024, 164402, 'Look Up In The Sky! It\'s A Bat!', '4', '2025-10-01', 'NIGHT OF THE SUPERMAN-BAT!It\'s a big day for Jimmy Olsen as he sets up shop in the new Gotham City branch of the Daily Planet. His first assignment: learn what has turned Superman into an uncontrollable monster--and why. Do the combined forces of the heroes of Gotham even stand a chance against this version of the Man of Steel? And what is Bruce Wayne doing a continent away in Emerald City?All this, and back in Smallville, Jon Kent begins training for the ultimate challenge--a way to combat the deadly effects of Kryptonite. Guest-starring: the Birds of Prey.', '1127024.jpg', '2026-06-16 14:36:55'),
(42, 1138105, 164402, 'Into the Heart of the Kryptonite Kingdom, Part Two: King\'s Ransom', '6', '2025-12-01', 'TO SAVE A KINGDOM!The deadly special forces of the Kobra Kult have infiltrated the Kryptonite Kingdom, and now this sovereign nation’s only hope for survival is the man whose very presence in the kingdom is a death sentence for him: Superman!An unholy alliance is struck, a Kryptonite King is crowned, and the Vipers strike in this epic next installment of Superman Unlimited!', '1138105.jpg', '2026-06-16 14:36:56'),
(43, 1134440, 164402, 'Into the Heart Kryptonite Kingdom! Part One of Two: Man of Gold', '5', '2025-11-01', 'INTO THE BELLY OF THE BEAST!When the Kryptonite Kingdom faces a deadly terrorist attack from the Kobra Kult...this looks like a job for any hero other than Superman.The nation-state of El Caldero has the highest concentration of Kryptonite on the planet. It is literally the last place on Earth Superman should go, but when its people cry out for help, of course the Man of Steel will answer the call. Because he\'s Superman.Part one of the first multipart adventure in Superman Unlimited.', '1134440.jpg', '2026-06-16 14:36:57'),
(44, 1144085, 164402, 'This Looks Like a Job...', '7', '2026-01-01', 'JON KENT TO THE RESCUE!There’s a monster in the fields of Kansas...and only Jon Kent can help save the farm! With Smallville under siege from the supernatural, the new Steelworks facility constructed in Superman’s hometown may be the solution...or is it the cause?Join Steel, Lana Lang, Ma, Pa, and good ol’ Pete Ross as our heroes work to save the heartland from horror!', '1144085.jpg', '2026-06-16 14:36:57'),
(45, 1148761, 164402, 'Rocketed from Earth', '8', '2026-02-01', 'GUEST STARRING GREEN LANTERN GUY GARDNER!Thanks to Toyman’s Kryptonite generators, Earth is finally ready to mass-produce commercial faster-than-light rockets...for the planet’s richest one percent. And who wouldn’t want Earth’s multibillionaires traveling among the stars? Everyone. Every single being in the entire cosmos.It’s up to Superman and special guest star Guy Gardner to save the launch of Simon Stagg’s new space yacht from one of the deadliest cosmic beings in the galaxy!', '1148761.jpg', '2026-06-16 14:36:58'),
(46, 1153495, 164402, 'Die Laughing, Part 1', '9', '2026-03-01', 'MURDER IN THE MAYOR\'S MANSION!Mayor Perry White has seen some major threats impact Metropolis over the years—but even the bulldog of city hall is unprepared for a murderous rampage by...the all-new Prankster?!Superman\'s now in a race against time to save his friend from a truly sick and twisted death! Do not miss this one. This is the story that will redefine the Prankster as one of Superman\'s top-tier rogues. Don\'t say we didn\'t warn you.Plus: Daily Planet reporter Jon Kent learns a life-changing secret, the Kryptonite Kingdom receives a strange visitor, and Steelworks sees some crazy $%^^%&amp; on the horizon in Die Laughing: Part 1.', '1153495.jpg', '2026-06-16 14:36:59'),
(47, 1156313, 164402, 'Die Laughing, Part 2', '10', '2026-04-01', 'It’s said that the sun is always shining on the city of Metropolis…but lately a shad-ow has fallen. This shadow has a name, and it’s on the lips of the terrified citizens of the city of tomorrow tonight…it’s the horrible new form of the Prankster! The all-new, mysterious Prankster was the deadliest assassin in the DC Universe, some-one with a perfect record of kills…until Superman saved one of his victims. And that makes the Man of Tomorrow his next target.', '1156313.jpg', '2026-06-16 14:36:59'),
(48, 1159697, 164402, 'Fallen Son', '11', '2026-05-01', 'THE FINAL MOMENTS OF JON KENT!What’s worse than a fifth-dimensional imp?A fourth dimensional demon!Jon Kent faces off against his greatest archenemy, a terrifying time-bending foe who’s attacking him years before they’ve ever met! Witness the final moments of Jon Kent, Superman.Also in this issue: Superboy?!No. Really?Wait. What?!Yeah. You’d better not miss this one.Call your retailer now.Reserve your copy now.Don’t wait.Go. Call. Now!', '1159697.jpg', '2026-06-16 14:37:00'),
(49, 1163061, 164402, 'Besides Myself', '12', '2026-06-01', 'REIGN OF THE SUPERBOYS HEATS UP!Little (super) boy lost!Tomorrow Man has a shocking surprise for Lois Lane. His name is Jon Kent, and he\'s just a little boy.The Reign of the Superboys continues!(And, possibly...the reign of a super...monkey?)', '1163061.jpg', '2026-06-16 14:37:00'),
(50, 1167193, 164402, 'Primal Fears', '13', '2026-07-01', 'REIGN OF THE SUPERBOYS CONTINUES!One word should fill you with fear: Metropolis! Two syllables should have you running for your lives! Beware the wrath of Beppo! The power of a Kryptonian with the mind of an angry primal beast!Can Tomorrow Man and Superboy even hope to stop the Mad Mammal of Might...Super Monkey?!The reign of the Superboys continues...as Jon Kent\'s life takes a surprising turn that super-fans won\'t want to miss!', '1167193.jpg', '2026-06-16 14:37:00');

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
  `last_sync` datetime DEFAULT NULL,
  `country` varchar(10) DEFAULT 'US'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `publishers`
--

INSERT INTO `publishers` (`id`, `publisher_id`, `name`, `logo`, `actif`, `last_sync`, `country`) VALUES
(1, 31, 'Marvel', 'publisher_31.gif', 1, '2026-06-12 22:30:55', 'US'),
(2, 10, 'DC Comics', 'publisher_10.jpg', 1, '2026-06-12 22:31:19', 'US'),
(3, 4788, 'Urban Comics', 'publisher_4788.jpeg', 1, '2026-06-12 22:32:18', 'FR'),
(4, 2245, 'Panini France', 'publisher_2245.jpg', 1, '2026-06-12 22:37:17', 'FR'),
(5, 2923, 'Delcourt', 'publisher_2923.jpg', 1, '2026-06-12 22:37:40', 'FR'),
(6, 513, 'Image', 'publisher_513.png', 1, '2026-06-12 22:38:27', 'US'),
(7, 364, 'Dark Horse Comics', 'publisher_364.jpg', 1, '2026-06-13 12:10:08', 'US'),
(8, 2932, 'Le Téméraire', 'publisher_2932.png', 1, '2026-06-13 13:17:19', 'FR'),
(9, 2579, 'Paperback - Casterman', 'publisher_2579.jpg', 1, '2026-06-13 14:49:43', 'FR'),
(10, 1133, 'Semic', 'publisher_1133.jpg', 1, '2026-06-13 14:51:19', 'FR'),
(11, 1190, 'IDW Publishing', 'publisher_1190.jpg', 0, '2026-06-16 07:39:19', 'US'),
(12, 708, 'WildStorm Productions', 'publisher_708.jpg', 1, '2026-06-16 09:28:15', 'US');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `series`
--

INSERT INTO `series` (`id`, `series_id`, `name`, `start_year`, `count_of_issues`, `publisher_id`, `logo`, `actif`, `last_sync`) VALUES
(3, 4596, 'The Infinity Gauntlet', 1991, 6, 31, '4596.jpg', 1, '2026-06-15 21:46:24'),
(5, 4795, 'The Infinity War', 1992, 6, 31, '4795.jpg', 1, '2026-06-15 21:47:57'),
(6, 5019, 'The Infinity Crusade', 1993, 6, 31, '5019.jpg', 1, '2026-06-15 22:44:52'),
(7, 161311, 'Justice League Unlimited', 2025, 19, 10, '161311.jpg', 1, '2026-06-16 11:01:37'),
(8, 164402, 'Superman Unlimited', 2025, 13, 10, '164402.jpg', 1, '2026-06-16 11:01:38'),
(9, 18058, 'Detective Comics', 1937, 883, 10, '18058.jpg', 1, '2026-06-16 18:13:48'),
(10, 18005, 'Action Comics', 1938, 864, 10, '18005.jpg', 1, '2026-06-16 18:13:50'),
(11, 91098, 'Detective Comics', 2016, 176, 10, '91098.jpg', 1, '2026-06-16 18:13:50'),
(12, 42563, 'Action Comics', 2011, 57, 10, '42563.jpg', 1, '2026-06-16 18:13:51'),
(13, 91078, 'Action Comics', 2016, 143, 10, '91078.jpg', 1, '2026-06-16 18:13:52'),
(14, 42594, 'Detective Comics', 2011, 57, 10, '42594.jpg', 1, '2026-06-16 18:13:53');

-- --------------------------------------------------------

--
-- Structure de la table `tomes`
--

CREATE TABLE `tomes` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `series_id` int(10) UNSIGNED DEFAULT NULL,
  `universe_id` int(10) UNSIGNED DEFAULT NULL,
  `collection_id` int(10) UNSIGNED DEFAULT NULL,
  `tome_number` int(11) DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `page_count` int(11) DEFAULT NULL,
  `publication_date` date DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `is_owned` tinyint(1) DEFAULT 0,
  `is_wanted` tinyint(1) DEFAULT 0,
  `is_read` tinyint(1) DEFAULT 0,
  `price_original` decimal(10,2) DEFAULT NULL,
  `publisher_vf_id` int(10) UNSIGNED DEFAULT NULL,
  `publisher_vo_id` int(10) UNSIGNED DEFAULT NULL,
  `format_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tome_issues`
--

CREATE TABLE `tome_issues` (
  `tome_id` int(10) UNSIGNED NOT NULL,
  `issue_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `universes`
--

CREATE TABLE `universes` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Index pour la table `collections`
--
ALTER TABLE `collections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_collections_publisher` (`publisher_id`);

--
-- Index pour la table `formats`
--
ALTER TABLE `formats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Index pour la table `issues`
--
ALTER TABLE `issues`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `issue_id` (`issue_id`),
  ADD KEY `series_id` (`series_id`);

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
  ADD UNIQUE KEY `unique_series_id` (`series_id`),
  ADD KEY `fk_series_publisher` (`publisher_id`);

--
-- Index pour la table `tomes`
--
ALTER TABLE `tomes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `universe_id` (`universe_id`),
  ADD KEY `collection_id` (`collection_id`),
  ADD KEY `series_id` (`series_id`),
  ADD KEY `fk_tomes_publisher_vf` (`publisher_vf_id`),
  ADD KEY `fk_tomes_publisher_vo` (`publisher_vo_id`),
  ADD KEY `fk_tomes_format` (`format_id`);

--
-- Index pour la table `tome_issues`
--
ALTER TABLE `tome_issues`
  ADD PRIMARY KEY (`tome_id`,`issue_id`),
  ADD KEY `issue_id` (`issue_id`);

--
-- Index pour la table `universes`
--
ALTER TABLE `universes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `api_requests`
--
ALTER TABLE `api_requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT pour la table `collections`
--
ALTER TABLE `collections`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `formats`
--
ALTER TABLE `formats`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `issues`
--
ALTER TABLE `issues`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT pour la table `publishers`
--
ALTER TABLE `publishers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `series`
--
ALTER TABLE `series`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `tomes`
--
ALTER TABLE `tomes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `universes`
--
ALTER TABLE `universes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `collections`
--
ALTER TABLE `collections`
  ADD CONSTRAINT `fk_collections_publisher` FOREIGN KEY (`publisher_id`) REFERENCES `publishers` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `issues`
--
ALTER TABLE `issues`
  ADD CONSTRAINT `issues_ibfk_1` FOREIGN KEY (`series_id`) REFERENCES `series` (`series_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `series`
--
ALTER TABLE `series`
  ADD CONSTRAINT `fk_series_publisher` FOREIGN KEY (`publisher_id`) REFERENCES `publishers` (`publisher_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tomes`
--
ALTER TABLE `tomes`
  ADD CONSTRAINT `fk_tomes_collection` FOREIGN KEY (`collection_id`) REFERENCES `collections` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tomes_format` FOREIGN KEY (`format_id`) REFERENCES `formats` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tomes_publisher_vf` FOREIGN KEY (`publisher_vf_id`) REFERENCES `publishers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tomes_publisher_vo` FOREIGN KEY (`publisher_vo_id`) REFERENCES `publishers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tomes_series` FOREIGN KEY (`series_id`) REFERENCES `series` (`series_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tomes_universe` FOREIGN KEY (`universe_id`) REFERENCES `universes` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `tome_issues`
--
ALTER TABLE `tome_issues`
  ADD CONSTRAINT `tome_issues_ibfk_1` FOREIGN KEY (`tome_id`) REFERENCES `tomes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tome_issues_ibfk_2` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
