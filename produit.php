<?php
require_once __DIR__ . '/includes/fonctions.php';

$slug = trim($_GET['slug'] ?? '');
if (!$slug) { header('Location: ' . SITE_URL . '/catalogue.php'); exit; }

$pdo = getPDO();
$p   = get_produit_slug($slug);
if (!$p) { http_response_code(404); $pageTitle='Produit introuvable'; require_once __DIR__.'/includes/header.php'; echo '<div class="container" style="padding:60px 0;text-align:center"><h1>Produit introuvable</h1><a href="'.SITE_URL.'/catalogue.php" class="btn-primary" style="display:inline-block;margin-top:16px">Retour au catalogue</a></div>'; require_once __DIR__.'/includes/footer.php'; exit; }

// Incrémenter vues
$pdo->prepare('UPDATE rm_produits SET vues=vues+1 WHERE id=?')->execute([$p['id']]);

// Produits similaires
$similaires_st = $pdo->prepare('SELECT * FROM rm_produits WHERE actif=1 AND marque=? AND id!=? ORDER BY deal DESC LIMIT 4');
$similaires_st->execute([$p['marque'], $p['id']]);
$similaires = $similaires_st->fetchAll();

$rupture   = $p['stock'] < 1;
$img       = $p['image'] ? (strpos($p['image'],'http')===0 ? $p['image'] : UPLOAD_URL.$p['image']) : '';
$pageTitle = e($p['nom']) . ' — Repare-Moi';
$pageDesc  = 'Achetez '.e($p['nom']).' à '.prix((int)$p['prix']).'. Qualité garantie, livraison rapide CI.';

require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
  <div class="breadcrumb">
    <a href="<?= SITE_URL ?>/">Accueil</a> <i class="fas fa-chevron-right"></i>
    <a href="<?= SITE_URL ?>/catalogue.php?marque=<?= urlencode($p['marque']) ?>"><?= e($p['marque']) ?></a>
    <i class="fas fa-chevron-right"></i> <span><?= e($p['nom']) ?></span>
  </div>

  <div class="produit-layout">
    <!-- IMAGE -->
    <div class="produit-img-wrap">
      <?php if ($img): ?>
        <img src="<?= e($img) ?>" alt="<?= e($p['nom']) ?>" class="produit-main-img">
      <?php else: ?>
        <div class="produit-img-placeholder">📱</div>
      <?php endif; ?>
      <?php if ($rupture): ?><div class="produit-rupture-overlay">Rupture de stock</div><?php endif; ?>
    </div>

    <!-- INFOS -->
    <div class="produit-details">
      <div class="produit-badge"><?= e($p['marque']) ?></div>
      <h1 class="produit-titre"><?= e($p['nom']) ?></h1>
      <div class="produit-prix"><?= prix((int)$p['prix']) ?></div>
      <div class="produit-stock-info <?= $rupture?'out':'' ?>">
        <i class="fas fa-<?= $rupture?'times':'check' ?>-circle"></i>
        <?= $rupture ? '<strong>Rupture de stock</strong>' : '<strong>En stock</strong> — '.$p['stock'].' unité(s) disponible(s)' ?>
      </div>

      <?php if ($p['description']): ?>
      <div class="produit-desc"><?= nl2br(e($p['description'])) ?></div>
      <?php endif; ?>

      <div class="produit-meta">
        <span><i class="fas fa-tag"></i> <?= e($p['categorie']) ?></span>
        <?php if ($p['serie']): ?><span><i class="fas fa-layer-group"></i> Série <?= e($p['serie']) ?></span><?php endif; ?>
      </div>

      <?php if (!$rupture): ?>
      <form method="post" action="<?= SITE_URL ?>/panier.php" class="form-produit">
        <input type="hidden" name="action" value="ajouter">
        <input type="hidden" name="produit_id" value="<?= e($p['id']) ?>">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <input type="hidden" name="redirect" value="<?= e($_SERVER['REQUEST_URI']) ?>">
        <div class="qty-wrap">
          <label>Quantité :</label>
          <div class="qty-ctrl">
            <button type="button" onclick="changeQty(-1)">−</button>
            <input type="number" name="qty" id="qty" value="1" min="1" max="<?= $p['stock'] ?>">
            <button type="button" onclick="changeQty(1)">+</button>
          </div>
        </div>
        <div class="produit-btns">
          <button type="submit" class="btn-primary btn-full"><i class="fas fa-cart-plus"></i> Ajouter au panier</button>
          <a href="<?= SITE_URL ?>/panier.php" class="btn-outline btn-full"><i class="fas fa-shopping-cart"></i> Voir le panier</a>
        </div>
      </form>
      <?php else: ?>
      <div style="margin-top:20px">
        <a href="https://wa.me/<?= SITE_WHATSAPP ?>?text=Bonjour, je cherche : <?= urlencode($p['nom']) ?>" class="btn-primary btn-full">
          <i class="fab fa-whatsapp"></i> Me prévenir quand disponible
        </a>
      </div>
      <?php endif; ?>

      <div class="produit-garanties">
        <div class="garanti-item"><i class="fas fa-shield-alt"></i> Qualité certifiée</div>
        <div class="garanti-item"><i class="fas fa-undo"></i> Garantie 48h</div>
        <div class="garanti-item"><i class="fas fa-shipping-fast"></i> Livraison rapide</div>
      </div>
    </div>
  </div>

  <!-- PRODUITS SIMILAIRES -->
  <?php if ($similaires): ?>
  <div class="section">
    <div class="section-header">
      <h2 class="section-title">Produits similaires</h2>
      <a href="<?= SITE_URL ?>/catalogue.php?marque=<?= urlencode($p['marque']) ?>" class="see-all">Voir tout →</a>
    </div>
    <div class="products-grid">
      <?php foreach ($similaires as $s):
        $s_rupture = $s['stock'] < 1;
        $s_img = $s['image'] ? (strpos($s['image'],'http')===0 ? $s['image'] : UPLOAD_URL.$s['image']) : '';
      ?>
      <div class="product-card">
        <a href="<?= SITE_URL ?>/produit.php?slug=<?= urlencode($s['slug']) ?>">
          <div class="product-img">
            <?= $s_img ? '<img src="'.e($s_img).'" alt="'.e($s['nom']).'" loading="lazy">' : '<span class="img-ph">📱</span>' ?>
            <?= $s_rupture ? '<span class="badge-rupture">Rupture</span>' : '' ?>
            <?= ($s['deal']&&!$s_rupture) ? '<span class="badge-deal">🔥</span>' : '' ?>
          </div>
          <div class="product-info">
            <div class="product-name"><?= e($s['nom']) ?></div>
            <div class="product-price"><?= prix((int)$s['prix']) ?></div>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<script>
function changeQty(d) {
  const i = document.getElementById('qty');
  i.value = Math.max(1, Math.min(<?= $p['stock'] ?>, parseInt(i.value)+d));
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
