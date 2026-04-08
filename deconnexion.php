<?php
require_once __DIR__ . '/includes/fonctions.php';
unset($_SESSION['client_id'], $_SESSION['client_nom']);
session_regenerate_id(true);
flash('Vous êtes déconnecté.');
header('Location: ' . SITE_URL . '/');
exit;
