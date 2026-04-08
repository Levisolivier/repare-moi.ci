<?php
// confirmation.php
require_once __DIR__ . '/includes/fonctions.php';
$commande = $_SESSION['last_commande'] ?? null;
if (!$commande) { header('Location: ' . SITE_URL . '/'); exit; }
unset($_SESSION['last_commande']);
$pageTitle = 'Commande confirmée — Repare-Moi';
require_once __DIR__ . '/includes/header.php';
?>
<div class="container" style="padding:60px 0;text-align:center;max-width:600px;margin:0 auto">
  <div style="font-size:80px;margin-bottom:20px">✅</div>
  <h1 style="font-size:28px;margin-bottom:12px;color:var(--primary)">Commande confirmée !</h1>
  <p style="font-size:16px;color:var(--text-muted);margin-bottom:8px">Votre référence : <strong style="color:var(--text-dark)"><?= e($commande['ref']) ?></strong></p>
  <p style="font-size:16px;color:var(--text-muted);margin-bottom:24px">Montant total : <strong><?= prix($commande['total']) ?></strong></p>
  <p style="color:var(--text-muted);margin-bottom:32px">Nous vous contacterons sous peu pour confirmer votre commande et organiser la livraison.</p>
  <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
    <a href="https://wa.me/<?= SITE_WHATSAPP ?>?text=Bonjour, ma commande est <?= urlencode($commande['ref']) ?>" class="btn-primary"><i class="fab fa-whatsapp"></i> Suivre par WhatsApp</a>
    <a href="<?= SITE_URL ?>/" class="btn-outline">Retour à l'accueil</a>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
