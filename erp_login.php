<?php
$DB_HOST = '127.0.0.1';
$DB_NAME = 'u173818135_UkbDn';
$DB_USER = 'u173818135_FS8Nz';
$DB_PASS = 'RepareMoi2025!';
$error   = '';

// Deja connecte ?
if (!empty($_COOKIE['erp_token'])) {
    header('Location: ERP_Dashboard_v3.html');
    exit;
}

// Traitement formulaire
if (!empty($_POST['login']) && !empty($_POST['password'])) {
    $login = strtolower(trim($_POST['login']));
    $pass  = trim($_POST['password']);
    try {
        $pdo = new PDO('mysql:host='.$DB_HOST.';dbname='.$DB_NAME.';charset=utf8mb4', $DB_USER, $DB_PASS);
        $st  = $pdo->prepare('SELECT * FROM erp_users WHERE login=? AND actif=1 LIMIT 1');
        $st->execute(array($login));
        $user = $st->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($pass, $user['password'])) {
            $token = bin2hex(random_bytes(32));
            $exp   = time() + 28800;
            $pdo->prepare('INSERT INTO erp_sessions (token,user_id,login,expires_at,ip,user_agent) VALUES (?,?,?,?,?,?)')
                ->execute(array($token,$user['id'],$user['login'],date('Y-m-d H:i:s',$exp),
                    @$_SERVER['REMOTE_ADDR'], substr(@$_SERVER['HTTP_USER_AGENT'],0,255)));
            $pdo->prepare('UPDATE erp_users SET last_login=NOW() WHERE id=?')->execute(array($user['id']));
            setcookie('erp_token', $token, $exp, '/');
            setcookie('erp_user', json_encode(array(
                'login'=>$user['login'],'nom'=>$user['nom'],
                'role'=>$user['role'],'color'=>$user['color'],'bg'=>$user['bg']
            )), $exp, '/');
            header('Location: ERP_Dashboard_v3.html');
            exit;
        } else {
            $error = 'Identifiant ou mot de passe incorrect';
        }
    } catch (Exception $e) {
        $error = 'Erreur: ' . $e->getMessage();
    }
}
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>REPARE-MOI CI - Connexion</title>
<style>
body{margin:0;padding:20px;background:#111827;font-family:Arial,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center}
.c{background:#1F2937;padding:32px;border-radius:12px;width:100%;max-width:360px}
.t{text-align:center;margin-bottom:24px}
.ico{width:54px;height:54px;background:#FF6A00;border-radius:12px;font-size:24px;font-weight:900;color:#fff;line-height:54px;text-align:center;margin:0 auto 12px}
h1{color:#fff;font-size:18px;margin:0 0 4px}
p{color:#6B7280;font-size:12px;margin:0}
label{display:block;color:#9CA3AF;font-size:11px;font-weight:700;text-transform:uppercase;margin:14px 0 5px}
input{width:100%;padding:12px;background:#111827;border:2px solid #374151;border-radius:8px;color:#fff;font-size:15px}
input[type=submit]{background:#FF6A00;border-color:#FF6A00;color:#fff;font-weight:800;font-size:15px;cursor:pointer;margin-top:16px}
input[type=submit]:hover{background:#E55A00}
.e{background:#7F1D1D;color:#FCA5A5;padding:12px;border-radius:8px;margin-top:12px;font-size:13px;text-align:center}
</style>
</head>
<body>
<div class="c">
  <div class="t">
    <div class="ico">R</div>
    <h1>REPARE-MOI CI</h1>
    <p>Gestion interne</p>
  </div>
  <form method="post">
    <label>Identifiant</label>
    <input type="text" name="login" value="<?php echo htmlspecialchars(isset($_POST['login'])?$_POST['login']:''); ?>" required>
    <label>Mot de passe</label>
    <input type="password" name="password" required>
    <input type="submit" value="Se connecter">
    <?php if($error!==''): ?><div class="e"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
  </form>
</div>
</body>
</html>
