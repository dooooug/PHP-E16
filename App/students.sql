-- phpMyAdmin SQL Dump
-- version 4.1.5
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Ven 14 Février 2014 à 11:29
-- Version du serveur :  5.6.15
-- Version de PHP :  5.4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `hetical`
--

-- --------------------------------------------------------

--
-- Structure de la table `students`
--

CREATE TABLE IF NOT EXISTS `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(20) NOT NULL,
  `lastname` varchar(20) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `promo` varchar(2) NOT NULL,
  `group` tinyint(4) NOT NULL,
  `subgroup` varchar(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `students`
--

INSERT INTO `students` (`id`, `firstname`, `lastname`, `login`, `password`, `promo`, `group`, `subgroup`) VALUES
(1, 'Quentin', 'Morisseau', 'quentin.morisseau', 'hetic2016', 'H1', 1, 'A'),
(2, 'Hugo', 'Leloup', 'hugo.leloup', 'hetic2016', 'H2', 2, 'B'),
(3, 'Antoine', 'Vidal de la Blache', 'antoine.vidaldelablache', 'hetic2016', 'H3', 3, 'C'),
(4, 'Christophe', 'Lepage', 'christophe.lepage', 'hetic2016', 'H4', 4, 'D'),
(5, 'Kevin', 'Chassagne', 'kevin.chassagne', 'hetic2016', 'H5', 5, 'E');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
