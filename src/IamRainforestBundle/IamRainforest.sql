-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 28. Apr 2015 um 13:05
-- Server Version: 5.5.43-0ubuntu0.14.04.1
-- PHP-Version: 5.5.9-1ubuntu4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `IamRainforest`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `map`
--

CREATE TABLE IF NOT EXISTS `map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(128) COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `image_url` varchar(128) COLLATE utf8_bin NOT NULL,
  `width` int(10) unsigned NOT NULL,
  `number_x` int(10) unsigned NOT NULL,
  `height` int(10) unsigned NOT NULL,
  `number_y` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `map`
--

INSERT INTO `map` (`id`, `slug`, `description`, `image_url`, `width`, `number_x`, `height`, `number_y`) VALUES
(1, 'rainforest', 'test Map ', 'img/rainforest.jpeg', 1280, 80, 800, 50);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `square`
--

CREATE TABLE IF NOT EXISTS `square` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `map_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `coord_x` int(10) unsigned NOT NULL,
  `coord_y` int(10) unsigned NOT NULL,
  `bought_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `personal_text` tinytext COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `map_id` (`map_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `square`
--

INSERT INTO `square` (`id`, `map_id`, `user_id`, `coord_x`, `coord_y`, `bought_at`, `personal_text`) VALUES
(2, 1, 1, 22, 45, '2015-04-28 10:10:22', 'roboter mit senf, ist mir egal. regenwald ist mir egal');

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `square`
--
ALTER TABLE `square`
  ADD CONSTRAINT `square_ibfk_1` FOREIGN KEY (`map_id`) REFERENCES `map` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
