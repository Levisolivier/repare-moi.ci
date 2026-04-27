-- ============================================================
-- REPARE-MOI.CI — Schéma base de données COMPLET
-- Base : u173818135_UkbDn
-- À importer via phpMyAdmin → SQL → Exécuter
-- ============================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- TABLE : rm_produits
-- ============================================================
CREATE TABLE IF NOT EXISTS `rm_produits` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nom`          VARCHAR(255) NOT NULL,
  `slug`         VARCHAR(255) NOT NULL UNIQUE,
  `description`  TEXT,
  `marque`       VARCHAR(100) NOT NULL,
  `categorie`    VARCHAR(100) NOT NULL,
  `serie`        VARCHAR(50)  DEFAULT '',
  `couleur`      VARCHAR(50)  DEFAULT NULL,
  `prix`         DECIMAL(12,0) NOT NULL DEFAULT 0,
  `stock`        INT NOT NULL DEFAULT 0,
  `image`        VARCHAR(500) DEFAULT '',
  `deal`         TINYINT(1)   DEFAULT 0,
  `actif`        TINYINT(1)   DEFAULT 1,
  `vues`         INT          DEFAULT 0,
  `created_at`   DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_marque`    (`marque`),
  INDEX `idx_categorie` (`categorie`),
  INDEX `idx_actif`     (`actif`),
  INDEX `idx_stock`     (`stock`),
  INDEX `idx_deal`      (`deal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : rm_clients
-- ============================================================
CREATE TABLE IF NOT EXISTS `rm_clients` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nom`          VARCHAR(100) NOT NULL,
  `prenom`       VARCHAR(100) NOT NULL,
  `email`        VARCHAR(255) NOT NULL UNIQUE,
  `telephone`    VARCHAR(30)  DEFAULT NULL,
  `adresse`      TEXT,
  `ville`        VARCHAR(100) DEFAULT 'Abidjan',
  `password`     VARCHAR(255) NOT NULL,
  `wp_user_id`   INT UNSIGNED DEFAULT NULL COMMENT 'ID WordPress original (migration)',
  `mdp_reset`    TINYINT(1)   DEFAULT 0   COMMENT '1 = doit changer son mot de passe',
  `actif`        TINYINT(1)   DEFAULT 1,
  `created_at`   DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email`     (`email`),
  INDEX `idx_actif`     (`actif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : rm_admins
-- ============================================================
CREATE TABLE IF NOT EXISTS `rm_admins` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nom`          VARCHAR(100) NOT NULL,
  `email`        VARCHAR(255) NOT NULL UNIQUE,
  `password`     VARCHAR(255) NOT NULL,
  `role`         ENUM('super_admin','admin','editeur') DEFAULT 'admin',
  `last_login`   DATETIME     DEFAULT NULL,
  `created_at`   DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : rm_commandes
-- ============================================================
CREATE TABLE IF NOT EXISTS `rm_commandes` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `reference`    VARCHAR(20)  NOT NULL UNIQUE,
  `client_id`    INT UNSIGNED DEFAULT NULL,
  `client_nom`   VARCHAR(200) NOT NULL,
  `client_email` VARCHAR(255) NOT NULL,
  `client_tel`   VARCHAR(30)  NOT NULL,
  `adresse`      TEXT         NOT NULL,
  `ville`        VARCHAR(100) DEFAULT 'Abidjan',
  `total`        DECIMAL(12,0) NOT NULL,
  `statut`       ENUM('en_attente','confirmee','en_cours','livree','annulee') DEFAULT 'en_attente',
  `paiement`     ENUM('orange_money','mtn_money','wave','moov','especes')     DEFAULT 'especes',
  `notes`        TEXT,
  `created_at`   DATETIME     DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`client_id`) REFERENCES `rm_clients`(`id`) ON DELETE SET NULL,
  INDEX `idx_statut`    (`statut`),
  INDEX `idx_reference` (`reference`),
  INDEX `idx_created`   (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : rm_commandes_items
-- ============================================================
CREATE TABLE IF NOT EXISTS `rm_commandes_items` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `commande_id`   INT UNSIGNED NOT NULL,
  `produit_id`    INT UNSIGNED DEFAULT NULL,
  `nom_produit`   VARCHAR(255) NOT NULL,
  `prix_unitaire` DECIMAL(12,0) NOT NULL,
  `quantite`      INT          NOT NULL DEFAULT 1,
  `sous_total`    DECIMAL(12,0) NOT NULL,
  FOREIGN KEY (`commande_id`) REFERENCES `rm_commandes`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`produit_id`)  REFERENCES `rm_produits`(`id`)  ON DELETE SET NULL,
  INDEX `idx_commande` (`commande_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- ADMIN PAR DÉFAUT
-- Email    : admin@repare-moi.ci
-- Mot de passe : Admin2025!
-- ⚠️ Changez le mot de passe immédiatement après connexion !
-- ============================================================
INSERT IGNORE INTO `rm_admins` (`nom`, `email`, `password`, `role`) VALUES
('Administrateur', 'admin@repare-moi.ci',
 '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'super_admin');

-- ============================================================
-- Import terminé avec succès !
-- Tables créées : rm_produits, rm_clients, rm_admins,
--                 rm_commandes, rm_commandes_items
-- Admin : admin@repare-moi.ci / Admin2025!
-- ============================================================
SELECT 'Installation terminee avec succes !' AS Statut;
