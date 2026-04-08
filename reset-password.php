<?php
// ============================================================
// REPARE-MOI.CI — Réinitialisation mot de passe
// ============================================================
require_once __DIR__ . '/includes/fonctions.php';

// ── Redirection si déjà connecté ──────────────────────────
if (client_connecte()) {
    header('Location: ' . SITE_URL . '/compte.php');
    exit;
}

$etape  = 'email';
$erreur = '';
$succes = '';

// ── Étape 1 : Soumettre email ─────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['etape'] ?? '') === 'email') {
    csrf_check();
    $email = trim(strtolower($_POST['email'] ?? ''));
    $pdo   = getPDO();
    $st    = $pdo->prepare('SELECT id, prenom FROM rm_clients WHERE email=? AND actif=1');
    $st->execute([$email]);
    $client = $st->fetch();

    if ($client) {
        $code    = random_int(100000, 999999);
        $expires = date('Y-m-d H:i:s', time() + 1800);

        $_SESSION['reset_email']   = $email;
        $_SESSION['reset_code']    = (string)$code;
        $_SESSION['reset_expires'] = $expires;
        $_SESSION['reset_prenom']  = $client['prenom'];
        $_SESSION['reset_attempts'] = 0;

        // TODO production : envoyer par email/SMS
        // mail($email, 'Code REPARE-MOI CI', "Votre code : $code\nValable 30 min.");

        $etape  = 'code';
        // En mode dev : afficher le code — RETIRER EN PRODUCTION
        $succes = defined('ENV') && ENV === 'dev'
            ? 'Code envoyé ! (dev: <strong>' . $code . '</strong>)'
            : 'Un code de vérification a été envoyé à votre adresse email.';
    } else {
        // Même message pour ne pas révéler si l'email existe
        $etape  = 'code';
        $_SESSION['reset_email']   = $email;
        $_SESSION['reset_code']    = 'INVALID';
        $_SESSION['reset_expires'] = date('Y-m-d H:i:s', time() + 1800);
        $succes = 'Si cet email est enregistré, vous recevrez un code de vérification.';
    }
}

// ── Étape 2 : Vérifier code ───────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['etape'] ?? '') === 'code') {
    csrf_check();
    $code_saisi = preg_replace('/\D/', '', trim($_POST['code'] ?? ''));

    // Anti-bruteforce : max 5 tentatives
    $_SESSION['reset_attempts'] = ($_SESSION['reset_attempts'] ?? 0) + 1;
    if ($_SESSION['reset_attempts'] > 5) {
        unset($_SESSION['reset_email'], $_SESSION['reset_code'], $_SESSION['reset_expires'], $_SESSION['reset_attempts']);
        $erreur = 'Trop de tentatives. Recommencez depuis le début.';
        $etape  = 'email';
    } elseif (
        isset($_SESSION['reset_code']) &&
        $_SESSION['reset_code'] !== 'INVALID' &&
        $_SESSION['reset_code'] === $code_saisi &&
        strtotime($_SESSION['reset_expires']) > time()
    ) {
        $_SESSION['reset_verified'] = true;
        $etape  = 'nouveau';
        $succes = 'Code vérifié ✅ Choisissez votre nouveau mot de passe.';
    } else {
        $reste = max(0, 5 - ($_SESSION['reset_attempts'] ?? 0));
        $erreur = 'Code incorrect ou expiré.' . ($reste > 0 ? " ($reste tentative(s) restante(s))" : '');
        $etape  = 'code';
    }
}

// ── Étape 3 : Nouveau mot de passe ───────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['etape'] ?? '') === 'nouveau') {
    csrf_check();
    if (empty($_SESSION['reset_verified']) || empty($_SESSION['reset_email'])) {
        $erreur = 'Session expirée. Recommencez.';
        $etape  = 'email';
    } else {
        $mdp  = $_POST['password']  ?? '';
        $mdp2 = $_POST['password2'] ?? '';

        if (strlen($mdp) < 8) {
            $erreur = 'Le mot de passe doit contenir au moins 8 caractères.';
            $etape  = 'nouveau';
        } elseif ($mdp !== $mdp2) {
            $erreur = 'Les mots de passe ne correspondent pas.';
            $etape  = 'nouveau';
        } else {
            $pdo  = getPDO();
            $hash = password_hash($mdp, PASSWORD_BCRYPT, ['cost' => 12]);
            $pdo->prepare('UPDATE rm_clients SET password=?, mdp_reset=0 WHERE email=?')
                ->execute([$hash, $_SESSION['reset_email']]);

            // Connexion automatique
            $row = $pdo->prepare('SELECT id, prenom FROM rm_clients WHERE email=?');
            $row->execute([$_SESSION['reset_email']]);
            $cl = $row->fetch();
            if ($cl) {
                $_SESSION['client_id']  = $cl['id'];
                $_SESSION['client_nom'] = $cl['prenom'];
            }

            // Nettoyer session reset
            foreach (['reset_email','reset_code','reset_expires','reset_prenom','reset_verified','reset_attempts'] as $k) {
                unset($_SESSION[$k]);
            }

            flash('Mot de passe mis à jour ! Bienvenue ' . e($cl['prenom'] ?? '') . ' 🎉');
            header('Location: ' . SITE_URL . '/compte.php');
            exit;
        }
    }
}

// Restaurer étape depuis session
if (empty($_POST)) {
    if (!empty($_SESSION['reset_verified'])) $etape = 'nouveau';
    elseif (!empty($_SESSION['reset_code']))  $etape = 'code';
}

$pageTitle = 'Réinitialisation mot de passe — REPARE-MOI CI';
require_once __DIR__ . '/includes/header.php';
?>

<style>
.reset-wrap{min-height:60vh;display:flex;align-items:center;justify-content:center;padding:32px 16px}
.reset-card{background:#fff;border-radius:14px;padding:36px 32px;width:100%;max-width:440px;box-shadow:0 4px 32px rgba(0,0,0,.09);border:1px solid #e5e7eb}
.steps{display:flex;align-items:center;justify-content:center;gap:0;margin-bottom:28px}
.step{display:flex;flex-direction:column;align-items:center;gap:6px;position:relative}
.step:not(:last-child)::after{content:'';position:absolute;top:14px;left:calc(50% + 14px);width:calc(100% - 4px);height:2px;background:#e5e7eb}
.step.done::after,.step.active::after{background:#F07818}
.step-dot{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;border:2px solid #e5e7eb;background:#fff;color:#9ca3af;position:relative;z-index:1;transition:.3s}
.step.active .step-dot{background:#F07818;border-color:#F07818;color:#fff;box-shadow:0 0 0 4px rgba(240,120,24,.15)}
.step.done .step-dot{background:#10b981;border-color:#10b981;color:#fff}
.step-label{font-size:11px;color:#9ca3af;font-weight:600;white-space:nowrap}
.step.active .step-label{color:#F07818}
.step.done .step-label{color:#10b981}
.step-connector{width:50px;height:2px;background:#e5e7eb;margin-top:-16px;flex-shrink:0}
.step-connector.done{background:#10b981}
.step-connector.active{background:#F07818}
.reset-title{font-size:22px;font-weight:800;text-align:center;color:#111827;margin-bottom:6px}
.reset-sub{font-size:13px;color:#6b7280;text-align:center;margin-bottom:22px;line-height:1.5}
.code-input{font-size:28px;letter-spacing:12px;text-align:center;font-weight:800;border:2px solid #e5e7eb;border-radius:10px;padding:14px;width:100%;outline:none;transition:.2s}
.code-input:focus{border-color:#F07818;box-shadow:0 0 0 4px rgba(240,120,24,.12)}
.btn-reset{width:100%;background:#F07818;color:#fff;border:none;padding:13px;border-radius:8px;font-size:15px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:.2s;margin-top:6px}
.btn-reset:hover{background:#C85F08;transform:translateY(-1px)}
.alert{padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;display:flex;align-items:flex-start;gap:8px}
.alert-error{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}
.alert-success{background:#dcfce7;color:#15803d;border:1px solid #86efac}
.form-label{display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:#374151}
.form-input{width:100%;border:1px solid #e5e7eb;border-radius:8px;padding:11px 14px;font-size:14px;outline:none;transition:.2s;background:#fff}
.form-input:focus{border-color:#F07818;box-shadow:0 0 0 3px rgba(240,120,24,.1)}
.pass-strength{height:4px;border-radius:2px;margin-top:6px;transition:.3s;background:#e5e7eb}
.back-link{text-align:center;margin-top:16px;font-size:13px}
.back-link a{color:#F07818;font-weight:600}
.back-link a:hover{text-decoration:underline}
</style>

<div class="reset-wrap">
  <div class="reset-card">

    <!-- Stepper -->
    <?php
    $steps = ['email'=>['Email','1'],'code'=>['Code','2'],'nouveau'=>['Nouveau MDP','3']];
    $step_keys = array_keys($steps);
    $current_idx = array_search($etape, $step_keys);
    ?>
    <div class="steps">
      <?php foreach($steps as $k=>[$lbl,$num]):
        $idx = array_search($k, $step_keys);
        $cls = $idx < $current_idx ? 'done' : ($idx === $current_idx ? 'active' : '');
      ?>
        <?php if($idx > 0): ?>
        <div class="step-connector <?= $idx <= $current_idx ? ($idx < $current_idx ? 'done' : 'active') : '' ?>"></div>
        <?php endif; ?>
        <div class="step <?= $cls ?>">
          <div class="step-dot"><?= $cls === 'done' ? '✓' : $num ?></div>
          <div class="step-label"><?= $lbl ?></div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if ($erreur): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <span><?= e($erreur) ?></span></div>
    <?php endif; ?>
    <?php if ($succes): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <span><?= $succes ?></span></div>
    <?php endif; ?>

    <?php if ($etape === 'email'): ?>
    <!-- ── ÉTAPE 1 ── -->
    <h1 class="reset-title">🔐 Mot de passe oublié ?</h1>
    <p class="reset-sub">Entrez votre adresse email — nous vous enverrons un code de vérification.</p>
    <form method="post" autocomplete="off">
      <input type="hidden" name="csrf"  value="<?= csrf_token() ?>">
      <input type="hidden" name="etape" value="email">
      <div style="margin-bottom:14px">
        <label class="form-label"><i class="fas fa-envelope" style="color:#F07818;margin-right:6px"></i>Adresse email</label>
        <input class="form-input" type="email" name="email" required autofocus placeholder="votre@email.com">
      </div>
      <button type="submit" class="btn-reset"><i class="fas fa-paper-plane"></i> Envoyer le code</button>
    </form>

    <?php elseif ($etape === 'code'): ?>
    <!-- ── ÉTAPE 2 ── -->
    <h1 class="reset-title">📩 Code de vérification</h1>
    <p class="reset-sub">Entrez le code à 6 chiffres envoyé à<br><strong style="color:#111"><?= e($_SESSION['reset_email'] ?? '') ?></strong><br><small style="color:#9ca3af">Valable 30 minutes</small></p>
    <form method="post" autocomplete="off">
      <input type="hidden" name="csrf"  value="<?= csrf_token() ?>">
      <input type="hidden" name="etape" value="code">
      <div style="margin-bottom:14px">
        <input class="code-input" type="text" name="code" required maxlength="6"
               placeholder="• • • • • •" inputmode="numeric" pattern="[0-9]{6}"
               oninput="this.value=this.value.replace(/\D/g,'')">
      </div>
      <button type="submit" class="btn-reset"><i class="fas fa-check-circle"></i> Vérifier le code</button>
    </form>
    <p style="text-align:center;margin-top:14px;font-size:12px;color:#9ca3af">
      Pas reçu ? <a href="<?= SITE_URL ?>/reset-password.php" style="color:#F07818;font-weight:600">Renvoyer un code</a>
    </p>

    <?php else: ?>
    <!-- ── ÉTAPE 3 ── -->
    <h1 class="reset-title">🔑 Nouveau mot de passe</h1>
    <p class="reset-sub">Bonjour <strong style="color:#111"><?= e($_SESSION['reset_prenom'] ?? '') ?></strong> ! Choisissez un mot de passe sécurisé.</p>
    <form method="post" autocomplete="new-password" id="formMdp">
      <input type="hidden" name="csrf"  value="<?= csrf_token() ?>">
      <input type="hidden" name="etape" value="nouveau">
      <div style="margin-bottom:14px">
        <label class="form-label"><i class="fas fa-lock" style="color:#F07818;margin-right:6px"></i>Nouveau mot de passe</label>
        <input class="form-input" type="password" name="password" id="pwd1" required minlength="8"
               placeholder="Minimum 8 caractères" oninput="checkStrength(this.value)">
        <div class="pass-strength" id="strength-bar"></div>
        <div id="strength-label" style="font-size:11px;color:#9ca3af;margin-top:4px"></div>
      </div>
      <div style="margin-bottom:16px">
        <label class="form-label"><i class="fas fa-lock" style="color:#F07818;margin-right:6px"></i>Confirmer le mot de passe</label>
        <input class="form-input" type="password" name="password2" id="pwd2" required minlength="8" placeholder="Répétez le mot de passe">
      </div>
      <button type="submit" class="btn-reset"><i class="fas fa-shield-alt"></i> Enregistrer le mot de passe</button>
    </form>
    <script>
    function checkStrength(v){
      const bar=document.getElementById('strength-bar');
      const lbl=document.getElementById('strength-label');
      let score=0;
      if(v.length>=8) score++;
      if(/[A-Z]/.test(v)) score++;
      if(/[0-9]/.test(v)) score++;
      if(/[^A-Za-z0-9]/.test(v)) score++;
      const levels=[['#ef4444','Faible'],['#f97316','Moyen'],['#eab308','Bon'],['#22c55e','Fort']];
      if(v.length===0){bar.style.background='#e5e7eb';lbl.textContent='';return;}
      bar.style.background=levels[score-1]?.[0]||'#ef4444';
      bar.style.width=(score/4*100)+'%';
      lbl.textContent=levels[score-1]?.[1]||'';
      lbl.style.color=levels[score-1]?.[0]||'#ef4444';
    }
    </script>
    <?php endif; ?>

    <div class="back-link">
      <a href="<?= SITE_URL ?>/connexion.php"><i class="fas fa-arrow-left"></i> Retour à la connexion</a>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
