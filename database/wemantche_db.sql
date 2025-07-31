-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2025 at 09:38 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wemantche_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activites`
--

CREATE TABLE `activites` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `document_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `statut` varchar(50) DEFAULT 'success',
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_action` varchar(50) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `details` text DEFAULT NULL,
  `date_action` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `categories_documents`
--

CREATE TABLE `categories_documents` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `statut` varchar(255) NOT NULL,
  `active` tinyint(1) DEFAULT 0,
  `cree_a` timestamp NOT NULL DEFAULT current_timestamp(),
  `mis_a_jour_a` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `categories_documents`
--

INSERT INTO `categories_documents` (`id`, `nom`, `description`, `statut`, `active`, `cree_a`, `mis_a_jour_a`) VALUES
(24, 'Exposés Académiques', 'Documents académiques tels que les présentations et les exposés.', 'disponible', 1, '2025-03-10 14:17:53', '2025-03-10 14:17:53'),
(25, 'Mémoires de Recherche', 'Documents académiques tels que les mémoires de fin d\'études.', 'disponible', 1, '2025-03-10 14:17:53', '2025-04-13 13:35:41'),
(26, 'CV et Lettres Pro', 'Curriculum Vitae et lettres de motivation.', 'publié', 1, '2025-03-10 14:17:53', '2025-03-10 14:17:53'),
(27, 'Documents Pro', 'Documents liés au monde professionnel.', 'disponible', 1, '2025-03-10 14:17:53', '2025-03-10 14:17:53'),
(28, 'Rapports Techniques', 'Rapports professionnels et académiques', 'disponible', 1, '2025-03-10 14:17:53', '2025-03-10 14:17:53'),
(32, 'Attestations', 'Attestations diverses.', 'publié', 1, '2025-03-10 14:47:45', '2025-03-10 14:47:45'),
(33, 'Documents Professionnels', 'Documents liés au monde professionnel.', 'disponible', 1, '2025-03-10 14:47:45', '2025-03-10 14:47:45'),
(35, 'Académique', 'Formations académiques et universitaires', 'disponible', 1, '2025-03-10 14:47:45', '2025-03-10 14:47:45'),
(36, 'Professionnel', 'Formations professionnelles et continues', 'disponible', 1, '2025-03-10 14:47:45', '2025-03-10 14:47:45'),
(37, 'Technique', 'Formations techniques et pratiques', 'disponible', 1, '2025-03-10 14:47:45', '2025-03-10 14:47:45'),
(38, 'Langues', 'Formations en langues étrangères', 'disponible', 1, '2025-03-10 14:47:45', '2025-03-10 14:47:45'),
(39, 'Informatique', 'Formations en informatique et technologies.', 'disponible', 1, '2025-03-10 14:47:45', '2025-03-18 16:28:09'),
(41, 'Formation', 'Documents liés aux formations', 'actif', 1, '2025-03-18 18:05:47', '2025-03-18 18:05:47');

-- --------------------------------------------------------

--
-- Table structure for table `categories_prix`
--

CREATE TABLE `categories_prix` (
  `id` int(11) NOT NULL,
  `categorie_id` int(11) NOT NULL,
  `prix_standard` int(11) NOT NULL COMMENT 'Prix de base en FCFA (entier)',
  `prix_urgent` int(11) DEFAULT NULL COMMENT 'Prix express en FCFA',
  `seuil_mots` int(11) DEFAULT 1000 COMMENT 'Nombre de mots inclus',
  `prix_par_mot_supp` int(11) DEFAULT 500 COMMENT 'Coût par mot supplémentaire (en FCFA)',
  `delai_standard_jours` tinyint(4) NOT NULL DEFAULT 7,
  `delai_urgent_jours` tinyint(4) DEFAULT 3,
  `mis_a_jour_a` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `niveau_etude` varchar(255) NOT NULL,
  `matiere_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `categories_prix`
--

INSERT INTO `categories_prix` (`id`, `categorie_id`, `prix_standard`, `prix_urgent`, `seuil_mots`, `prix_par_mot_supp`, `delai_standard_jours`, `delai_urgent_jours`, `mis_a_jour_a`, `niveau_etude`, `matiere_id`) VALUES
(5, 39, 111, 111100, 2147483647, 500, 7, 1, '2025-04-25 12:24:59', '', NULL),
(8, 25, 10, 1000, 1000, 500, 7, 1, '2025-04-24 20:06:15', '', NULL),
(9, 36, 100, 0, 1000, 500, 127, 1, '2025-04-25 10:16:38', '', NULL),
(22, 26, 1, 1, 1000, 500, 7, 1, '2025-05-03 16:29:30', 'RAS', NULL),
(24, 33, 441, NULL, 1000, 500, 7, 10, '2025-05-03 23:27:37', 'Seconde', NULL),
(33, 32, 44, 444, 1000, 500, 7, 44, '2025-05-04 01:41:25', 'RAS', NULL),
(36, 35, 1, 10, 1000, 500, 7, 12, '2025-05-04 23:21:26', 'Statistiques', NULL),
(41, 37, 111, 11, 1000, 500, 7, 1, '2025-05-04 23:33:32', 'RAS', NULL),
(42, 38, 11, 111, 1000, 500, 7, 1, '2025-05-04 23:55:18', 'Cours Moyen 1', NULL),
(43, 24, 11, 11, 1000, 500, 7, 11, '2025-05-04 23:55:38', 'Petite Section', NULL),
(46, 24, 44, 4, 1000, 500, 7, 4, '2025-05-05 00:11:24', 'Troisième', NULL),
(47, 35, 45, 58, 1000, 500, 7, 87, '2025-05-05 00:13:30', 'Sociologie', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `niveau` enum('maternelle','primaire','college','lycee') COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordre` int(11) NOT NULL,
  `actif` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `nom`, `niveau`, `ordre`, `actif`) VALUES
(1, 'Petite Section', 'maternelle', 1, 1),
(2, 'Moyenne Section', 'maternelle', 2, 1),
(3, 'Grande Section', 'maternelle', 3, 1),
(4, 'Cours Préparatoire', 'primaire', 4, 1),
(5, 'Cours Élémentaire 1', 'primaire', 5, 1),
(6, 'Cours Élémentaire 2', 'primaire', 6, 1),
(7, 'Cours Moyen 1', 'primaire', 7, 1),
(8, 'Cours Moyen 2', 'primaire', 8, 1),
(9, 'Sixième', 'college', 9, 1),
(11, 'Quatrième', 'college', 11, 1),
(12, 'Troisième', 'college', 12, 1),
(13, 'Seconde', 'lycee', 13, 1),
(14, 'Première', 'lycee', 14, 1),
(15, 'Terminale', 'lycee', 15, 1);

-- --------------------------------------------------------

--
-- Table structure for table `connexion_tentatives`
--

CREATE TABLE `connexion_tentatives` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `success` tinyint(1) DEFAULT 0,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `sujet` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `statut` varchar(20) NOT NULL DEFAULT 'nouvelle'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `nom`, `prenom`, `email`, `sujet`, `message`, `date_creation`, `statut`) VALUES
(30, 'ABATTI', 'EUCHER', 'abattieuchert@gmail.com', 'question', 'hhhhhhhhh', '2025-05-24 14:35:43', 'nouvelle'),
(31, 'ABATTI', 'EUCHER', 'abattieuchert@gmail.com', 'support', 'bb;bh;bh;bh', '2025-05-25 17:13:40', 'nouvelle');

-- --------------------------------------------------------

--
-- Table structure for table `demandes_redaction`
--

CREATE TABLE `demandes_redaction` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `categorie_id` int(11) NOT NULL,
  `sujet_theme` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Titre principal de la demande',
  `filiere` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Filière concernée pour les mémoires',
  `filiere_id` int(11) DEFAULT NULL,
  `classe` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Niveau scolaire pour les exposés',
  `classe_id` int(11) DEFAULT NULL,
  `matiere` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Matière académique concernée',
  `objectifs` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Objectifs pédagogiques du document',
  `plan_souhaite` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Structure souhaitée du document',
  `consignes_specifiques` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Instructions particulières',
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Description générale de la demande',
  `delai_souhaite` date NOT NULL COMMENT 'Date limite de remise',
  `budget` decimal(10,0) NOT NULL COMMENT 'Budget alloué en FCFA (entier)',
  `statut` enum('en_attente','en_cours','termine','annule','rejetee','validee') COLLATE utf8mb4_unicode_ci DEFAULT 'en_attente' COMMENT 'État de la demande',
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Date de création',
  `date_modification` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Date de dernière modification',
  `notified` tinyint(1) DEFAULT 0 COMMENT 'Notification envoyée'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Table des demandes de rédaction sur mesure';

--
-- Dumping data for table `demandes_redaction`
--

INSERT INTO `demandes_redaction` (`id`, `utilisateur_id`, `categorie_id`, `sujet_theme`, `filiere`, `filiere_id`, `classe`, `classe_id`, `matiere`, `objectifs`, `plan_souhaite`, `consignes_specifiques`, `description`, `delai_souhaite`, `budget`, `statut`, `date_creation`, `date_modification`, `notified`) VALUES
(29, 73, 36, 'gggggggg', NULL, NULL, NULL, NULL, NULL, 'test', 'test', 'test', 'test', '2025-05-30', '0', 'en_attente', '2025-05-24 13:22:49', '2025-05-24 13:22:49', 0),
(30, 73, 26, 'gggggggg', NULL, NULL, NULL, NULL, NULL, 'jkjkjhkljh', 'jhjl', 'hjklhklh', 'hjklhjklhjkl', '2025-05-30', '1', 'en_attente', '2025-05-25 16:17:03', '2025-05-25 16:17:03', 0),
(31, 73, 32, 'bbbb', NULL, NULL, NULL, NULL, NULL, 'bbbbbbb', 'bbbbbbbbjk', 'nljlkljklj', '', '0000-00-00', '0', 'termine', '2025-05-25 16:22:18', '2025-05-28 07:23:43', 0);

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `contenu` text NOT NULL,
  `categorie_id` int(11) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `langues` varchar(255) NOT NULL,
  `niveau` varchar(255) NOT NULL,
  `mots_cles` text DEFAULT NULL,
  `taille_fichier` int(11) NOT NULL,
  `type_fichier` varchar(255) NOT NULL,
  `statut` varchar(255) NOT NULL,
  `cree_a` timestamp NOT NULL DEFAULT current_timestamp(),
  `mis_a_jour_a` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `downloads_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `filieres`
--

CREATE TABLE `filieres` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `departement` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `niveau` enum('licence','master','doctorat') COLLATE utf8mb4_unicode_ci NOT NULL,
  `actif` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `filieres`
--

INSERT INTO `filieres` (`id`, `nom`, `departement`, `description`, `niveau`, `actif`) VALUES
(1, 'Informatique', 'Sciences et Technologies', NULL, 'licence', 1),
(2, 'Génie Civil', 'Sciences et Technologies', NULL, 'licence', 1),
(3, 'Génie Électrique', 'Sciences et Technologies', NULL, 'licence', 1),
(4, 'Télécommunications', 'Sciences et Technologies', NULL, 'licence', 1),
(5, 'Finance Comptabilité', 'Sciences de Gestion', NULL, 'licence', 1),
(6, 'Marketing Management', 'Sciences de Gestion', NULL, 'licence', 1),
(7, 'Gestion des Ressources Humaines', 'Sciences de Gestion', NULL, 'licence', 1),
(8, 'Commerce International', 'Sciences de Gestion', NULL, 'licence', 1),
(9, 'Sociologie', 'Sciences Humaines', NULL, 'licence', 1),
(10, 'Psychologie', 'Sciences Humaines', NULL, 'licence', 1),
(11, 'Histoire-Géographie', 'Sciences Humaines', NULL, 'licence', 1),
(12, 'Lettres Modernes', 'Lettres', NULL, 'licence', 1),
(13, 'Droit Privé', 'Sciences Juridiques', NULL, 'licence', 1),
(14, 'Droit Public', 'Sciences Juridiques', NULL, 'licence', 1),
(15, 'Sciences Politiques', 'Sciences Juridiques', NULL, 'licence', 1),
(16, 'Médecine Générale', 'Sciences de la Santé', NULL, 'doctorat', 1),
(17, 'Pharmacie', 'Sciences de la Santé', NULL, 'doctorat', 1),
(18, 'Sciences Infirmières', 'Sciences de la Santé', NULL, 'licence', 1),
(19, 'Économie Appliquée', 'Sciences Économiques', NULL, 'licence', 1),
(20, 'Statistiques', 'Sciences Économiques', NULL, 'licence', 1),
(21, 'Économétrie', 'Sciences Économiques', NULL, 'licence', 1),
(23, 'Commerce International', 'Sciences de Gestionnlnnnl', NULL, 'master', 1);

-- --------------------------------------------------------

--
-- Table structure for table `matieres`
--

CREATE TABLE `matieres` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `matieres`
--

INSERT INTO `matieres` (`id`, `nom`) VALUES
(24, 'Accompagnement personnalisé'),
(26, 'Aide aux devoirs'),
(11, 'Allemand (LV2)'),
(10, 'Anglais (LV1)'),
(14, 'Arts Plastiques'),
(30, 'Cinéma-audiovisuel'),
(20, 'Culture et création artistique'),
(28, 'Culture et création design'),
(31, 'Danse'),
(32, 'Échecs'),
(6, 'Education Morale et Civique'),
(15, 'Education Musicale'),
(16, 'Education Physique et Sportive (EPS)'),
(12, 'Espagnol (LV2)'),
(3, 'Français'),
(18, 'Grec ancien'),
(5, 'Histoire-Géographie'),
(13, 'Italien (LV2)'),
(21, 'Langue et culture régionale'),
(17, 'Latin'),
(23, 'Littérature et société'),
(4, 'Mathématiques'),
(22, 'Méthodes et pratiques scientifiques'),
(8, 'Physique-Chimie'),
(7, 'Sciences de la Vie et de la Terre (SVT)'),
(27, 'Sciences économiques et sociales (initiation)'),
(19, 'Sciences Numériques et Informatique'),
(9, 'Technologie'),
(29, 'Théâtre'),
(25, 'Vie de classe');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `lu` tinyint(1) DEFAULT 0,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `reponses_questionnaire`
--

CREATE TABLE `reponses_questionnaire` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `question1` varchar(50) NOT NULL,
  `question2` varchar(50) NOT NULL,
  `question3` varchar(50) NOT NULL,
  `question4` varchar(50) NOT NULL,
  `question5` varchar(50) NOT NULL,
  `cree_a` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `reponses_questionnaire`
--

INSERT INTO `reponses_questionnaire` (`id`, `utilisateur_id`, `question1`, `question2`, `question3`, `question4`, `question5`, `cree_a`) VALUES
(26, 71, 'rouge', 'chien', 'printemps', 'pizza', 'lecture', '2025-04-17 07:46:31'),
(27, 72, 'rouge', 'chien', 'printemps', 'pizza', 'lecture', '2025-04-25 11:08:19'),
(28, 73, 'rouge', 'chien', 'printemps', 'pizza', 'lecture', '2025-05-02 15:22:12');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `titre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'fa-star',
  `actif` tinyint(1) DEFAULT 1,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `titre`, `description`, `prix`, `icon`, `actif`, `date_creation`) VALUES
(1, 'Consultation Juridique', 'Consultation juridique personnalisée avec un expert', '25000.00', 'fa-scale-balanced', 1, '2025-03-11 16:10:22'),
(2, 'Formation Professionnelle', 'Formation complète en droit des affaires', '150000.00', 'fa-graduation-cap', 1, '2025-03-11 16:10:22'),
(3, 'Assistance Administrative', 'Aide pour vos démarches administratives', '35000.00', 'fa-file-contract', 1, '2025-03-11 16:10:22');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `site_name` varchar(255) NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `default_language` varchar(10) DEFAULT 'fr',
  `timezone` varchar(50) DEFAULT 'UTC',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `site_name`, `contact_email`, `description`, `default_language`, `timezone`, `updated_at`) VALUES
(1, 'WEMANTCHE', 'contact@wemantche.com', 'Plateforme de ressources académiques et professionnelles', 'fr', 'UTC', '2025-03-10 21:53:04');

-- --------------------------------------------------------

--
-- Table structure for table `transactions_mobile`
--

CREATE TABLE `transactions_mobile` (
  `id` int(11) NOT NULL,
  `reference` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fedapay_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `operateur` enum('orange','mtn','moov') COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `statut` enum('en_attente','valide','echoue','annule') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_modification` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `response_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`response_data`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `mot_de_passe_hash` varchar(255) NOT NULL,
  `cree_a` timestamp NOT NULL DEFAULT current_timestamp(),
  `mis_a_jour_a` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` enum('utilisateur','redacteur','administrateur') DEFAULT 'utilisateur',
  `statut` enum('actif','bloque') DEFAULT 'actif',
  `derniere_action` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `prenom`, `nom`, `email`, `telephone`, `mot_de_passe_hash`, `cree_a`, `mis_a_jour_a`, `role`, `statut`, `derniere_action`, `remember_token`) VALUES
(71, 'Eucher', 'ABATTI', 'abattieucher@gmail.com', '+2290157002427', '$2y$10$ukp.iw5qrVaR6TtG45VmCOvw3FY9yOGxD.Faq9YMn6gvsFNEVXw6y', '2025-04-17 07:46:17', '2025-05-27 12:36:15', 'administrateur', 'actif', '2025-05-27 13:36:15', '6da47db9d48447806151da8b724dce8939aba189b15b98830b238f90925f91a4'),
(72, 'Eucher', 'ABATTI', 'abattieuchert@gmail.com', '+2290157002427', '$2y$10$6vAwOmnERXtH2h5cToieC.dUMIN2bvFOuIobw6ssJ.Sf0qIIeTGg.', '2025-04-25 11:08:04', '2025-04-25 11:08:26', 'utilisateur', 'actif', '2025-04-25 12:08:26', '550d0a3693ff4b940b6fe0b4f0de8afa24d546e4ce65778c7f52eb2ada22da09'),
(73, 'HBBHJHBH.Hbkbkbjlbbhkbkbklbklblkk', 'AUGOUYON', 'olakiki302@gmail.com', '', '$2y$10$UkDX3w28nW8Yyy51zuDW4er2LkXl0O.3YGAqDKs72Amrb2kT2HsV.', '2025-05-02 15:21:57', '2025-05-27 12:09:02', 'utilisateur', 'actif', '2025-05-27 13:09:02', '7282c6e021374fbdf1597943bb6016abce0875de414c25827b1365e43859e5b4');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activites`
--
ALTER TABLE `activites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `idx_activites_date` (`date_creation`),
  ADD KEY `idx_activites_user` (`utilisateur_id`);

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Indexes for table `categories_documents`
--
ALTER TABLE `categories_documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Indexes for table `categories_prix`
--
ALTER TABLE `categories_prix`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_categorie_niveau` (`categorie_id`,`niveau_etude`),
  ADD KEY `fk_matiere` (`matiere_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `connexion_tentatives`
--
ALTER TABLE `connexion_tentatives`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_tentatives_ip` (`ip_address`),
  ADD KEY `idx_tentatives_date` (`attempted_at`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `demandes_redaction`
--
ALTER TABLE `demandes_redaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_categorie` (`categorie_id`),
  ADD KEY `fk_utilisateur` (`utilisateur_id`),
  ADD KEY `fk_demande_classe` (`classe_id`),
  ADD KEY `fk_demande_filiere` (`filiere_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categorie_id` (`categorie_id`),
  ADD KEY `idx_cree_a` (`cree_a`),
  ADD KEY `idx_type_fichier` (`type_fichier`),
  ADD KEY `idx_downloads` (`downloads_count`);

--
-- Indexes for table `filieres`
--
ALTER TABLE `filieres`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `matieres`
--
ALTER TABLE `matieres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom_unique` (`nom`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `document_id` (`document_id`);

--
-- Indexes for table `reponses_questionnaire`
--
ALTER TABLE `reponses_questionnaire`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_actif` (`actif`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions_mobile`
--
ALTER TABLE `transactions_mobile`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `idx_reference` (`reference`),
  ADD KEY `idx_fedapay_id` (`fedapay_id`),
  ADD KEY `idx_telephone` (`telephone`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_item` (`item_type`,`item_id`);

--
-- Indexes for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activites`
--
ALTER TABLE `activites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `categories_documents`
--
ALTER TABLE `categories_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `categories_prix`
--
ALTER TABLE `categories_prix`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `connexion_tentatives`
--
ALTER TABLE `connexion_tentatives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `demandes_redaction`
--
ALTER TABLE `demandes_redaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `filieres`
--
ALTER TABLE `filieres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `matieres`
--
ALTER TABLE `matieres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reponses_questionnaire`
--
ALTER TABLE `reponses_questionnaire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions_mobile`
--
ALTER TABLE `transactions_mobile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activites`
--
ALTER TABLE `activites`
  ADD CONSTRAINT `activites_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `activites_ibfk_2` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`);

--
-- Constraints for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `fk_admin_logs_utilisateur` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories_prix`
--
ALTER TABLE `categories_prix`
  ADD CONSTRAINT `fk_categorie_prix` FOREIGN KEY (`categorie_id`) REFERENCES `categories_documents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_matiere` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `connexion_tentatives`
--
ALTER TABLE `connexion_tentatives`
  ADD CONSTRAINT `connexion_tentatives_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`);

--
-- Constraints for table `demandes_redaction`
--
ALTER TABLE `demandes_redaction`
  ADD CONSTRAINT `fk_categorie` FOREIGN KEY (`categorie_id`) REFERENCES `categories_documents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_demande_classe` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `fk_demande_filiere` FOREIGN KEY (`filiere_id`) REFERENCES `filieres` (`id`),
  ADD CONSTRAINT `fk_utilisateur` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`categorie_id`) REFERENCES `categories_documents` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`);

--
-- Constraints for table `reponses_questionnaire`
--
ALTER TABLE `reponses_questionnaire`
  ADD CONSTRAINT `reponses_questionnaire_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
