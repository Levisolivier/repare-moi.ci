<?php
require_once __DIR__ . '/includes/fonctions.php';
$pageTitle = 'Boutique — REPARE-MOI CI';
$pageDesc  = 'Trouvez rapidement vos pièces détachées smartphones. Samsung, iPhone, Huawei, Xiaomi. Livraison Abidjan 24h.';
require_once __DIR__ . '/includes/header.php';
?>
<style>
/* ══ BOUTIQUE — CSS autonome ══ */
:root{--primary:#F07818;--primary-dark:#C85F08;--primary-light:#FFF3E8;--dark-header:#111827;--border:#E5E7EB;--text-dark:#1A1A2E;--text-muted:#6B7280;--success:#10B981;--rupture:#EF4444;--card-bg:#fff;--body-bg:#F4F6F9}

/* ── Hero ── */
.boutique-hero{background:linear-gradient(135deg,#111827 0%,#1e3a8a 55%,#C85F08 100%);padding:40px 0}
.boutique-hero-inner{display:flex;align-items:center;gap:32px;flex-wrap:wrap}
.boutique-hero-text{flex:1;min-width:280px}
.boutique-hero-text h1{font-size:32px;font-weight:800;color:#fff;margin-bottom:10px;line-height:1.2}
.boutique-hero-text h1 span{color:var(--primary)}
.boutique-hero-text p{font-size:14px;color:rgba(255,255,255,.75);margin-bottom:20px}
.boutique-search-big{display:flex;border:2px solid var(--primary);border-radius:8px;overflow:hidden;background:rgba(255,255,255,.1);max-width:600px;margin-bottom:18px}
.boutique-search-big select{background:rgba(255,255,255,.2);color:#fff;border:none;border-right:1px solid rgba(255,255,255,.2);padding:0 14px;font-size:13px;cursor:pointer;outline:none;min-width:130px}
.boutique-search-big select option{background:#1f2937;color:#fff}
.boutique-search-big input{flex:1;background:transparent;border:none;color:#fff;padding:14px 16px;font-size:14px;outline:none}
.boutique-search-big input::placeholder{color:rgba(255,255,255,.5)}
.boutique-search-big button{background:var(--primary);border:none;color:#fff;padding:0 22px;font-size:15px;font-weight:700;display:flex;align-items:center;gap:8px;cursor:pointer;transition:.2s;white-space:nowrap}
.boutique-search-big button:hover{background:var(--primary-dark)}
.boutique-hero-stats{display:flex;gap:12px;flex-wrap:wrap}
.bhs{background:rgba(255,255,255,.12);border-radius:8px;padding:10px 18px;text-align:center;backdrop-filter:blur(4px)}
.bhs-n{font-size:22px;font-weight:800;color:var(--primary);display:block}
.bhs-l{font-size:11px;color:rgba(255,255,255,.65)}

/* ── Brand nav ── */
.brand-nav{background:#fff;border-bottom:2px solid var(--border);padding:12px 0}
.brand-nav-label{font-size:11px;font-weight:700;color:#9ca3af;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px}
.brand-nav-inner{display:flex;gap:8px;overflow-x:auto;scrollbar-width:none;padding-bottom:4px}
.brand-nav-inner::-webkit-scrollbar{display:none}
.brand-btn{flex-shrink:0;display:flex;flex-direction:column;align-items:center;gap:4px;padding:10px 16px;border:1px solid var(--border);border-radius:10px;background:#fff;font-size:11px;font-weight:700;color:var(--text-dark);transition:.2s;cursor:pointer;min-width:72px;text-align:center}
.brand-btn:hover{border-color:var(--primary);color:var(--primary);background:var(--primary-light);transform:translateY(-2px);box-shadow:0 4px 12px rgba(240,120,24,.15)}
.brand-btn .bb-icon{font-size:22px;line-height:1}
.brand-btn .bb-count{font-size:9px;color:var(--text-muted);font-weight:400}

/* ── Cat chips ── */
.cat-chips-wrap{background:#fff;border-bottom:1px solid var(--border);padding:10px 0}
.cat-chips{display:flex;gap:8px;overflow-x:auto;scrollbar-width:none;padding-bottom:2px}
.cat-chips::-webkit-scrollbar{display:none}
.cat-chip{flex-shrink:0;display:flex;align-items:center;gap:6px;padding:8px 18px;border:1px solid var(--border);border-radius:20px;font-size:12px;font-weight:600;background:#fff;color:var(--text-dark);transition:.2s;cursor:pointer;white-space:nowrap;text-decoration:none}
.cat-chip:hover,.cat-chip.active{background:var(--primary);color:#fff;border-color:var(--primary)}
.cat-chip i{font-size:11px}

/* ── Product cards ── */
.product-card{background:var(--card-bg);border:1px solid var(--border);border-radius:8px;overflow:hidden;transition:.25s;display:flex;flex-direction:column}
.product-card:hover{border-color:var(--primary);box-shadow:0 6px 24px rgba(240,120,24,.18);transform:translateY(-3px)}
.product-img{background:#f9fafb;height:155px;display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative;flex-shrink:0}
.product-img img{width:100%;height:100%;object-fit:cover;transition:.3s}
.product-img .img-ph{font-size:50px;opacity:.2;color:#9ca3af}
.badge-rupture{position:absolute;top:8px;left:8px;background:#ef4444;color:#fff;font-size:9px;font-weight:700;padding:3px 8px;border-radius:4px;text-transform:uppercase}
.badge-deal{position:absolute;top:8px;right:8px;background:var(--primary);color:#fff;font-size:9px;font-weight:700;padding:3px 8px;border-radius:4px}
.badge-hot{position:absolute;top:8px;right:8px;background:#10b981;color:#fff;font-size:9px;font-weight:700;padding:3px 8px;border-radius:4px}
.product-info{padding:10px;flex:1;display:flex;flex-direction:column}
.product-meta-row{display:flex;align-items:center;justify-content:space-between;gap:4px;margin-bottom:4px}
.product-brand{font-size:10px;color:var(--primary);font-weight:700;text-transform:uppercase;letter-spacing:.5px}
.product-cat-badge{font-size:9px;background:var(--primary-light);color:var(--primary);padding:2px 6px;border-radius:3px;font-weight:600;white-space:nowrap}
.product-name{font-size:12.5px;font-weight:600;color:var(--text-dark);line-height:1.35;min-height:34px;margin-bottom:6px;flex:1;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical}
.product-price{font-size:16px;font-weight:800;color:var(--primary);margin-bottom:5px}
.product-stock{font-size:10px;color:var(--success);display:flex;align-items:center;gap:4px;margin-bottom:8px}
.product-stock.out{color:var(--rupture)}
.btn-add{width:100%;background:var(--primary);color:#fff;border:none;padding:9px;border-radius:5px;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;gap:6px;transition:.2s;cursor:pointer}
.btn-add:hover:not(:disabled){background:var(--primary-dark)}
.btn-add:disabled{background:#d1d5db;cursor:not-allowed;color:#9ca3af}
.btn-wa{background:#25D366;color:#fff;border:none;border-radius:5px;display:inline-flex;align-items:center;justify-content:center;gap:6px;font-weight:700;transition:.2s;cursor:pointer;text-decoration:none}
.btn-wa:hover{background:#1ebe5d}
.btn-outline{border:2px solid currentColor;padding:10px 22px;border-radius:6px;font-weight:600;font-size:14px;transition:.2s;display:inline-flex;align-items:center;gap:8px;background:none;cursor:pointer;text-decoration:none}
.btn-outline:hover{background:rgba(255,255,255,.15)}

/* ── Section headers ── */
.ps-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;padding-bottom:10px;border-bottom:2px solid var(--border)}
.ps-title{font-size:17px;font-weight:700;color:var(--text-dark);display:flex;align-items:center;gap:8px}
.ps-title i{color:var(--primary)}
.ps-see-all{font-size:12px;font-weight:600;color:var(--primary);display:flex;align-items:center;gap:4px;text-decoration:none}
.ps-see-all:hover{text-decoration:underline}

/* ── Ad ── */
.ad-partner{display:flex;align-items:center;gap:14px;background:var(--card-bg);border:1px solid var(--border);border-radius:8px;padding:14px 18px;transition:.2s;text-decoration:none}
.ad-partner:hover{border-color:var(--primary);box-shadow:0 4px 16px rgba(240,120,24,.12)}
.ad-partner-icon{width:46px;height:46px;background:var(--primary-light);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0}
.ad-partner-text strong{display:block;font-size:13px;font-weight:700;color:var(--text-dark)}
.ad-partner-text span{font-size:11.5px;color:var(--text-muted)}
.ad-partner-cta{margin-left:auto;background:var(--primary);color:#fff;padding:7px 16px;border-radius:6px;font-size:12px;font-weight:700;white-space:nowrap;flex-shrink:0}
.ad-placeholder{display:flex;align-items:center;justify-content:center;gap:10px;background:#f3f4f6;border:1px dashed #d1d5db;border-radius:6px;color:#9ca3af;font-size:12px;padding:14px 20px;width:100%;min-height:70px}

/* ── Grids ── */
.prod-grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}

/* ── Responsive ── */
@media(max-width:1024px){.prod-grid-4{grid-template-columns:repeat(3,1fr)}}
@media(max-width:768px){
  .boutique-hero{padding:24px 0}
  .boutique-hero-text h1{font-size:22px}
  .boutique-search-big{max-width:100%}
  .boutique-search-big select{min-width:100px;font-size:12px}
  .prod-grid-4{grid-template-columns:repeat(2,1fr);gap:10px}
  .bhs{padding:8px 14px}
  .bhs-n{font-size:18px}
  .cat-grid-5{grid-template-columns:repeat(3,1fr)!important}
}
@media(max-width:480px){
  .boutique-search-big select{display:none}
  .prod-grid-4{grid-template-columns:repeat(2,1fr);gap:8px}
  .product-img{height:120px}
  .cat-grid-5{grid-template-columns:repeat(2,1fr)!important}
  .boutique-hero-stats{gap:8px}
  .bhs{padding:7px 10px}
}
@keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
</style>

<?php
$pdo = getPDO();
$nbProduits = (int)$pdo->query('SELECT COUNT(*) FROM rm_produits WHERE actif=1')->fetchColumn();
$nbMarques  = (int)$pdo->query('SELECT COUNT(DISTINCT marque) FROM rm_produits WHERE actif=1')->fetchColumn();

$marques_list = $pdo->query("SELECT marque, COUNT(*) nb FROM rm_produits WHERE actif=1 GROUP BY marque ORDER BY nb DESC")->fetchAll();
$cats_data    = $pdo->query("SELECT categorie, COUNT(*) nb FROM rm_produits WHERE actif=1 GROUP BY categorie ORDER BY nb DESC LIMIT 12")->fetchAll();

$populaires = $pdo->query("SELECT * FROM rm_produits WHERE actif=1 AND stock>0 ORDER BY stock DESC, deal DESC LIMIT 8")->fetchAll();
$deals      = $pdo->query("SELECT * FROM rm_produits WHERE actif=1 AND deal=1 AND stock>0 ORDER BY prix ASC LIMIT 8")->fetchAll();
$nouveautes = $pdo->query("SELECT * FROM rm_produits WHERE actif=1 AND stock>0 ORDER BY id DESC LIMIT 8")->fetchAll();

$marques_icons = ['Samsung'=>['📱','#1428A0'],'iPhone'=>['🍎','#555'],'Huawei'=>['🌸','#CF0A2C'],'Xiaomi'=>['⚡','#FF6900'],'Motorola'=>['Ⓜ️','#5c6bc0'],'LG'=>['🔵','#a50034'],'Nokia'=>['🟦','#005AFF'],'Google Pixel'=>['🎯','#4285F4'],'Oppo'=>['🟢','#1D8348'],'Tecno'=>['📲','#0033CC'],'Infinix'=>['♾️','#6C3483'],'Itel'=>['💡','#E67E22'],'OnePlus'=>['🔴','#F5010C'],'Vivo'=>['🎵','#415fff'],'TCL'=>['🔷','#003087'],'Blackview'=>['⬛','#333']];
$cats_icons = ['Écran'=>['fa-tv','#2563eb'],'Batterie'=>['fa-battery-three-quarters','#059669'],'Coque arrière'=>['fa-mobile-alt','#9333ea'],'Vitre'=>['fa-mobile-screen-button','#0284c7'],'Caméra'=>['fa-camera','#ea580c'],'Haut-parleur'=>['fa-volume-high','#e11d48'],'Connecteur'=>['fa-plug','#d97706'],'Nappe'=>['fa-bezier-curve','#7c3aed'],'Chassis'=>['fa-layer-group','#475569'],'Autre'=>['fa-wrench','#6b7280']];

function card_boutique($p, $pdo) {
    $rup = $p['stock']<1;
    if(!empty($p['image'])) {
        $img = strpos($p['image'],'http')===0 ? $p['image'] : UPLOAD_URL.$p['image'];
    } else {
        $cat_img_map = ['Écran'=>'ecran','Batterie'=>'batterie','Coque arrière'=>'coque','Vitre'=>'vitre','Caméra'=>'camera','Haut-parleur'=>'hautparleur','Connecteur'=>'connecteur','Nappe'=>'nappe','Chassis'=>'chassis'];
        $cat_slug = $cat_img_map[$p['categorie']??''] ?? 'default';
        $img = SITE_URL.'/assets/img/categories/'.$cat_slug.'.png';
    }
    ob_start(); ?>
    <div class="product-card">
      <a href="<?=SITE_URL?>/produit.php?slug=<?=urlencode($p['slug'])?>">
        <div class="product-img">
          <?=$img?'<img src="'.e($img).'" alt="'.e($p['nom']).'" loading="lazy">'
                 :'<span class="img-ph"><i class="fas fa-mobile-alt"></i></span>'?>
          <?=$rup?'<span class="badge-rupture">Rupture</span>':''?>
          <?=(!$rup&&$p['deal'])?'<span class="badge-deal">🔥 Deal</span>':''?>
          <?=(!$rup&&$p['stock']>20)?'<span class="badge-hot" style="background:#10b981">✓ Dispo</span>':''?>
        </div>
        <div class="product-info">
          <div class="product-meta-row">
            <span class="product-brand"><?=e($p['marque'])?></span>
            <span class="product-cat-badge"><?=e($p['categorie'])?></span>
          </div>
          <div class="product-name"><?=e($p['nom'])?></div>
          <div class="product-price"><?=prix((int)$p['prix'])?></div>
          <div class="product-stock <?=$rup?'out':''?>">
            <?=$rup?'<i class="fas fa-times-circle"></i>':'<i class="fas fa-check-circle"></i>'?>
            <?=$rup?'Rupture':'En stock ('.$p['stock'].')' ?>
          </div>
        </div>
      </a>
      <div style="padding:0 10px 10px;display:flex;gap:6px">
        <form method="post" action="<?=SITE_URL?>/panier.php" style="flex:1">
          <input type="hidden" name="action"     value="ajouter">
          <input type="hidden" name="produit_id" value="<?=(int)$p['id']?>">
          <input type="hidden" name="csrf"       value="<?=csrf_token()?>">
          <input type="hidden" name="redirect"   value="/boutique.php">
          <button type="submit" class="btn-add" <?=$rup?'disabled':''?>>
            <?=$rup?'<i class="fas fa-ban"></i> Indisponible':'<i class="fas fa-cart-plus"></i> Panier'?>
          </button>
        </form>
        <a href="https://wa.me/2250545362890?text=<?=urlencode('Bonjour, je veux commander : '.$p['nom'].' — '.prix((int)$p['prix']))?>"
           target="_blank" class="btn-wa" style="padding:9px 10px;border-radius:5px;flex-shrink:0" title="WhatsApp">
          <i class="fab fa-whatsapp"></i>
        </a>
      </div>
    </div>
    <?php return ob_get_clean();
}
?>

<!-- ══ HERO BOUTIQUE ══ -->
<div class="boutique-hero">
  <div class="container">
    <div class="boutique-hero-inner">
      <div class="boutique-hero-text">
        <h1>Votre <span>boutique</span> de pièces<br>smartphones en CI</h1>
        <p>Trouvez rapidement la pièce qu'il vous faut — Samsung, iPhone, Huawei, Xiaomi et plus.</p>
        <!-- Barre de recherche intelligente -->
        <form class="boutique-search-big" method="get" action="catalogue.php">
          <select name="marque">
            <option value="">Toutes marques</option>
            <?php foreach($marques_list as $m): ?>
            <option value="<?=e($m['marque'])?>"><?=e($m['marque'])?></option>
            <?php endforeach; ?>
          </select>
          <input type="text" name="q" placeholder="Ex: écran iPhone 11, batterie Samsung A12..." autocomplete="off">
          <button type="submit"><i class="fas fa-search"></i> Rechercher</button>
        </form>
        <div class="boutique-hero-stats">
          <div class="bhs"><span class="bhs-n"><?=$nbProduits?>+</span><span class="bhs-l">Références</span></div>
          <div class="bhs"><span class="bhs-n"><?=$nbMarques?></span><span class="bhs-l">Marques</span></div>
          <div class="bhs"><span class="bhs-n">24h</span><span class="bhs-l">Livraison</span></div>
          <div class="bhs"><span class="bhs-n">3 mois</span><span class="bhs-l">Garantie</span></div>
        </div>
      </div>
      <div style="text-align:center;flex-shrink:0;display:none" id="heroVisual">
        <div style="font-size:90px;animation:float 3s ease-in-out infinite">🛒</div>
        <div style="color:rgba(255,255,255,.6);font-size:13px;margin-top:12px">Commandez en toute confiance</div>
      </div>
    </div>
  </div>
</div>

<!-- ══ NAVIGATION PAR MARQUE ══ -->
<div class="brand-nav">
  <div class="container">
    <div style="font-size:11px;font-weight:700;color:#9ca3af;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px">Rechercher par marque :</div>
    <div class="brand-nav-inner">
      <?php foreach($marques_list as $m):
        [$ico,$col] = $marques_icons[$m['marque']] ?? ['📱','#555']; ?>
      <a href="catalogue.php?marque=<?=urlencode($m['marque'])?>" class="brand-btn">
        <span class="bb-icon"><?=$ico?></span>
        <span><?=e($m['marque'])?></span>
        <span class="bb-count"><?=$m['nb']?> pièces</span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- ══ CATÉGORIES VISUELLES ══ -->
<div class="cat-chips-wrap">
  <div class="container">
    <div class="cat-chips">
      <a href="catalogue.php" class="cat-chip active">
        <i class="fas fa-th-large"></i> Tout voir
      </a>
      <?php foreach($cats_data as $cat):
        [$ico,$col] = $cats_icons[$cat['categorie']] ?? ['fa-wrench','#666']; ?>
      <a href="catalogue.php?categorie=<?=urlencode($cat['categorie'])?>" class="cat-chip">
        <i class="fas <?=$ico?>" style="color:<?=$col?>"></i>
        <?=e($cat['categorie'])?>
        <span style="font-size:10px;opacity:.6">(<?=$cat['nb']?>)</span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<div class="container" style="padding-top:20px;padding-bottom:40px">

  <!-- ══ DEALS ══ -->
  <?php if($deals): ?>
  <div style="margin-bottom:32px">
    <div style="background:linear-gradient(135deg,var(--dark-header),#1e3a8a);border-radius:10px;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px">
      <div>
        <span style="background:var(--primary);color:#fff;font-size:11px;font-weight:700;padding:3px 12px;border-radius:20px;text-transform:uppercase">🔥 Deals du jour</span>
        <h2 style="color:#fff;font-size:18px;font-weight:800;margin-top:8px">Meilleures offres du moment</h2>
        <p style="color:rgba(255,255,255,.65);font-size:13px">Stock limité — profitez-en maintenant !</p>
      </div>
      <a href="catalogue.php?tri=deal" class="btn-primary"><i class="fas fa-fire"></i> Tous les deals</a>
    </div>
    <div class="prod-grid-4">
      <?php foreach(array_slice($deals,0,4) as $p) echo card_boutique($p,$pdo); ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- ── Partenaire KDO.ci ── -->
  <a href="https://www.kdo.ci" target="_blank" rel="noopener" class="ad-partner" style="display:flex;margin-bottom:28px">
    <div class="ad-partner-icon">🎁</div>
    <div class="ad-partner-text">
      <strong>AnnoncesKDO.ci — Marketplace Côte d'Ivoire</strong>
      <span>Achetez &amp; vendez partout en CI · 93 villes couvertes · Dépôt 100% gratuit</span>
    </div>
    <span class="ad-partner-cta">Découvrir →</span>
  </a>

  <!-- ══ POPULAIRES ══ -->
  <?php if($populaires): ?>
  <div style="margin-bottom:32px">
    <div class="ps-header">
      <h2 class="ps-title"><i class="fas fa-star"></i> Produits populaires</h2>
      <a href="catalogue.php?tri=stock_desc" class="ps-see-all">Voir tout <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="prod-grid-4">
      <?php foreach(array_slice($populaires,0,8) as $p) echo card_boutique($p,$pdo); ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- ══ NOUVEAUTÉS ══ -->
  <?php if($nouveautes): ?>
  <div style="margin-bottom:32px">
    <div class="ps-header">
      <h2 class="ps-title"><i class="fas fa-bolt"></i> Nouveautés</h2>
      <a href="catalogue.php?tri=stock_desc" class="ps-see-all">Voir tout <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="prod-grid-4">
      <?php foreach(array_slice($nouveautes,0,8) as $p) echo card_boutique($p,$pdo); ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- ══ CATÉGORIES VISUELLES ══ -->
  <div style="margin-bottom:32px">
    <div class="ps-header">
      <h2 class="ps-title"><i class="fas fa-th-large"></i> Parcourir par catégorie</h2>
    </div>
    <div class="cat-grid-5" style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px">
      <?php foreach($cats_data as $cat):
        [$ico,$col] = $cats_icons[$cat['categorie']] ?? ['fa-wrench','#666']; ?>
      <a href="catalogue.php?categorie=<?=urlencode($cat['categorie'])?>"
         style="background:#fff;border:1px solid var(--border);border-radius:10px;padding:18px 10px;text-align:center;transition:.2s;display:flex;flex-direction:column;align-items:center;gap:8px"
         onmouseover="this.style.borderColor='var(--primary)';this.style.transform='translateY(-3px)';this.style.boxShadow='0 6px 20px rgba(240,120,24,.15)'"
         onmouseout="this.style.borderColor='var(--border)';this.style.transform='';this.style.boxShadow=''">
        <span style="width:52px;height:52px;background:<?=$col?>22;border-radius:12px;display:flex;align-items:center;justify-content:center">
          <i class="fas <?=$ico?>" style="font-size:22px;color:<?=$col?>"></i>
        </span>
        <span style="font-size:12px;font-weight:700;color:var(--text-dark)"><?=e($cat['categorie'])?></span>
        <span style="font-size:11px;color:var(--text-muted)"><?=$cat['nb']?> produits</span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ══ CTA WHATSAPP ══ -->
  <div style="background:linear-gradient(135deg,#075E54,#128C7E);border-radius:12px;padding:28px 32px;text-align:center;margin-bottom:32px">
    <div style="font-size:40px;margin-bottom:12px">💬</div>
    <h3 style="color:#fff;font-size:20px;font-weight:800;margin-bottom:8px">Vous ne trouvez pas ce qu'il vous faut ?</h3>
    <p style="color:rgba(255,255,255,.8);font-size:14px;margin-bottom:20px">Contactez-nous directement sur WhatsApp — réponse rapide 7j/7 !</p>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
      <a href="https://wa.me/2250545362890?text=<?=urlencode('Bonjour REPARE-MOI CI, je recherche une pièce détachée. Pouvez-vous m\'aider ?')?>"
         target="_blank" class="btn-wa" style="font-size:15px;padding:13px 28px">
        <i class="fab fa-whatsapp"></i> Commander sur WhatsApp
      </a>
      <a href="<?=SITE_URL?>/catalogue.php" class="btn-outline" style="border-color:#fff;color:#fff;font-size:15px;padding:13px 28px">
        <i class="fas fa-th-large"></i> Voir tout le catalogue
      </a>
    </div>
  </div>

  <!-- ══ ESPACE PUB BAS ══ -->
  <div class="ad-slot">
    <span class="ad-label">Publicité</span>
    <div class="ad-placeholder"><i class="fas fa-ad"></i><span>Espace publicitaire — 970×90</span></div>
  </div>

</div>

<style>
@keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
.prod-grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
@media(max-width:1024px){.prod-grid-4{grid-template-columns:repeat(3,1fr)}}
@media(max-width:768px){
  .prod-grid-4{grid-template-columns:repeat(2,1fr);gap:10px}
  #heroVisual{display:none!important}
  div[style*="grid-template-columns:repeat(5,1fr)"]{grid-template-columns:repeat(3,1fr)!important}
}
@media(max-width:480px){
  .prod-grid-4{grid-template-columns:repeat(2,1fr);gap:8px}
  div[style*="grid-template-columns:repeat(3,1fr)"]{grid-template-columns:repeat(2,1fr)!important}
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
