-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 28 déc. 2025 à 08:38
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `touche_pas_au_klaxon`
--

-- --------------------------------------------------------

--
-- Structure de la table `agence`
--

DROP TABLE IF EXISTS `agence`;
CREATE TABLE IF NOT EXISTS `agence` (
  `Id_Agence` int NOT NULL AUTO_INCREMENT,
  `ville` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`Id_Agence`),
  UNIQUE KEY `ville` (`ville`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `agence`
--

INSERT INTO `agence` (`Id_Agence`, `ville`) VALUES
(9, 'Bordeaux'),
(10, 'Lille'),
(2, 'Lyon'),
(3, 'Marseille'),
(8, 'Montpellier'),
(6, 'Nantes'),
(5, 'Nice'),
(1, 'Paris'),
(12, 'Reims'),
(11, 'Rennes'),
(7, 'Strasbourg'),
(4, 'Toulouse'),
(16, 'Tours');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `Id_Utilisateur` int NOT NULL AUTO_INCREMENT,
  `nom_utilisateur` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `prenom_utilisateur` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `Id_Agence` int NOT NULL,
  PRIMARY KEY (`Id_Utilisateur`),
  UNIQUE KEY `email` (`email`),
  KEY `Id_Agence` (`Id_Agence`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`Id_Utilisateur`, `nom_utilisateur`, `prenom_utilisateur`, `email`, `telephone`, `mot_de_passe`, `admin`, `Id_Agence`) VALUES
(1, 'Martin', 'Alexandre', 'alexandre.martin@email.fr', '0612345678', '$2y$10$piIgMAgDO4wvZqsHSC5jH.efHelUSuj7MPsvqt.tcwtBITuev84kS', 1, 7),
(2, 'Dubois', 'Sophie', 'sophie.dubois@email.fr', '0698765432', '$2y$10$piIgMAgDO4wvZqsHSC5jH.efHelUSuj7MPsvqt.tcwtBITuev84kS', 0, 10),
(3, 'Bernard', 'Julien', 'julien.bernard@email.fr', '0622446688', '$2y$10$piIgMAgDO4wvZqsHSC5jH.efHelUSuj7MPsvqt.tcwtBITuev84kS', 0, 6),
(4, 'Moreau', 'Camille', 'camille.moreau@email.fr', '0611223344', '$2y$10$piIgMAgDO4wvZqsHSC5jH.efHelUSuj7MPsvqt.tcwtBITuev84kS', 0, 1),
(5, 'Lefèvre', 'Lucie', 'lucie.lefevre@email.fr', '0777889900', '$2y$10$piIgMAgDO4wvZqsHSC5jH.efHelUSuj7MPsvqt.tcwtBITuev84kS', 0, 4),
(6, 'Leroy', 'Thomas', 'thomas.leroy@email.fr', '0655443322', '$2y$10$piIgMAgDO4wvZqsHSC5jH.efHelUSuj7MPsvqt.tcwtBITuev84kS', 0, 9),
(7, 'Roux', 'Chloé', 'chloe.roux@email.fr', '0633221199', '$2y$10$piIgMAgDO4wvZqsHSC5jH.efHelUSuj7MPsvqt.tcwtBITuev84kS', 0, 3),
(8, 'Petit', 'Maxime', 'maxime.petit@email.fr', '0766778899', '$2y$10$piIgMAgDO4wvZqsHSC5jH.efHelUSuj7MPsvqt.tcwtBITuev84kS', 0, 3),
(9, 'Garnier', 'Laura', 'laura.garnier@email.fr', '0688776655', '$2y$10$piIgMAgDO4wvZqsHSC5jH.efHelUSuj7MPsvqt.tcwtBITuev84kS', 0, 4),
(10, 'Dupuis', 'Antoine', 'antoine.dupuis@email.fr', '0744556677', '$2y$10$piIgMAgDO4wvZqsHSC5jH.efHelUSuj7MPsvqt.tcwtBITuev84kS', 0, 2),
(11, 'Lefebvre', 'Emma', 'emma.lefebvre@email.fr', '0699887766', '$2y$10$piIgMAgDO4wvZqsHSC5jH.efHelUSuj7MPsvqt.tcwtBITuev84kS', 0, 2),
(12, 'Fontaine', 'Louis', 'louis.fontaine@email.fr', '0655667788', '$2y$10$piIgMAgDO4wvZqsHSC5jH.efHelUSuj7MPsvqt.tcwtBITuev84kS', 0, 12),
(13, 'Chevalier', 'Clara', 'clara.chevalier@email.fr', '0788990011', '$2y$10$piIgMAgDO4wvZqsHSC5jH.efHelUSuj7MPsvqt.tcwtBITuev84kS', 0, 3),
(14, 'Robin', 'Nicolas', 'nicolas.robin@email.fr', '0644332211', '$2y$10$piIgMAgDO4wvZqsHSC5jH.efHelUSuj7MPsvqt.tcwtBITuev84kS', 0, 3),
(15, 'Gauthier', 'Marine', 'marine.gauthier@email.fr', '0677889922', '$2y$10$piIgMAgDO4wvZqsHSC5jH.efHelUSuj7MPsvqt.tcwtBITuev84kS', 0, 9),
(16, 'Fournier', 'Pierre', 'pierre.fournier@email.fr', '0722334455', '$2y$10$piIgMAgDO4wvZqsHSC5jH.efHelUSuj7MPsvqt.tcwtBITuev84kS', 0, 3),
(17, 'Girard', 'Sarah', 'sarah.girard@email.fr', '0688665544', '$2y$10$piIgMAgDO4wvZqsHSC5jH.efHelUSuj7MPsvqt.tcwtBITuev84kS', 0, 12),
(18, 'Lambert', 'Hugo', 'hugo.lambert@email.fr', '0611223366', '$2y$10$piIgMAgDO4wvZqsHSC5jH.efHelUSuj7MPsvqt.tcwtBITuev84kS', 0, 5),
(19, 'Masson', 'Julie', 'julie.masson@email.fr', '0733445566', '$2y$10$piIgMAgDO4wvZqsHSC5jH.efHelUSuj7MPsvqt.tcwtBITuev84kS', 0, 8),
(20, 'Henry', 'Arthur', 'arthur.henry@email.fr', '0666554433', '$2y$10$piIgMAgDO4wvZqsHSC5jH.efHelUSuj7MPsvqt.tcwtBITuev84kS', 0, 4);

-- --------------------------------------------------------

--
-- Structure de la table `trajet`
--

DROP TABLE IF EXISTS `trajet`;
CREATE TABLE IF NOT EXISTS `trajet` (
  `Id_Trajet` int NOT NULL AUTO_INCREMENT,
  `date_heure_depart` datetime NOT NULL,
  `date_heure_arrivee` datetime NOT NULL,
  `nb_places_total` int NOT NULL,
  `nb_places_dispo` int NOT NULL,
  `Id_Conducteur` int NOT NULL,
  `Id_Agence_Depart` int NOT NULL,
  `Id_Agence_Arrivee` int NOT NULL,
  PRIMARY KEY (`Id_Trajet`),
  KEY `Id_Conducteur` (`Id_Conducteur`),
  KEY `Id_Agence_Depart` (`Id_Agence_Depart`),
  KEY `Id_Agence_Arrivee` (`Id_Agence_Arrivee`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `trajet`
--

INSERT INTO `trajet` (`Id_Trajet`, `date_heure_depart`, `date_heure_arrivee`, `nb_places_total`, `nb_places_dispo`, `Id_Conducteur`, `Id_Agence_Depart`, `Id_Agence_Arrivee`) VALUES
(2, '2025-12-24 10:00:00', '2025-12-24 15:00:00', 8, 4, 14, 8, 11),
(4, '2025-12-26 10:00:00', '2025-12-26 13:00:00', 3, 1, 10, 5, 6),
(5, '2025-12-27 13:00:00', '2025-12-27 17:00:00', 8, 6, 6, 3, 7);


--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `trajet`
--
ALTER TABLE `trajet`
  ADD CONSTRAINT `trajet_ibfk_1` FOREIGN KEY (`Id_Conducteur`) REFERENCES `utilisateur` (`Id_Utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `trajet_ibfk_2` FOREIGN KEY (`Id_Agence_Depart`) REFERENCES `agence` (`Id_Agence`),
  ADD CONSTRAINT `trajet_ibfk_3` FOREIGN KEY (`Id_Agence_Arrivee`) REFERENCES `agence` (`Id_Agence`);

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `utilisateur_ibfk_1` FOREIGN KEY (`Id_Agence`) REFERENCES `agence` (`Id_Agence`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
