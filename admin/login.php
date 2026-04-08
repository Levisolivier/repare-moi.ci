<?php
require_once __DIR__ . '/../includes/fonctions.php';

if (admin_connecte()) { header('Location: ' . SITE_URL . '/admin/'); exit; }

$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = trim($_POST['email'] ?? '');
    $mdp   = $_POST['password'] ?? '';
    $pdo   = getPDO();
    $st    = $pdo->prepare('SELECT * FROM rm_admins WHERE email=?');
    $st->execute([$email]);
    $admin = $st->fetch();
    if ($admin && password_verify($mdp, $admin['password'])) {
        $_SESSION['admin_id']   = $admin['id'];
        $_SESSION['admin_nom']  = $admin['nom'];
        $_SESSION['admin_role'] = $admin['role'];
        $pdo->prepare('UPDATE rm_admins SET last_login=NOW() WHERE id=?')->execute([$admin['id']]);
        header('Location: ' . SITE_URL . '/admin/');
        exit;
    }
    $erreur = 'Identifiants incorrects.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin — Repare-Moi</title>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Open Sans',sans-serif;background:#f0f0f0;display:flex;align-items:center;justify-content:center;min-height:100vh}
.login-card{background:#fff;border-radius:10px;padding:40px;width:100%;max-width:400px;box-shadow:0 4px 24px rgba(0,0,0,.1)}
.login-logo{text-align:center;margin-bottom:28px}
.login-logo span{font-size:28px;font-weight:700;font-family:'Open Sans',sans-serif}
.login-logo span em{color:#f76b1c;font-style:normal}
.login-logo small{display:block;font-size:12px;color:#888;margin-top:4px}
h1{font-size:18px;text-align:center;margin-bottom:20px;color:#222}
.form-group{margin-bottom:16px}
.form-group label{display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:#333}
.form-group input{width:100%;border:1px solid #ddd;border-radius:6px;padding:10px 14px;font-size:14px;outline:none;transition:.2s}
.form-group input:focus{border-color:#f76b1c}
.btn{width:100%;background:#f76b1c;color:#fff;border:none;padding:12px;border-radius:6px;font-size:15px;font-weight:700;cursor:pointer;transition:.2s}
.btn:hover{background:#d45c10}
.alert{background:#ffe0e0;border:1px solid #f88;border-radius:6px;padding:10px 14px;font-size:13px;color:#c00;margin-bottom:16px}
</style>
</head>
<body>
<div class="login-card">
  <div class="login-logo">
    <span>Repare<em>Moi</em></span>
    <small>Administration</small>
  </div>
  <?php if ($erreur): ?><div class="alert"><i class="fas fa-exclamation-circle"></i> <?= e($erreur) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <div class="form-group"><label>Email</label><input type="email" name="email" required autofocus></div>
    <div class="form-group"><label>Mot de passe</label><input type="password" name="password" required></div>
    <button type="submit" class="btn"><i class="fas fa-lock"></i> Connexion</button>
  </form>
</div>
</body>
</html>
