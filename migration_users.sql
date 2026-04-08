-- ============================================================
-- MIGRATION UTILISATEURS WORDPRESS → rm_clients
-- ============================================================
-- INSTRUCTIONS :
-- 1. Ouvrez phpMyAdmin → base u173818135_UkbDn
-- 2. Cliquez sur "SQL"
-- 3. Copiez-collez CE FICHIER ENTIER et cliquez "Exécuter"
-- ============================================================

-- Étape 0 : S'assurer que la table rm_clients existe
CREATE TABLE IF NOT EXISTS `rm_clients` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nom`          VARCHAR(100) NOT NULL,
  `prenom`       VARCHAR(100) NOT NULL,
  `email`        VARCHAR(255) NOT NULL UNIQUE,
  `telephone`    VARCHAR(30),
  `adresse`      TEXT,
  `ville`        VARCHAR(100) DEFAULT 'Abidjan',
  `password`     VARCHAR(255) NOT NULL,
  `wp_user_id`   INT UNSIGNED DEFAULT NULL COMMENT 'ID WordPress original',
  `mdp_reset`    TINYINT(1) DEFAULT 1 COMMENT '1 = doit changer son mdp',
  `actif`        TINYINT(1) DEFAULT 1,
  `created_at`   DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Étape 1 : Migrer les utilisateurs WordPress
-- (exclut les administrateurs)
-- ============================================================
INSERT IGNORE INTO rm_clients (
    nom,
    prenom,
    email,
    telephone,
    adresse,
    ville,
    password,
    wp_user_id,
    mdp_reset,
    actif,
    created_at
)
SELECT
    -- Nom de famille (billing_last_name ou display_name)
    COALESCE(
        NULLIF(TRIM((SELECT meta_value FROM wp_usermeta WHERE user_id = u.ID AND meta_key = 'billing_last_name' LIMIT 1)), ''),
        NULLIF(TRIM(SUBSTRING_INDEX(u.display_name, ' ', -1)), ''),
        'Inconnu'
    ) AS nom,

    -- Prénom (billing_first_name ou première partie du display_name)
    COALESCE(
        NULLIF(TRIM((SELECT meta_value FROM wp_usermeta WHERE user_id = u.ID AND meta_key = 'billing_first_name' LIMIT 1)), ''),
        NULLIF(TRIM(SUBSTRING_INDEX(u.display_name, ' ', 1)), ''),
        u.user_login
    ) AS prenom,

    -- Email
    u.user_email AS email,

    -- Téléphone (billing_phone WooCommerce)
    COALESCE(
        NULLIF(TRIM((SELECT meta_value FROM wp_usermeta WHERE user_id = u.ID AND meta_key = 'billing_phone' LIMIT 1)), ''),
        NULL
    ) AS telephone,

    -- Adresse (billing_address_1 + billing_address_2)
    NULLIF(TRIM(CONCAT(
        COALESCE((SELECT meta_value FROM wp_usermeta WHERE user_id = u.ID AND meta_key = 'billing_address_1' LIMIT 1), ''),
        ' ',
        COALESCE((SELECT meta_value FROM wp_usermeta WHERE user_id = u.ID AND meta_key = 'billing_address_2' LIMIT 1), '')
    )), ' ') AS adresse,

    -- Ville (billing_city WooCommerce)
    COALESCE(
        NULLIF(TRIM((SELECT meta_value FROM wp_usermeta WHERE user_id = u.ID AND meta_key = 'billing_city' LIMIT 1)), ''),
        'Abidjan'
    ) AS ville,

    -- Mot de passe : hash WordPress conservé temporairement
    -- (les users devront réinitialiser leur mdp — voir reset_password.php)
    u.user_pass AS password,

    -- ID WordPress original (pour référence)
    u.ID AS wp_user_id,

    -- Doit changer son mot de passe (1 = oui)
    1 AS mdp_reset,

    -- Actif
    1 AS actif,

    -- Date d'inscription originale
    u.user_registered AS created_at

FROM wp_users u

-- Exclure les administrateurs WordPress
WHERE u.ID NOT IN (
    SELECT user_id FROM wp_usermeta
    WHERE meta_key = 'wp_capabilities'
    AND meta_value LIKE '%administrator%'
)
-- Exclure les emails vides
AND u.user_email != ''
AND u.user_email IS NOT NULL;

-- ============================================================
-- Étape 2 : Vérifier le résultat
-- ============================================================
SELECT
    COUNT(*) AS total_migres,
    COUNT(telephone) AS avec_telephone,
    COUNT(adresse) AS avec_adresse,
    SUM(mdp_reset) AS doivent_changer_mdp
FROM rm_clients;

-- ============================================================
-- Étape 3 : Voir la liste migrée
-- ============================================================
SELECT
    id,
    prenom,
    nom,
    email,
    telephone,
    ville,
    DATE(created_at) AS inscrit_le,
    IF(mdp_reset=1, '⚠️ Reset requis', '✅ OK') AS statut_mdp
FROM rm_clients
ORDER BY created_at DESC;
