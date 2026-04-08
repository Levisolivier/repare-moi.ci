<?php
// ============================================================
// REPARE-MOI.CI — Configuration principale
// ⚠️  Modifiez ces valeurs avant de mettre en ligne
// ============================================================

define('SITE_NOM',    'Repare-Moi');
define('SITE_URL',    'https://repare-moi.ci');       // Sans slash final
define('SITE_EMAIL',  'info@repare-moi.ci');
define('SITE_TEL1',   '+2250545362890');
define('SITE_TEL2',   '+2250707907719');
define('SITE_WHATSAPP', '2250545362890');

// Devise
define('DEVISE',      'FCFA');

// Dossier uploads
define('UPLOAD_DIR',  __DIR__ . '/../uploads/produits/');
define('UPLOAD_URL',  SITE_URL . '/uploads/produits/');

// Clé secrète pour les tokens CSRF (changez cette valeur !)
define('SECRET_KEY',  'rm_ci_2025_changez_cette_cle_!@#');

// Durée de session (en secondes) — 2h
define('SESSION_DUREE', 7200);

// Livraison
define('FRAIS_LIVRAISON', 1000);   // FCFA — 0 pour gratuit
define('LIVRAISON_GRATUITE_MIN', 50000); // Gratuit au-dessus de cette somme

// Environnement : 'dev' ou 'prod'
define('ENV', 'dev');

// Affichage erreurs selon environnement
if (ENV === 'dev') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Fuseau horaire
date_default_timezone_set('Africa/Abidjan');

// Démarrage session sécurisée
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => SESSION_DUREE,
        'path'     => '/',
        'secure'   => (ENV === 'prod'),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// ============================================================
// GOOGLE SHEETS — Synchronisation produits
// Collez ici l'URL de votre Google Apps Script déployé
// ============================================================
define('SHEETS_URL', 'https://script.google.com/macros/s/AKfycbzX8-fL2BvFnwW01T1VOc9GgZmuJACv6EAqnh3jXqB15unFnG6CU9J9yarsZ5RluH848g/exec');  // ← Collez votre URL ici après déploiement
