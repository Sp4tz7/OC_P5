-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Version du serveur :  10.4.10-MariaDB
-- Version de PHP :  7.2.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `php_blog`
--

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `date_add` datetime NOT NULL DEFAULT current_timestamp(),
  `date_edit` datetime DEFAULT NULL,
  `status` enum('PENDING','APPROVED','REJECTED') NOT NULL DEFAULT 'PENDING',
  PRIMARY KEY (`id`),
  KEY `FK_BLOG` (`post_id`),
  KEY `FK_USER_COMMENT` (`user_id`),
  KEY `FK_PARENT` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

DROP TABLE IF EXISTS `post`;
CREATE TABLE IF NOT EXISTS `post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(64) NOT NULL,
  `slug` varchar(128) NOT NULL,
  `abstract_content` tinytext DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `image_url` varchar(128) DEFAULT NULL,
  `date_add` datetime NOT NULL DEFAULT current_timestamp(),
  `date_edit` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `edited_by` int(11) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `FK_USER` (`created_by`),
  KEY `FK_EDIT` (`edited_by`),
  KEY `FK_CATEGORY` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `post_category`
--

DROP TABLE IF EXISTS `post_category`;
CREATE TABLE IF NOT EXISTS `post_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(55) NOT NULL,
  `category_slug` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_slug` (`category_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(64) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `token` varchar(32) NOT NULL,
  `token_validity` datetime DEFAULT NULL,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `nickname` varchar(32) DEFAULT NULL,
  `role` enum('SUPERADMIN','ADMIN','MEMBER') NOT NULL DEFAULT 'MEMBER',
  `register_date` datetime DEFAULT current_timestamp(),
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `show_full_name` tinyint(1) NOT NULL DEFAULT 0,
  `send_email_replay` tinyint(1) NOT NULL DEFAULT 0,
  `send_email_approve` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `nickname` (`nickname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `FK_BLOG` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_PARENT` FOREIGN KEY (`parent_id`) REFERENCES `comment` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_USER_COMMENT` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `FK_CATEGORY` FOREIGN KEY (`category_id`) REFERENCES `post_category` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_EDIT` FOREIGN KEY (`edited_by`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_USER` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
