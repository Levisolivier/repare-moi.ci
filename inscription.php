<?php
require_once __DIR__ . '/includes/fonctions.php';

if (client_connecte()) { header('Location: ' . SITE_URL . '/compte.php'); exit; }

$erreurs = [];
$vals    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $vals = [
        'prenom'    => trim($_POST['prenom'] ?? ''),
        'nom'       => trim($_POST['nom'] ?? ''),
        'email'     => trim($_POST['email'] ?? ''),
        'telephone' => trim($_POST['telephone'] ?? ''),
        'adresse'   => trim($_POST['adresse'] ?? ''),
        'ville'     => trim($_POST['ville'] ?? 'Abidjan'),
        'password'  => $_POST['password'] ?? '',
        'password2' => $_POST['password2'] ?? '',
    ];

    if (strlen($vals['prenom']) < 2)  $erreurs[] = 'Prénom requis.';
    if (strlen($vals['nom']) < 2)     $erreurs[] = 'Nom requis.';
    if (!filter_var($vals['email'],FILTER_VALIDATE_EMAIL)) $erreurs[] = 'Email invalide.';
    if (strlen($vals['password']) < 8) $erreurs[] = 'Mot de passe : minimum 8 caractères.';
    if ($vals['password'] !== $vals['password2']) $erreurs[] = 'Les mots de passe ne correspondent pas.';

    if (!$erreurs) {
        $pdo = getPDO();
        $existe = $pdo->prepare('SELECT id FROM rm_clients WHERE email=?');
        $existe->execute([$vals['email']]);
        if ($existe->fetch()) {
            $erreurs[] = 'Cet email est déjà utilisé.';
        } else {
            $st = $pdo->prepare('INSERT INTO rm_clients (nom,prenom,email,telephone,adresse,ville,password) VALUES (?,?,?,?,?,?,?)');
            $st->execute([
                $vals['nom'], $vals['prenom'], $vals['email'],
                $vals['telephone'], $vals['adresse'], $vals['ville'],
                password_hash($vals['password'], PASSWORD_BCRYPT, ['cost'=>12])
            ]);
            $_SESSION['client_id']  = (int)$pdo->lastInsertId();
            $_SESSION['client_nom'] = $vals['prenom'];
            flash('Bienvenue ' . $vals['prenom'] . ' ! Votre compte a été créé.');
            header('Location: ' . SITE_URL . '/compte.php');
            exit;
        }
    }
}

$pageTitle = 'Créer un compte — Repare-Moi';
require_once __DIR__ . '/includes/header.php';
?>
<div class="container auth-container" style="max-width:560px">
  <div class="auth-card">
    <h1 class="auth-title">Créer un compte</h1>
    <?php if ($erreurs): ?>
    <div class="alert alert-error">
      <?php foreach ($erreurs as $e): ?><div><i class="fas fa-exclamation-circle"></i> <?= e($e) ?></div><?php endforeach; ?>
    </div>
    <?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div class="form-group"><label>Prénom *</label><input type="text" name="prenom" value="<?= e($vals['prenom']??'') ?>" required></div>
        <div class="form-group"><label>Nom *</label><input type="text" name="nom" value="<?= e($vals['nom']??'') ?>" required></div>
      </div>
      <div class="form-group"><label>Email *</label><input type="email" name="email" value="<?= e($vals['email']??'') ?>" required></div>
      <div class="form-group"><label>Téléphone</label><input type="tel" name="telephone" value="<?= e($vals['telephone']??'') ?>"></div>
      <div class="form-group"><label>Adresse</label><input type="text" name="adresse" value="<?= e($vals['adresse']??'') ?>"></div>
      <div class="form-group"><label>Ville</label><input type="text" name="ville" value="<?= e($vals['ville']??'Abidjan') ?>"></div>
      <div class="form-group"><label>Mot de passe * (min. 8 carac.)</label><input type="password" name="password" required minlength="8"></div>
      <div class="form-group"><label>Confirmer le mot de passe *</label><input type="password" name="password2" required></div>
      <button type="submit" class="btn-primary btn-full">Créer mon compte</button>
    </form>
    <p style="text-align:center;margin-top:16px;font-size:13px">
      Déjà un compte ? <a href="<?= SITE_URL ?>/connexion.php" style="color:var(--primary)">Se connecter</a>
    </p>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
