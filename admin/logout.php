<?php
require_once __DIR__ . '/../includes/fonctions.php';
unset($_SESSION['admin_id'], $_SESSION['admin_nom'], $_SESSION['admin_role']);
session_regenerate_id(true);
session_destroy();
header('Location: ' . SITE_URL . '/admin/login.php');
exit;
