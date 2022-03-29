CREATE DATABASE IF NOT EXISTS `soireisti` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `soireisti`;

-- Structure de la table `articles`
CREATE TABLE `articles` (
  `id` int NOT NULL,
  `nom` text NOT NULL,
  `prix` int NOT NULL,
  `description` text NOT NULL,
  `restants` int NOT NULL,
  `vendus` int NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Structure de la table `budget`
CREATE TABLE `budget` (
  `id` int NOT NULL,
  `nom` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `valeur` int NOT NULL,
  `idUtilisateur` int DEFAULT NULL,
  `categorie` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ('non catégorisé')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `candidatures`
CREATE TABLE `candidatures` (
  `id` int NOT NULL,
  `idUtilisateur` int NOT NULL,
  `demande` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `commentaires`
CREATE TABLE `commentaires` (
  `id` int NOT NULL,
  `idUtilisateur` int NOT NULL,
  `idEvent` int NOT NULL,
  `texte` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `events`
CREATE TABLE `events` (
  `id` int NOT NULL,
  `nom` tinytext NOT NULL,
  `prix` int NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL,
  `lieu` text NOT NULL,
  `lat` double NOT NULL,
  `lon` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Structure de la table `messagerie`
CREATE TABLE `messagerie` (
  `de` int NOT NULL,
  `a` int NOT NULL,
  `date` datetime NOT NULL,
  `texte` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `paniers`
CREATE TABLE `paniers` (
  `idClient` int NOT NULL,
  `idArticle` int NOT NULL,
  `quantite` int NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `reservations`
CREATE TABLE `reservations` (
  `id` int NOT NULL,
  `idUtilisateur` int NOT NULL,
  `idEvent` int NOT NULL,
  `code` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `utilisee` tinyint NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `sondages`
CREATE TABLE `sondages` (
  `id` int NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `reponses` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`reponses`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Structure de la table `utilisateurs`
CREATE TABLE `utilisateurs` (
  `id` int NOT NULL,
  `pseudo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `mdp` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin` tinyint NOT NULL DEFAULT 0,
  `codeMdpOublie` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codePostal` int DEFAULT NULL,
  `ville` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pays` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sondagesRepondus` bigint NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index pour la table `articles`
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`);

-- Index pour la table `budget`
ALTER TABLE `budget`
  ADD PRIMARY KEY (`id`);

-- Index pour la table `candidatures`
ALTER TABLE `candidatures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idUtilisateur` (`idUtilisateur`);

-- Index pour la table `commentaires`
ALTER TABLE `commentaires`
  ADD PRIMARY KEY (`id`);

-- Index pour la table `events`
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

-- Index pour la table `reservations`
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`) USING HASH;

-- Index pour la table `sondages`
ALTER TABLE `sondages`
  ADD PRIMARY KEY (`id`);

-- Index pour la table `utilisateurs`
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`);
  -- ADD UNIQUE KEY `pseudo` (`pseudo`) USING HASH,
  -- ADD UNIQUE KEY `email` (`email`) USING HASH;

-- AUTO_INCREMENT
ALTER TABLE `articles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `budget`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `candidatures`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `commentaires`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `events`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `reservations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `sondages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `utilisateurs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;