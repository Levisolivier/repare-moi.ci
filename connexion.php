<?php
require_once __DIR__ . '/includes/fonctions.php';

if (client_connecte()) { header('Location: ' . SITE_URL . '/compte.php'); exit; }

$erreur  = '';
$redirect = $_GET['redirect'] ?? '/compte.php';
// Valider que le redirect est un chemin interne
if (!preg_match('#^/[^/\\\]#', $redirect)) $redirect = '/compte.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = trim($_POST['email'] ?? '');
    $mdp   = $_POST['password'] ?? '';

    $pdo = getPDO();
    $st  = $pdo->prepare('SELECT * FROM rm_clients WHERE email=? AND actif=1');
    $st->execute([$email]);
    $client = $st->fetch();

    if ($client) {
        $hash = $client['password'];
        $ok   = false;

        // Hash bcrypt (nouveau site)
        if (password_verify($mdp, $hash)) {
            $ok = true;
        }
        // Hash WordPress phpass ($P$ ou $H$) — utilisateurs migrés
        elseif (substr($hash, 0, 3) === '$P$' || substr($hash, 0, 3) === '$H$') {
            require_once __DIR__ . '/includes/phpass.php';
            $wp_hasher = new PasswordHash(8, true);
            if ($wp_hasher->CheckPassword($mdp, $hash)) {
                $ok = true;
                // Migrer vers bcrypt immédiatement
                $pdo->prepare('UPDATE rm_clients SET password=? WHERE id=?')
                    ->execute([password_hash($mdp, PASSWORD_BCRYPT, ['cost'=>12]), $client['id']]);
            }
        }

        if ($ok) {
            $_SESSION['client_id']  = $client['id'];
            $_SESSION['client_nom'] = $client['prenom'];
            // Utilisateur migré WP → forcer reset mot de passe
            if (!empty($client['mdp_reset'])) {
                flash('Bienvenue ' . $client['prenom'] . ' ! Veuillez choisir un nouveau mot de passe pour sécuriser votre compte.', 'warning');
                header('Location: ' . SITE_URL . '/reset-password.php');
                exit;
            }
            flash('Bienvenue, ' . $client['prenom'] . ' !');
            header('Location: ' . SITE_URL . $redirect);
            exit;
        } else {
            $erreur = 'Email ou mot de passe incorrect.';
        }
    } else {
        $erreur = 'Email ou mot de passe incorrect.';
    }
}

$pageTitle = 'Connexion — Repare-Moi';
require_once __DIR__ . '/includes/header.php';
?>
<div class="container auth-container">
  <div class="auth-card">
    <h1 class="auth-title">Connexion</h1>
    <?php if ($erreur): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= e($erreur) ?></div>
    <?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <div class="form-group"><label>Email</label><input type="email" name="email" required autofocus></div>
      <div class="form-group"><label>Mot de passe</label><input type="password" name="password" required></div>
      <button type="submit" class="btn-primary btn-full">Se connecter</button>
    </form>
    <p style="text-align:center;margin-top:16px;font-size:13px">
      <a href="<?= SITE_URL ?>/reset-password.php" style="color:#888;font-size:12px">Mot de passe oublié ?</a><br><br>Pas encore de compte ? <a href="<?= SITE_URL ?>/inscription.php" style="color:var(--primary)">Créer un compte</a>
    </p>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
