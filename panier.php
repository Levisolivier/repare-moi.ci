<?php
require_once __DIR__ . '/includes/fonctions.php';

// ── Actions POST ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';

    if ($action === 'ajouter') {
        $id  = (int)($_POST['produit_id'] ?? 0);
        $qty = max(1, (int)($_POST['qty'] ?? 1));
        if (panier_ajouter($id, $qty)) flash('Article ajouté au panier ✅');
        else flash('Produit indisponible ou rupture de stock.', 'error');
        $redirect = $_POST['redirect'] ?? '/panier.php';
        // Valider que le redirect est un chemin interne (commence par /)
        if (!preg_match('#^/[^/\\\]#', $redirect)) $redirect = '/panier.php';
        header('Location: ' . SITE_URL . $redirect);
        exit;
    }

    if ($action === 'retirer') {
        panier_retirer((int)($_POST['produit_id'] ?? 0));
        header('Location: ' . SITE_URL . '/panier.php');
        exit;
    }

    if ($action === 'vider') {
        panier_vider();
        header('Location: ' . SITE_URL . '/panier.php');
        exit;
    }

    if ($action === 'commander') {
        // Validation
        $nom       = trim($_POST['nom'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $adresse   = trim($_POST['adresse'] ?? '');
        $ville     = trim($_POST['ville'] ?? 'Abidjan');
        $paiement  = $_POST['paiement'] ?? 'especes';
        $notes     = trim($_POST['notes'] ?? '');

        $erreurs = [];
        if (strlen($nom) < 2)         $erreurs[] = 'Nom complet requis.';
        if (!filter_var($email,FILTER_VALIDATE_EMAIL)) $erreurs[] = 'Email invalide.';
        if (strlen($telephone) < 8)   $erreurs[] = 'Téléphone requis.';
        if (strlen($adresse) < 5)     $erreurs[] = 'Adresse de livraison requise.';
        if (empty(panier_get()))      $erreurs[] = 'Votre panier est vide.';

        if ($erreurs) {
            flash(implode(' ', $erreurs), 'error');
            header('Location: ' . SITE_URL . '/panier.php');
            exit;
        }

        $id = creer_commande(compact('nom','email','telephone','adresse','ville','paiement','notes'));
        if ($id) {
            header('Location: ' . SITE_URL . '/confirmation.php');
        } else {
            flash('Erreur lors de la commande. Veuillez réessayer.', 'error');
            header('Location: ' . SITE_URL . '/panier.php');
        }
        exit;
    }
}

$panier = panier_get();
$total  = panier_total();
$frais  = ($total > 0 && $total < LIVRAISON_GRATUITE_MIN) ? FRAIS_LIVRAISON : 0;
$client_data = client_connecte() ? client() : [];
$pageTitle = 'Mon Panier — Repare-Moi';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
  <div class="breadcrumb">
    <a href="<?= SITE_URL ?>/">Accueil</a> <i class="fas fa-chevron-right"></i> <span>Mon Panier</span>
  </div>

  <?php if (empty($panier)): ?>
  <div class="empty-state" style="padding:80px 0">
    <i class="fas fa-shopping-cart"></i>
    <p>Votre panier est vide.</p>
    <a href="<?= SITE_URL ?>/catalogue.php" class="btn-primary" style="display:inline-block;margin-top:16px">
      <i class="fas fa-shopping-bag"></i> Voir le catalogue
    </a>
  </div>

  <?php else: ?>
  <div class="panier-layout">
    <!-- ARTICLES -->
    <div class="panier-items">
      <h2 class="section-title" style="margin-bottom:16px">🛒 Mon Panier (<?= panier_count() ?> article<?= panier_count()>1?'s':'' ?>)</h2>

      <?php foreach ($panier as $item): ?>
      <div class="panier-item">
        <div class="panier-img">
          <?php $img = $item['image'] ? (strpos($item['image'],'http')===0 ? $item['image'] : UPLOAD_URL.$item['image']) : ''; ?>
          <?= $img ? '<img src="'.e($img).'" alt="'.e($item['nom']).'">' : '<span>📱</span>' ?>
        </div>
        <div class="panier-info">
          <div class="panier-nom"><?= e($item['nom']) ?></div>
          <div class="panier-marque"><?= e($item['marque']) ?></div>
          <div class="panier-prix-unit"><?= prix((int)$item['prix']) ?> / unité</div>
        </div>
        <div class="panier-qty"><?= $item['qty'] ?> ×</div>
        <div class="panier-sous-total"><?= prix($item['prix'] * $item['qty']) ?></div>
        <form method="post">
          <input type="hidden" name="action" value="retirer">
          <input type="hidden" name="produit_id" value="<?= e($item['produit_id']) ?>">
          <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
          <button type="submit" class="btn-retirer" title="Retirer"><i class="fas fa-trash"></i></button>
        </form>
      </div>
      <?php endforeach; ?>

      <div class="panier-actions">
        <form method="post" style="display:inline">
          <input type="hidden" name="action" value="vider">
          <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
          <button type="submit" class="btn-outline" onclick="return confirm('Vider le panier ?')">
            <i class="fas fa-trash"></i> Vider le panier
          </button>
        </form>
        <a href="<?= SITE_URL ?>/catalogue.php" class="btn-outline"><i class="fas fa-arrow-left"></i> Continuer les achats</a>
      </div>
    </div>

    <!-- COMMANDE -->
    <div class="panier-commande">
      <div class="commande-recapitulatif">
        <h3>Récapitulatif</h3>
        <div class="recap-ligne"><span>Sous-total</span><span><?= prix($total) ?></span></div>
        <div class="recap-ligne"><span>Livraison</span><span><?= $frais > 0 ? prix($frais) : '<span style="color:green">Gratuite</span>' ?></span></div>
        <?php if ($total < LIVRAISON_GRATUITE_MIN && FRAIS_LIVRAISON > 0): ?>
        <div class="recap-note">Livraison gratuite à partir de <?= prix(LIVRAISON_GRATUITE_MIN) ?></div>
        <?php endif; ?>
        <div class="recap-total"><span>Total</span><span><?= prix($total + $frais) ?></span></div>
      </div>

      <div class="commande-form">
        <h3>Informations de livraison</h3>
        <form method="post">
          <input type="hidden" name="action" value="commander">
          <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

          <div class="form-group">
            <label>Nom complet *</label>
            <input type="text" name="nom" required value="<?= e(trim(($client_data['prenom']??'') . ' ' . ($client_data['nom']??''))) ?>">
          </div>
          <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" required value="<?= e($client_data['email']??'') ?>">
          </div>
          <div class="form-group">
            <label>Téléphone *</label>
            <input type="tel" name="telephone" required value="<?= e($client_data['telephone']??'') ?>">
          </div>
          <div class="form-group">
            <label>Adresse de livraison *</label>
            <textarea name="adresse" rows="2" required><?= e($client_data['adresse']??'') ?></textarea>
          </div>
          <div class="form-group">
            <label>Ville</label>
            <input type="text" name="ville" value="<?= e($client_data['ville']??'Abidjan') ?>">
          </div>
          <div class="form-group">
            <label>Mode de paiement *</label>
            <select name="paiement" required>
              <option value="orange_money">Orange Money</option>
              <option value="mtn_money">MTN Money</option>
              <option value="wave">Wave</option>
              <option value="moov">Moov Money</option>
              <option value="especes">Paiement à la livraison</option>
            </select>
          </div>
          <div class="form-group">
            <label>Notes (optionnel)</label>
            <textarea name="notes" rows="2" placeholder="Instructions particulières..."></textarea>
          </div>

          <button type="submit" class="btn-primary btn-full btn-lg">
            <i class="fas fa-check-circle"></i> Confirmer la commande
          </button>
        </form>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
