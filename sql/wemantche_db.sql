-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 13 mars 2025 à 08:49
-- Version du serveur : 10.4.24-MariaDB
-- Version de PHP : 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `wemantche_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `activites`
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
-- Structure de la table `categories_documents`
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
-- Déchargement des données de la table `categories_documents`
--

INSERT INTO `categories_documents` (`id`, `nom`, `description`, `statut`, `active`, `cree_a`, `mis_a_jour_a`) VALUES
(24, 'Exposés Académiques', 'Documents académiques tels que les présentations et les exposés.', 'disponible', 1, '2025-03-10 14:17:53', '2025-03-10 14:17:53'),
(25, 'Mémoires de Recherche', 'Documents académiques tels que les mémoires de fin d\'études.', 'disponible', 1, '2025-03-10 14:17:53', '2025-03-10 14:17:53'),
(26, 'CV et Lettres Pro', 'Curriculum Vitae et lettres de motivation.', 'publié', 1, '2025-03-10 14:17:53', '2025-03-10 14:17:53'),
(27, 'Documents Pro', 'Documents liés au monde professionnel.', 'disponible', 1, '2025-03-10 14:17:53', '2025-03-10 14:17:53'),
(28, 'Rapports Techniques', 'Rapports professionnels et académiques', 'disponible', 1, '2025-03-10 14:17:53', '2025-03-10 14:17:53'),
(29, 'Exposés', 'Documents académiques tels que les présentations et les exposés.', 'disponible', 1, '2025-03-10 14:47:45', '2025-03-10 14:47:45'),
(30, 'Mémoires', 'Documents académiques tels que les mémoires de fin d\'études.', 'disponible', 1, '2025-03-10 14:47:45', '2025-03-10 14:47:45'),
(31, 'CV & Lettres', 'Curriculum Vitae et lettres de motivation.', 'publié', 1, '2025-03-10 14:47:45', '2025-03-10 14:47:45'),
(32, 'Attestations', 'Attestations diverses.', 'publié', 1, '2025-03-10 14:47:45', '2025-03-10 14:47:45'),
(33, 'Documents Professionnels', 'Documents liés au monde professionnel.', 'disponible', 1, '2025-03-10 14:47:45', '2025-03-10 14:47:45'),
(34, 'Tous les Documents', 'Tous les types de documents disponibles.', 'publié', 1, '2025-03-10 14:47:45', '2025-03-10 14:47:45'),
(35, 'Académique', 'Formations académiques et universitaires', 'disponible', 1, '2025-03-10 14:47:45', '2025-03-10 14:47:45'),
(36, 'Professionnel', 'Formations professionnelles et continues', 'disponible', 1, '2025-03-10 14:47:45', '2025-03-10 14:47:45'),
(37, 'Technique', 'Formations techniques et pratiques', 'disponible', 1, '2025-03-10 14:47:45', '2025-03-10 14:47:45'),
(38, 'Langues', 'Formations en langues étrangères', 'disponible', 1, '2025-03-10 14:47:45', '2025-03-10 14:47:45'),
(39, 'Informatique', 'Formations en informatique et technologies', 'disponible', 1, '2025-03-10 14:47:45', '2025-03-10 14:47:45');

-- --------------------------------------------------------

--
-- Structure de la table `connexion_tentatives`
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
-- Structure de la table `contacts`
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
-- Déchargement des données de la table `contacts`
--

INSERT INTO `contacts` (`id`, `nom`, `prenom`, `email`, `sujet`, `message`, `date_creation`, `statut`) VALUES
(1, 'ABATTI', 'Eucher', 'abattieucher@gmail.com', 'support', 'g', '2025-03-12 18:11:03', 'nouvelle');

-- --------------------------------------------------------

--
-- Structure de la table `demandes_redaction`
--

CREATE TABLE `demandes_redaction` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `type_document` enum('memoire','rapport','expose','autre') NOT NULL,
  `sujet` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `delai_souhaite` date NOT NULL,
  `budget` decimal(10,2) NOT NULL,
  `statut` enum('en_attente','en_cours','termine','annule') DEFAULT 'en_attente',
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_modification` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `notified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `documents`
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
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `type` varchar(50) DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `reponses_questionnaire`
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

-- --------------------------------------------------------

--
-- Structure de la table `services`
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
-- Déchargement des données de la table `services`
--

INSERT INTO `services` (`id`, `titre`, `description`, `prix`, `icon`, `actif`, `date_creation`) VALUES
(1, 'Consultation Juridique', 'Consultation juridique personnalisée avec un expert', '25000.00', 'fa-scale-balanced', 1, '2025-03-11 16:10:22'),
(2, 'Formation Professionnelle', 'Formation complète en droit des affaires', '150000.00', 'fa-graduation-cap', 1, '2025-03-11 16:10:22'),
(3, 'Assistance Administrative', 'Aide pour vos démarches administratives', '35000.00', 'fa-file-contract', 1, '2025-03-11 16:10:22');

-- --------------------------------------------------------

--
-- Structure de la table `site_settings`
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
-- Déchargement des données de la table `site_settings`
--

INSERT INTO `site_settings` (`id`, `site_name`, `contact_email`, `description`, `default_language`, `timezone`, `updated_at`) VALUES
(1, 'WEMANTCHE', 'contact@wemantche.com', 'Plateforme de ressources académiques et professionnelles', 'fr', 'UTC', '2025-03-10 21:53:04');

-- --------------------------------------------------------

--
-- Structure de la table `transactions_mobile`
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
-- Structure de la table `utilisateurs`
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
  `statut` enum('actif','en_attente','bloque') DEFAULT 'en_attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `prenom`, `nom`, `email`, `telephone`, `mot_de_passe_hash`, `cree_a`, `mis_a_jour_a`, `role`, `statut`) VALUES
(29, 'Jean', 'Dupont', 'jean@example.com', '123456789', 'hash123', '2025-03-10 14:17:53', '2025-03-10 14:17:53', '', 'actif'),
(30, 'Marie', 'Martin', 'marie@example.com', '987654321', 'hash456', '2025-03-10 14:17:53', '2025-03-10 14:17:53', '', 'actif'),
(31, 'Pierre', 'Durant', 'pierre@example.com', '456789123', 'hash789', '2025-03-10 14:17:53', '2025-03-11 07:31:25', '', 'bloque'),
(32, 'Eucher', 'ABATTI', 'abattieucher@gmail.com', '57002427', '$2y$10$aM8ZTlQDGFNL5mHlrmVs/uljVqb7bMF.jl4aOqJDW1hsNlJjCKl3u', '2025-03-10 23:15:35', '2025-03-10 23:15:35', 'utilisateur', 'en_attente'),
(33, 'rr', 'ABATTI', 'abattieuchggger@gmail.com', '57002427', '$2y$10$PScRQQZ.jpeVqgv5HZPLh.FJwR20cYBJ0KnGruHgujqORnI4F5KHS', '2025-03-12 19:40:10', '2025-03-12 19:42:26', 'administrateur', 'bloque'),
(34, 'Eucher', 'ABATTI', 'abattieucgggherto@gmail.com', '57002427', '$2y$10$98FroocLfu3SjnE1qe1AMuvvgBsdsvQuHCx7mAy7EQvdoh8EXErQS', '2025-03-12 19:40:42', '2025-03-12 19:40:42', 'redacteur', 'actif'),
(35, 'ttu', 'ABATTI', 'admiyyunt@mail.com', '57002427', '$2y$10$q9s7KV8rGKna1POo08gM..BSo2GngsZuIFv3PFnWec3kHHxNMw6jy', '2025-03-12 19:47:40', '2025-03-12 19:47:40', 'utilisateur', 'actif');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `activites`
--
ALTER TABLE `activites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `idx_activites_date` (`date_creation`),
  ADD KEY `idx_activites_user` (`utilisateur_id`);

--
-- Index pour la table `categories_documents`
--
ALTER TABLE `categories_documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `connexion_tentatives`
--
ALTER TABLE `connexion_tentatives`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_tentatives_ip` (`ip_address`),
  ADD KEY `idx_tentatives_date` (`attempted_at`);

--
-- Index pour la table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `demandes_redaction`
--
ALTER TABLE `demandes_redaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `idx_demandes_statut` (`statut`),
  ADD KEY `idx_demandes_type` (`type_document`);

--
-- Index pour la table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categorie_id` (`categorie_id`),
  ADD KEY `idx_cree_a` (`cree_a`),
  ADD KEY `idx_type_fichier` (`type_fichier`),
  ADD KEY `idx_downloads` (`downloads_count`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `reponses_questionnaire`
--
ALTER TABLE `reponses_questionnaire`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_actif` (`actif`);

--
-- Index pour la table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `transactions_mobile`
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
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `activites`
--
ALTER TABLE `activites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `categories_documents`
--
ALTER TABLE `categories_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT pour la table `connexion_tentatives`
--
ALTER TABLE `connexion_tentatives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `demandes_redaction`
--
ALTER TABLE `demandes_redaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `reponses_questionnaire`
--
ALTER TABLE `reponses_questionnaire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `transactions_mobile`
--
ALTER TABLE `transactions_mobile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `activites`
--
ALTER TABLE `activites`
  ADD CONSTRAINT `activites_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `activites_ibfk_2` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`);

--
-- Contraintes pour la table `connexion_tentatives`
--
ALTER TABLE `connexion_tentatives`
  ADD CONSTRAINT `connexion_tentatives_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`);

--
-- Contraintes pour la table `demandes_redaction`
--
ALTER TABLE `demandes_redaction`
  ADD CONSTRAINT `demandes_redaction_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`);

-- Ajout des contraintes pour la table `documents`
ALTER TABLE `documents` 
ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`categorie_id`) REFERENCES `categories_documents` (`id`);

-- Ajout des contraintes pour la table `notifications`
ALTER TABLE `notifications` 
ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateurs` (`id`);

--
-- Contraintes pour la table `reponses_questionnaire`
--
ALTER TABLE `reponses_questionnaire`
  ADD CONSTRAINT `reponses_questionnaire_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
