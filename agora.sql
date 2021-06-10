-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 10, 2021 at 08:46 AM
-- Server version: 5.7.31
-- PHP Version: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `agora`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_64C19C15E237E06` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`) VALUES
(4, 'Basket'),
(1, 'Defaut'),
(3, 'Foot'),
(2, 'Tennis');

-- --------------------------------------------------------

--
-- Table structure for table `delegation`
--

DROP TABLE IF EXISTS `delegation`;
CREATE TABLE IF NOT EXISTS `delegation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_from_id` int(11) NOT NULL,
  `user_to_id` int(11) NOT NULL,
  `workshop_id` int(11) DEFAULT NULL,
  `theme_id` int(11) DEFAULT NULL,
  `deepness` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_292F436D20C3C701` (`user_from_id`),
  KEY `IDX_292F436DD2F7B13D` (`user_to_id`),
  KEY `IDX_292F436D1FDCE57C` (`workshop_id`),
  KEY `IDX_292F436D59027487` (`theme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document`
--

DROP TABLE IF EXISTS `document`;
CREATE TABLE IF NOT EXISTS `document` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workshop_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D8698A761FDCE57C` (`workshop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forum`
--

DROP TABLE IF EXISTS `forum`;
CREATE TABLE IF NOT EXISTS `forum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `proposal_id` int(11) NOT NULL,
  `parent_forum_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_852BBECDA76ED395` (`user_id`),
  KEY `IDX_852BBECDF4792058` (`proposal_id`),
  KEY `IDX_852BBECDB6011601` (`parent_forum_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `forum`
--

INSERT INTO `forum` (`id`, `user_id`, `proposal_id`, `parent_forum_id`, `name`, `description`) VALUES
(1, 1003, 3, NULL, '10cm de plus ca serait bien', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus;</p>'),
(2, 1003, 3, NULL, 'Ou mneme 20 cm', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus;</p>'),
(3, 1003, 1, NULL, 'Pas plus haut pour moi', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus;</p>'),
(4, 1003, 2, NULL, 'Garder ca serait cool', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus;</p>'),
(6, 1003, 2, NULL, 'non parent', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus;</p>'),
(7, 999, 1, NULL, 'C\'est haut deja', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus;</p>'),
(9, 999, 2, 6, 'Ok!!!!', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus;</p>');

-- --------------------------------------------------------

--
-- Table structure for table `keyword`
--

DROP TABLE IF EXISTS `keyword`;
CREATE TABLE IF NOT EXISTS `keyword` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `keyword`
--

INSERT INTO `keyword` (`id`, `name`) VALUES
(1, 'Basket'),
(2, 'but'),
(3, 'But'),
(4, 'foot');

-- --------------------------------------------------------

--
-- Table structure for table `migration_versions`
--

DROP TABLE IF EXISTS `migration_versions`;
CREATE TABLE IF NOT EXISTS `migration_versions` (
  `version` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migration_versions`
--

INSERT INTO `migration_versions` (`version`, `executed_at`) VALUES
('1', '2021-06-10 07:37:46');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
CREATE TABLE IF NOT EXISTS `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `request_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `subject` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BF5476CAA76ED395` (`user_id`),
  KEY `IDX_BF5476CA427EB8A5` (`request_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`id`, `user_id`, `request_id`, `date`, `subject`, `is_read`) VALUES
(1, 1003, NULL, '2021-06-10', 'Réponse : C\'est haut deja', 0),
(2, 1003, NULL, '2021-06-10', 'Réponse : Nimporte quoi', 0),
(3, 1003, NULL, '2021-06-10', 'Réponse : Ok!!!!', 0),
(4, 1003, NULL, '2021-06-10', 'Réponse : Super test', 0);

-- --------------------------------------------------------

--
-- Table structure for table `proposal`
--

DROP TABLE IF EXISTS `proposal`;
CREATE TABLE IF NOT EXISTS `proposal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workshop_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BFE594721FDCE57C` (`workshop_id`),
  KEY `IDX_BFE59472A76ED395` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `proposal`
--

INSERT INTO `proposal` (`id`, `workshop_id`, `user_id`, `name`, `description`) VALUES
(1, 1, 999, 'Changer la hauteur', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus;</p>'),
(2, 1, 999, 'Ou alors la garder', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus;</p>'),
(3, 2, 1003, 'Agrandir les cages??', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus;</p>'),
(4, 2, 1003, 'Plus petite sinon', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus;</p>');

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

DROP TABLE IF EXISTS `report`;
CREATE TABLE IF NOT EXISTS `report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `forum_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C42F7784A76ED395` (`user_id`),
  KEY `IDX_C42F778429CCBAD0` (`forum_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

DROP TABLE IF EXISTS `request`;
CREATE TABLE IF NOT EXISTS `request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `is_done` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3B978F9FA76ED395` (`user_id`),
  KEY `IDX_3B978F9F12469DE2` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reset_password_request`
--

DROP TABLE IF EXISTS `reset_password_request`;
CREATE TABLE IF NOT EXISTS `reset_password_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `selector` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hashed_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requested_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_7CE748AA76ED395` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `theme`
--

DROP TABLE IF EXISTS `theme`;
CREATE TABLE IF NOT EXISTS `theme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_public` tinyint(1) NOT NULL,
  `delegation_deepness` int(11) DEFAULT NULL,
  `vote_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9775E70812469DE2` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `theme`
--

INSERT INTO `theme` (`id`, `category_id`, `name`, `image`, `updated_at`, `description`, `is_public`, `delegation_deepness`, `vote_type`) VALUES
(1, 4, 'Les dunks', '60c1c36faf07f047807026.jpg', '2021-06-10 07:46:55', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus;</p>', 1, 5, 'no-delegation'),
(2, 3, 'Les cages', '60c1c37e225f0014118757.jpg', '2021-06-10 07:47:10', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus;</p>', 1, NULL, 'no-delegation');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_allowed_emails` tinyint(1) NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `first_name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=1004 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `roles`, `password`, `email`, `is_allowed_emails`, `image`, `updated_at`, `first_name`, `last_name`) VALUES
(999, 'Administrateur', '[\"ROLE_ADMIN\"]', '$argon2id$v=19$m=65536,t=4,p=1$L2pxOS81eEpMaUdaVEFVQg$IFC+9PjDXqNVtAKik1Wo21+kugImSmQgEnAvpXOzI1w', 'admin@mail.com', 0, NULL, NULL, 'Admin', 'Nistrateur'),
(1000, 'Utilisateur', '[]', '$argon2id$v=19$m=65536,t=4,p=1$THBwb2VJZ0s2S3JCbkF0Vw$E07T0i6khFq+TxcjS/Nv1X7McUa4ESi36gu+4zloMFQ', 'util@mail.com', 0, NULL, NULL, 'Utili', 'Lisateur'),
(1001, 'Moderateur', '[\"ROLE_MODERATOR\"]', '$argon2id$v=19$m=65536,t=4,p=1$UzMxMjhKTm14ZXdrLlJKUA$I9Nae98mEkXCnOO+O4mWVM4X/V94yXyB7dTHhyAKjzY', 'modo@mail.com', 0, NULL, NULL, 'Mode', 'rateur'),
(1002, 'restreint', '[\"ROLE_ADMIN_RESTRICTED\"]', '$argon2id$v=19$m=65536,t=4,p=1$eUtPVi45Z0l2a3dCS0VPSQ$bh/8P+ogZKti1eUAVZKraAJhK6VAX4YwuosW0xzz4zk', 'restriced@mail.com', 0, NULL, NULL, 'KJeqn;chel', 'Legrand'),
(1003, 'Autre Utilisateur', '[]', '$argon2id$v=19$m=65536,t=4,p=1$VXZLdnVlamVnQTd4VEhsOQ$uqSVDXRBY0qc85vlu04ZloY8B/R/993r9X8E+ZeMj4g', 'alex@mail.com', 0, NULL, NULL, 'Autr', 'Magique');

-- --------------------------------------------------------

--
-- Table structure for table `user_category`
--

DROP TABLE IF EXISTS `user_category`;
CREATE TABLE IF NOT EXISTS `user_category` (
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`category_id`),
  KEY `IDX_E6C1FDC1A76ED395` (`user_id`),
  KEY `IDX_E6C1FDC112469DE2` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_category`
--

INSERT INTO `user_category` (`user_id`, `category_id`) VALUES
(999, 2),
(999, 3),
(999, 4),
(1000, 2),
(1000, 3),
(1000, 4),
(1001, 2),
(1001, 3),
(1001, 4),
(1002, 2),
(1002, 3),
(1002, 4),
(1003, 2),
(1003, 3),
(1003, 4);

-- --------------------------------------------------------

--
-- Table structure for table `vote`
--

DROP TABLE IF EXISTS `vote`;
CREATE TABLE IF NOT EXISTS `vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proposal_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `creation_date` date NOT NULL,
  `voted_for` tinyint(1) NOT NULL DEFAULT '0',
  `voted_against` tinyint(1) NOT NULL DEFAULT '0',
  `voted_blank` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `IDX_5A108564F4792058` (`proposal_id`),
  KEY `IDX_5A108564A76ED395` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vote`
--

INSERT INTO `vote` (`id`, `proposal_id`, `user_id`, `creation_date`, `voted_for`, `voted_against`, `voted_blank`) VALUES
(1, 1, 1003, '2021-06-10', 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `website`
--

DROP TABLE IF EXISTS `website`;
CREATE TABLE IF NOT EXISTS `website` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `website`
--

INSERT INTO `website` (`id`, `title`, `version`, `name`, `email`) VALUES
(1, 'AGORA Ex Machina', 'v0.9.2', 'CRLBazin', 'crlbazin@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `workshop`
--

DROP TABLE IF EXISTS `workshop`;
CREATE TABLE IF NOT EXISTS `workshop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `theme_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_begin` date NOT NULL,
  `date_end` date NOT NULL,
  `rights_see_workshop` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rights_vote_proposals` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rights_write_proposals` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quorum_required` int(11) DEFAULT NULL,
  `rights_delegation` tinyint(1) NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `date_vote_begin` date NOT NULL,
  `date_vote_end` date NOT NULL,
  `keytext` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delegation_deepness` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9B6F02C459027487` (`theme_id`),
  KEY `IDX_9B6F02C4A76ED395` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `workshop`
--

INSERT INTO `workshop` (`id`, `theme_id`, `user_id`, `name`, `description`, `date_begin`, `date_end`, `rights_see_workshop`, `rights_vote_proposals`, `rights_write_proposals`, `quorum_required`, `rights_delegation`, `image`, `updated_at`, `date_vote_begin`, `date_vote_end`, `keytext`, `delegation_deepness`) VALUES
(1, 1, 999, 'Trop dur les dunks??', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus;</p>', '2021-06-10', '2021-08-10', 'everyone', 'everyone', 'everyone', 0, 1, '60c1c39946f90234562106.jpg', '2021-06-10 07:47:37', '2021-06-10', '2021-08-10', 'Basket, but', NULL),
(2, 2, 999, 'Trop grandes les cages?', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus; Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quae animi affectio suum cuique tribuens atque hanc, quam dico. Tollenda est atque extrahenda radicitus. Rationis enim perfectio est virtus;</p>', '2021-06-10', '2021-08-10', 'everyone', 'everyone', 'everyone', 0, 1, '60c1c3ab3c25b012248829.jpg', '2021-06-10 07:47:55', '2021-06-10', '2021-08-10', 'But, foot', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `workshop_keyword`
--

DROP TABLE IF EXISTS `workshop_keyword`;
CREATE TABLE IF NOT EXISTS `workshop_keyword` (
  `workshop_id` int(11) NOT NULL,
  `keyword_id` int(11) NOT NULL,
  PRIMARY KEY (`workshop_id`,`keyword_id`),
  KEY `IDX_18DC632C1FDCE57C` (`workshop_id`),
  KEY `IDX_18DC632C115D4552` (`keyword_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `workshop_keyword`
--

INSERT INTO `workshop_keyword` (`workshop_id`, `keyword_id`) VALUES
(1, 1),
(1, 2),
(2, 3),
(2, 4);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `delegation`
--
ALTER TABLE `delegation`
  ADD CONSTRAINT `FK_292F436D1FDCE57C` FOREIGN KEY (`workshop_id`) REFERENCES `workshop` (`id`),
  ADD CONSTRAINT `FK_292F436D20C3C701` FOREIGN KEY (`user_from_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_292F436D59027487` FOREIGN KEY (`theme_id`) REFERENCES `theme` (`id`),
  ADD CONSTRAINT `FK_292F436DD2F7B13D` FOREIGN KEY (`user_to_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `document`
--
ALTER TABLE `document`
  ADD CONSTRAINT `FK_D8698A761FDCE57C` FOREIGN KEY (`workshop_id`) REFERENCES `workshop` (`id`);

--
-- Constraints for table `forum`
--
ALTER TABLE `forum`
  ADD CONSTRAINT `FK_852BBECDA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_852BBECDB6011601` FOREIGN KEY (`parent_forum_id`) REFERENCES `forum` (`id`),
  ADD CONSTRAINT `FK_852BBECDF4792058` FOREIGN KEY (`proposal_id`) REFERENCES `proposal` (`id`);

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `FK_BF5476CA427EB8A5` FOREIGN KEY (`request_id`) REFERENCES `request` (`id`),
  ADD CONSTRAINT `FK_BF5476CAA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `proposal`
--
ALTER TABLE `proposal`
  ADD CONSTRAINT `FK_BFE594721FDCE57C` FOREIGN KEY (`workshop_id`) REFERENCES `workshop` (`id`),
  ADD CONSTRAINT `FK_BFE59472A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `FK_C42F778429CCBAD0` FOREIGN KEY (`forum_id`) REFERENCES `forum` (`id`),
  ADD CONSTRAINT `FK_C42F7784A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `request`
--
ALTER TABLE `request`
  ADD CONSTRAINT `FK_3B978F9F12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
  ADD CONSTRAINT `FK_3B978F9FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `reset_password_request`
--
ALTER TABLE `reset_password_request`
  ADD CONSTRAINT `FK_7CE748AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `theme`
--
ALTER TABLE `theme`
  ADD CONSTRAINT `FK_9775E70812469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_category`
--
ALTER TABLE `user_category`
  ADD CONSTRAINT `FK_E6C1FDC112469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_E6C1FDC1A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vote`
--
ALTER TABLE `vote`
  ADD CONSTRAINT `FK_5A108564A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_5A108564F4792058` FOREIGN KEY (`proposal_id`) REFERENCES `proposal` (`id`);

--
-- Constraints for table `workshop`
--
ALTER TABLE `workshop`
  ADD CONSTRAINT `FK_9B6F02C459027487` FOREIGN KEY (`theme_id`) REFERENCES `theme` (`id`),
  ADD CONSTRAINT `FK_9B6F02C4A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `workshop_keyword`
--
ALTER TABLE `workshop_keyword`
  ADD CONSTRAINT `FK_18DC632C115D4552` FOREIGN KEY (`keyword_id`) REFERENCES `keyword` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_18DC632C1FDCE57C` FOREIGN KEY (`workshop_id`) REFERENCES `workshop` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
