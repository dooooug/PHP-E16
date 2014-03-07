-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le : Ven 07 Mars 2014 à 13:26
-- Version du serveur: 5.5.35
-- Version de PHP: 5.3.10-1ubuntu3.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `hetical`
--

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `creator` tinyint(4) NOT NULL,
  `room` tinyint(4) NOT NULL,
  `date` date NOT NULL,
  `hour` tinyint(4) NOT NULL,
  `promo` varchar(2) NOT NULL,
  `priority` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

--
-- Contenu de la table `events`
--

INSERT INTO `events` (`id`, `title`, `creator`, `room`, `date`, `hour`, `promo`, `priority`) VALUES
(13, 'Soutien Drupal', 1, 5, '2014-02-20', 2, 'H3', 2),
(14, 'Soutien SQL', 2, 5, '2014-02-21', 2, 'H3', 2),
(15, 'Soutien Marketing', 3, 5, '2014-02-22', 2, 'H3', 1),
(16, 'Soutien PHP', 4, 5, '2014-02-23', 2, 'H3', 2),
(17, 'Soutien Design', 5, 10, '2014-02-24', 2, 'H3', 2),
(18, 'Soutien Anglais', 4, 10, '2014-02-21', 1, 'H3', 2);

-- --------------------------------------------------------

--
-- Structure de la table `promos`
--

CREATE TABLE IF NOT EXISTS `promos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Contenu de la table `promos`
--

INSERT INTO `promos` (`id`, `name`) VALUES
(1, 'H1'),
(2, 'H2'),
(3, 'H3'),
(4, 'H4'),
(5, 'H5');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(20) NOT NULL,
  `lastname` varchar(20) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `promo` varchar(2) NOT NULL,
  `group` tinyint(4) NOT NULL,
  `subgroup` varchar(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `login`, `password`, `promo`, `group`, `subgroup`) VALUES
(1, 'Quentin', 'Morisseau', 'quentin.morisseau', 'hetic2016', 'H3', 1, 'A'),
(2, 'Hugo', 'Leloup', 'hugo.leloup', 'hetic2016', 'H3', 1, 'B'),
(3, 'Antoine', 'Vidal de la Blache', 'antoine.vidaldelablache', 'hetic2016', 'H3', 2, 'B'),
(4, 'Christophe', 'Lepage', 'christophe.lepage', 'hetic2016', 'H3', 2, 'A'),
(5, 'Kevin', 'Chassagne', 'kevin.chassagne', 'hetic2016', 'H3', 2, 'A'),
(6, 'François', 'Pumir', 'francois.pumir', 'theboss', 'H3', 2, 'B'),
(7, 'Test', 'Test', 'test.test', 'test', 'H3', 1, 'A');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
