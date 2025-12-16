-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 16 déc. 2025 à 03:45
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
-- Base de données : `app_review_project`
--

-- --------------------------------------------------------

--
-- Structure de la table `business`
--

CREATE TABLE `business` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `owner_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `business`
--

INSERT INTO `business` (`id`, `name`, `address`, `phone`, `website`, `description`, `owner_id`, `category_id`) VALUES
(1, 'Le Bon Restaurant', '123 Rue de la Paix, Paris', '01 23 45 67 89', 'https://lebonrestaurant.fr', 'Un restaurant traditionnel français avec une cuisine raffinée.', 2, 1),
(2, 'Coffee Corner', '456 Avenue des Champs, Lyon', '04 56 78 90 12', 'https://coffeecorner.com', 'Café artisanal avec pâtisseries maison.', 3, 2),
(3, 'Grand Hôtel', '789 Boulevard Maritime, Nice', '04 93 12 34 56', 'https://grandhotelnice.com', 'Hôtel 4 étoiles avec vue sur la mer.', 2, 3),
(4, 'Fashion Store', '101 Rue de la Mode, Lille', '03 20 12 34 56', 'https://fashionstore.fr', 'Boutique de vêtements tendance pour hommes et femmes.', 3, 4),
(5, 'Beauty Paradise', '202 Avenue du Luxe, Bordeaux', '05 56 78 90 12', 'https://beautyparadise.com', 'Salon de beauté et spa relaxant.', 2, 5),
(6, 'Fresh Market', '303 Rue du Commerce, Marseille', '04 91 23 45 67', 'https://freshmarket.fr', 'Supermarché bio avec produits locaux.', 3, 6),
(7, 'City Pharmacy', '404 Boulevard Central, Toulouse', '05 61 23 45 67', 'https://citypharmacy.fr', 'Pharmacie de garde et parapharmacie.', 2, 7),
(8, 'Auto Expert', '505 Route Nationale, Strasbourg', '03 88 12 34 56', 'https://autoexpert.fr', 'Garage automobile avec service complet.', 3, 8);

-- --------------------------------------------------------

--
-- Structure de la table `business_photo`
--

CREATE TABLE `business_photo` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `business_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `business_photo`
--

INSERT INTO `business_photo` (`id`, `filename`, `business_id`) VALUES
(1, 'pizza1-6940bc1b5a4ca.jpg', 1),
(2, 'pizza2-6940bc1b5c196.jpg', 1),
(3, 'pizza3-6940bc1b5d2e9.jpg', 1),
(4, 'pizza4-6940bc1b5e588.jpg', 1),
(5, 'green1-6940bc301b153.jpg', 2),
(6, 'green2-6940bc301da27.jpg', 2),
(7, 'green3-6940bc301e83b.jpg', 2),
(8, 'hotel2-6940bca5ab068.jpg', 3),
(9, 'hotel4-6940bca5ace04.jpg', 3),
(10, 'hotrl1-6940bca5ae45b.jpg', 3),
(11, 'store1-6940bd3677260.jpg', 4),
(12, 'store2-6940bd3678fb5.jpg', 4),
(13, 'salon1-6940bd9cc71a1.jpg', 5),
(14, 'salon2-6940bd9cc924b.jpg', 5),
(15, 'salon3-6940bd9cca205.png', 5),
(16, 'march1-6940bdfb83e43.jpg', 6),
(17, 'march2-6940bdfb85dc3.jpg', 6),
(18, 'march3-6940bdfb86e92.jpg', 6),
(19, 'pharma1-6940be57d51e4.jpg', 7),
(20, 'pharma2-6940be57d70f8.jpg', 7),
(21, 'pharma3-6940be57d8088.jpg', 7),
(22, 'auto1-6940bedb7a0d9.jpg', 8),
(23, 'auto2-6940bedb7be86.jpg', 8),
(24, 'auto3-6940bedb7d226.jpg', 8);

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`id`, `name`) VALUES
(1, 'Restaurant'),
(2, 'Café'),
(3, 'Hôtel'),
(4, 'Boutique'),
(5, 'Salon de beauté'),
(6, 'Supermarket'),
(7, 'Pharmacie'),
(8, 'Garage automobile');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20251130130505', '2025-12-16 02:37:12', 1522);

-- --------------------------------------------------------

--
-- Structure de la table `favorite_business`
--

CREATE TABLE `favorite_business` (
  `id` int(11) NOT NULL,
  `added_at` datetime NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `business_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `favorite_business`
--

INSERT INTO `favorite_business` (`id`, `added_at`, `user_id`, `business_id`) VALUES
(1, '2025-12-11 02:47:48', 4, 1),
(2, '2025-12-12 02:47:48', 4, 2),
(3, '2025-12-13 02:47:48', 5, 3),
(4, '2025-12-14 02:47:48', 5, 4),
(5, '2025-12-15 02:47:48', 6, 1),
(6, '2025-12-16 02:47:48', 6, 6);

-- --------------------------------------------------------

--
-- Structure de la table `like_review`
--

CREATE TABLE `like_review` (
  `id` int(11) NOT NULL,
  `liked_at` datetime NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `review_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `like_review`
--

INSERT INTO `like_review` (`id`, `liked_at`, `user_id`, `review_id`) VALUES
(1, '2025-12-14 02:47:49', 4, 2),
(2, '2025-12-14 02:47:49', 5, 1),
(3, '2025-12-15 02:47:49', 6, 1),
(4, '2025-12-15 02:47:49', 4, 3),
(5, '2025-12-16 02:47:49', 5, 4),
(6, '2025-12-16 02:47:49', 6, 5);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` longtext NOT NULL,
  `type` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `is_read` tinyint(1) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `notification`
--

INSERT INTO `notification` (`id`, `title`, `message`, `type`, `created_at`, `is_read`, `user_id`) VALUES
(1, 'Bienvenue sur App-Review !', 'Bonjour Admin, bienvenue sur App-Review ! Nous sommes ravis de vous compter parmi nous. Commencez dès maintenant à explorer les commerces autour de vous, laisser vos avis et découvrir de nouveaux endroits.', 'success', '2025-12-16 02:41:21', 0, 1),
(2, 'Conseils pour bien débuter', '💡 Astuce : Complétez votre profil pour recevoir des recommandations personnalisées. N\'hésitez pas à partager vos expériences en laissant des avis détaillés avec photos !', 'info', '2025-12-16 02:41:21', 0, 1);

-- --------------------------------------------------------

--
-- Structure de la table `report_review`
--

CREATE TABLE `report_review` (
  `id` int(11) NOT NULL,
  `reason` longtext NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `review_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `review`
--

CREATE TABLE `review` (
  `id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` longtext DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `is_edited` tinyint(1) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `business_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `review`
--

INSERT INTO `review` (`id`, `rating`, `comment`, `created_at`, `updated_at`, `is_edited`, `user_id`, `business_id`) VALUES
(1, 5, 'Excellent restaurant! La nourriture était délicieuse et le service impeccable.', '2025-12-06 02:47:48', NULL, 0, 4, 1),
(2, 4, 'Très bon café, l\'ambiance est chaleureuse. Les gâteaux sont faits maison.', '2025-12-08 02:47:48', NULL, 0, 5, 2),
(3, 3, 'Hôtel correct mais un peu bruyant. La chambre était propre.', '2025-12-10 02:47:48', '2025-12-11 02:47:48', 1, 6, 3),
(4, 5, 'Magnifique boutique! Les vêtements sont de très bonne qualité.', '2025-12-11 02:47:48', NULL, 0, 4, 4),
(5, 2, 'Déçu par le service. Rendez-vous retardé de 30 minutes.', '2025-12-12 02:47:48', NULL, 0, 5, 5),
(6, 4, 'Super marché avec de bons produits frais. Prix raisonnables.', '2025-12-13 02:47:48', NULL, 0, 6, 6),
(7, 5, 'Pharmacie très professionnelle. Personnel à l\'écoute.', '2025-12-14 02:47:48', NULL, 0, 4, 7),
(8, 3, 'Réparation correcte mais un peu chère. Livraison ponctuelle.', '2025-12-15 02:47:48', NULL, 0, 5, 8),
(9, 5, 'Deuxième visite, toujours aussi satisfait!', '2025-12-16 02:47:48', NULL, 0, 6, 1),
(10, 4, 'Parfait pour un petit déjeuner entre amis.', '2025-12-16 02:47:48', NULL, 0, 4, 2);

-- --------------------------------------------------------

--
-- Structure de la table `review_photo`
--

CREATE TABLE `review_photo` (
  `id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `review_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `cin` varchar(255) DEFAULT NULL,
  `is_validated` tinyint(1) NOT NULL,
  `is_active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`, `role`, `created_at`, `photo`, `cin`, `is_validated`, `is_active`) VALUES
(1, 'Admin', 'admin@example.com', '$2y$13$eW8oQTz44Od56PQhtTaShOToR75PSY.Adtl9YQzeis6hnlmNXY3pW', 'ADMIN', '2025-12-16 02:41:21', 'p3-6940bbb5af9d1.jpg', NULL, 1, 1),
(2, 'Service Owner 1', 'owner1@example.com', '$2y$13$dp5wp.OfEk32GK8uiW4dcO013s35sABqhVoDu6CLjP1N0UyuSbIEW', 'SERVICE_OWNER', '2025-12-16 02:47:48', 'profil1-6940bbcc68699.jpg', 'B654321', 1, 1),
(3, 'Service Owner 2', 'owner2@example.com', '$2y$13$K0OBipBCSYZrOehosA4DlOuqi2.oGgMW970aa7znscpiJJ6VfxgkK', 'SERVICE_OWNER', '2025-12-16 02:47:48', 'p1-6940bb586b858.jpg', 'C789012', 1, 1),
(4, 'Customer 1', 'customer1@example.com', '$2y$13$49WpAdeLShJxeaydPbyeMuFCCSVdJ6pMS8ssU25/Ju1cZQPXJ0y0G', 'CUSTOMER', '2025-12-16 02:47:48', 'p2-6940bb841a1be.jpg', NULL, 1, 1),
(5, 'Customer 2', 'customer2@example.com', '$2y$13$baFF95WAU9V9jFLLaIvkzevMO2JmisylmQTncRvq/ekBKijHT3uQe', 'CUSTOMER', '2025-12-16 02:47:48', 'profil2-6940bb9184846.webp', NULL, 1, 1),
(6, 'Customer 3', 'customer3@example.com', '$2y$13$YkA/fDIoCuKsAAFGnuRYX.eJRaAd1BtBqVvo1RhtanVpDu8AaDVva', 'CUSTOMER', '2025-12-16 02:47:48', 'profil3-6940bba0ad723.jpg', NULL, 1, 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `business`
--
ALTER TABLE `business`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_8D36E387E3C61F9` (`owner_id`),
  ADD KEY `IDX_8D36E3812469DE2` (`category_id`);

--
-- Index pour la table `business_photo`
--
ALTER TABLE `business_photo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_6A5663A7A89DB457` (`business_id`);

--
-- Index pour la table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `favorite_business`
--
ALTER TABLE `favorite_business`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_A28476C9A76ED395` (`user_id`),
  ADD KEY `IDX_A28476C9A89DB457` (`business_id`);

--
-- Index pour la table `like_review`
--
ALTER TABLE `like_review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_479A9234A76ED395` (`user_id`),
  ADD KEY `IDX_479A92343E2E969B` (`review_id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Index pour la table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_BF5476CAA76ED395` (`user_id`);

--
-- Index pour la table `report_review`
--
ALTER TABLE `report_review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_50B05459A76ED395` (`user_id`),
  ADD KEY `IDX_50B054593E2E969B` (`review_id`);

--
-- Index pour la table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_794381C6A76ED395` (`user_id`),
  ADD KEY `IDX_794381C6A89DB457` (`business_id`);

--
-- Index pour la table `review_photo`
--
ALTER TABLE `review_photo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_739A8033E2E969B` (`review_id`),
  ADD KEY `IDX_739A803A76ED395` (`user_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `business`
--
ALTER TABLE `business`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `business_photo`
--
ALTER TABLE `business_photo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `favorite_business`
--
ALTER TABLE `favorite_business`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `like_review`
--
ALTER TABLE `like_review`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `report_review`
--
ALTER TABLE `report_review`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `review`
--
ALTER TABLE `review`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `review_photo`
--
ALTER TABLE `review_photo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `business`
--
ALTER TABLE `business`
  ADD CONSTRAINT `FK_8D36E3812469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
  ADD CONSTRAINT `FK_8D36E387E3C61F9` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `business_photo`
--
ALTER TABLE `business_photo`
  ADD CONSTRAINT `FK_6A5663A7A89DB457` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`);

--
-- Contraintes pour la table `favorite_business`
--
ALTER TABLE `favorite_business`
  ADD CONSTRAINT `FK_A28476C9A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_A28476C9A89DB457` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`);

--
-- Contraintes pour la table `like_review`
--
ALTER TABLE `like_review`
  ADD CONSTRAINT `FK_479A92343E2E969B` FOREIGN KEY (`review_id`) REFERENCES `review` (`id`),
  ADD CONSTRAINT `FK_479A9234A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `FK_BF5476CAA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `report_review`
--
ALTER TABLE `report_review`
  ADD CONSTRAINT `FK_50B054593E2E969B` FOREIGN KEY (`review_id`) REFERENCES `review` (`id`),
  ADD CONSTRAINT `FK_50B05459A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `FK_794381C6A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_794381C6A89DB457` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`);

--
-- Contraintes pour la table `review_photo`
--
ALTER TABLE `review_photo`
  ADD CONSTRAINT `FK_739A8033E2E969B` FOREIGN KEY (`review_id`) REFERENCES `review` (`id`),
  ADD CONSTRAINT `FK_739A803A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
