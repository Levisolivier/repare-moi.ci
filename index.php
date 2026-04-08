<?php
$pageTitle = 'REPARE-MOI CI — Pièces détachées smartphones Côte d\'Ivoire';
$pageDesc  = 'Achetez vos pièces détachées smartphones pas chers en Côte d\'Ivoire. Samsung, iPhone, Huawei, Xiaomi. Livraison Abidjan 24h. Garantie 48h.';

ob_start(); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<style>
/* ══════════════════════════════════════════════
   REPARE-MOI CI — Homepage v4
   Charte : #FF6A00 · #111111 · #FFFFFF
   ══════════════════════════════════════════════ */

/* Ticker */
.ticker-wrap{background:#FF6A00;overflow:hidden;padding:7px 0;border-bottom:2px solid #E55A00}
.ticker-track{display:flex;animation:ticker 40s linear infinite;white-space:nowrap}
.ticker-track:hover{animation-play-state:paused}
.ti{padding:0 36px;font-size:11.5px;font-weight:600;color:#fff;display:inline-flex;align-items:center;gap:7px}
.ti i{opacity:.8;font-size:10px}
@keyframes ticker{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}

/* Hero */
.hero-v4{background:linear-gradient(135deg,#0A0A0A 0%,#1C1C1C 55%,#2A1400 100%);padding:40px 0;position:relative;overflow:hidden}
.hero-v4::before{content:'';position:absolute;right:-60px;top:-80px;width:320px;height:320px;border-radius:50%;background:#FF6A00;opacity:.07}
.hero-v4::after{content:'';position:absolute;right:80px;bottom:-100px;width:200px;height:200px;border-radius:50%;background:#FF6A00;opacity:.04}
.hero-inner-v4{display:flex;align-items:center;gap:32px;position:relative;z-index:1}
.hero-content-v4{flex:1}
.hero-eyebrow{display:inline-flex;align-items:center;gap:7px;background:rgba(255,106,0,.15);border:1px solid rgba(255,106,0,.3);padding:5px 12px;border-radius:20px;margin-bottom:14px}
.hero-eyebrow .dot{width:7px;height:7px;border-radius:50%;background:#FF6A00;animation:hpulse 1.5s infinite}
@keyframes hpulse{0%,100%{opacity:1}50%{opacity:.3}}
.hero-eyebrow span{font-size:11px;font-weight:700;color:#FF6A00;letter-spacing:.5px}
.hero-v4 h1{font-size:30px;font-weight:900;color:#fff;line-height:1.2;margin-bottom:10px}
.hero-v4 h1 span{color:#FF6A00}
.hero-v4 .hero-sub{font-size:12px;color:#777;margin-bottom:18px;line-height:1.7}
.hero-v4 .hero-sub strong{color:#aaa}
.hero-ctas-v4{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:14px}
.btn-hero-primary{background:#FF6A00;color:#fff;border:none;padding:12px 22px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:7px;transition:.2s;font-family:inherit}
.btn-hero-primary:hover{background:#E55A00;transform:translateY(-1px);box-shadow:0 6px 20px rgba(255,106,0,.3)}
.btn-hero-secondary{background:transparent;color:#fff;border:1.5px solid rgba(255,255,255,.25);padding:11px 20px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;transition:.2s}
.btn-hero-secondary:hover{border-color:rgba(255,255,255,.5);background:rgba(255,255,255,.05)}
.hero-trust{display:flex;gap:14px;flex-wrap:wrap}
.htrust{display:flex;align-items:center;gap:5px;font-size:10px;color:#666}
.htrust .dot{width:5px;height:5px;border-radius:50%;background:#3DDC97}
.hero-cards{display:flex;flex-direction:column;gap:8px;flex-shrink:0}
.hcard{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.09);border-radius:10px;padding:12px 16px;min-width:150px;backdrop-filter:blur(4px)}
.hcard-label{font-size:8.5px;color:#555;text-transform:uppercase;letter-spacing:.8px;margin-bottom:3px}
.hcard-val{font-size:18px;font-weight:900;color:#fff;margin-bottom:1px}
.hcard-sub{font-size:9px;color:#666}
.promo-bar{background:linear-gradient(90deg,rgba(255,106,0,.92),rgba(229,90,0,.92));padding:8px 20px;display:flex;align-items:center;justify-content:space-between}
.promo-bar span{font-size:11px;font-weight:700;color:#fff}
.promo-bar a{font-size:11px;color:rgba(255,255,255,.8);text-decoration:underline;cursor:pointer}

/* Avantages */
.avantages-strip{background:#fff;border-bottom:1px solid #EBEBEB}
.avantages-strip .container{display:grid;grid-template-columns:repeat(4,1fr)}
.av-bloc{padding:16px;border-right:1px solid #EBEBEB;display:flex;align-items:center;gap:12px}
.av-bloc:last-child{border-right:none}
.av-ico{width:38px;height:38px;border-radius:10px;background:#FFF3EA;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#FF6A00;font-size:16px}
.av-title{font-size:12px;font-weight:700;color:#111;margin-bottom:2px}
.av-desc{font-size:10px;color:#6B7280;line-height:1.4}

/* Section layout */
.home-sec{padding:28px 0}
.home-sec.alt{background:#F5F5F5}
.home-sec.dark{background:#111;padding:32px 0}
.sec-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:18px}
.sec-tag{display:inline-block;font-size:9px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#FF6A00;background:#FFF3EA;padding:3px 9px;border-radius:20px;margin-bottom:6px}
.sec-title{font-size:16px;font-weight:900;color:#111;position:relative;padding-left:12px}
.sec-title::before{content:'';position:absolute;left:0;top:50%;transform:translateY(-50%);width:3.5px;height:16px;background:#FF6A00;border-radius:2px}
.sec-title.white{color:#fff}
.sec-title.white::before{background:#FF6A00}
.sec-link{font-size:11px;font-weight:700;color:#FF6A00;white-space:nowrap}
.sec-link:hover{text-decoration:underline}

/* Brands grid */
.brands-grid-v4{display:grid;grid-template-columns:repeat(10,1fr);gap:8px}
.brand-card-v4{border:1.5px solid #EBEBEB;border-radius:10px;padding:10px 6px;text-align:center;cursor:pointer;background:#fff;transition:.2s}
.brand-card-v4:hover{border-color:#FF6A00;transform:translateY(-2px);box-shadow:0 4px 14px rgba(255,106,0,.12)}
.bc-logo{height:28px;display:flex;align-items:center;justify-content:center;margin-bottom:5px;font-size:11px;font-weight:900}
.bc-name{font-size:9px;font-weight:700;color:#555}
.bc-count{font-size:8px;color:#bbb;margin-top:1px}

/* Categories */
.cats-grid-v4{display:grid;grid-template-columns:repeat(6,1fr);gap:10px}
.cat-v4{border-radius:12px;overflow:hidden;cursor:pointer;transition:.2s}
.cat-v4:hover{transform:translateY(-3px);box-shadow:0 6px 20px rgba(0,0,0,.1)}
.cat-top{height:88px;display:flex;align-items:center;justify-content:center;position:relative}
.cat-icon-circle{width:46px;height:46px;border-radius:50%;background:rgba(255,255,255,.9);display:flex;align-items:center;justify-content:center;font-size:20px;position:relative;z-index:1}
.cat-bottom{padding:9px 10px 11px;border:1px solid;border-top:none;border-radius:0 0 12px 12px}
.cat-name{font-size:10.5px;font-weight:800;margin-bottom:1px}
.cat-sub{font-size:9px;opacity:.75}

/* Deals */
.deals-header-bar{border-radius:12px;padding:14px 18px;display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;background:linear-gradient(135deg,#111,#1E1E1E)}
.deals-header-bar h3{font-size:15px;font-weight:900;color:#fff;margin-bottom:3px}
.deals-header-bar p{font-size:10px;color:#777}
.countdown-wrap{display:flex;align-items:center;gap:7px}
.cd-box{background:#FF6A00;color:#fff;border-radius:6px;padding:5px 8px;text-align:center;min-width:34px}
.cd-num{font-size:14px;font-weight:900;line-height:1;display:block}
.cd-unit{font-size:8px;font-weight:600}
.cd-sep{color:#FF6A00;font-weight:900;font-size:16px}

/* Product cards */
.prods-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
.prod-v4{background:#fff;border:1px solid #EBEBEB;border-radius:12px;overflow:hidden;transition:.25s;display:flex;flex-direction:column}
.prod-v4:hover{border-color:#FF6A00;box-shadow:0 6px 24px rgba(255,106,0,.14);transform:translateY(-3px)}
.prod-img-v4{height:110px;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden}
.prod-img-v4 img{width:100%;height:100%;object-fit:cover}
.prod-img-v4 .img-ph{font-size:44px;opacity:.18;color:#9CA3AF}
.prod-img-v4 img[src=""]{display:none}
.badge-v4{position:absolute;font-size:9px;font-weight:700;padding:3px 8px;border-radius:4px;line-height:1.3}
.badge-deal{top:8px;left:8px;background:#FF6A00;color:#fff}
.badge-hot{top:8px;right:8px;background:#EF4444;color:#fff}
.badge-new{top:8px;right:8px;background:#10B981;color:#fff}
.prod-body-v4{padding:10px;flex:1;display:flex;flex-direction:column}
.p-brand{font-size:9px;font-weight:700;color:#FF6A00;text-transform:uppercase;letter-spacing:.6px;margin-bottom:2px}
.p-name{font-size:11.5px;font-weight:700;color:#111;line-height:1.35;min-height:30px;margin-bottom:5px;flex:1;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical}
.p-stars{display:flex;align-items:center;gap:3px;margin-bottom:4px}
.p-stars .s{color:#F59E0B;font-size:9px}
.p-stars .sc{font-size:8px;color:#9CA3AF}
.p-price{font-size:15px;font-weight:900;color:#FF6A00;margin-bottom:4px}
.p-price .old{font-size:9px;text-decoration:line-through;color:#bbb;font-weight:400;margin-left:5px}
.p-stock{font-size:9px;color:#10B981;display:flex;align-items:center;gap:4px;margin-bottom:8px}
.p-stock.low{color:#F59E0B}
.p-stock.out{color:#EF4444}
.p-stock .dot{width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0}
.btn-panier{width:100%;background:#FF6A00;color:#fff;border:none;padding:8px;border-radius:7px;font-size:10px;font-weight:700;cursor:pointer;font-family:inherit;transition:.2s;display:flex;align-items:center;justify-content:center;gap:5px}
.btn-panier:hover:not(:disabled){background:#E55A00}
.btn-panier:disabled{background:#D1D5DB;cursor:not-allowed;color:#9CA3AF}
.btn-wa-small{width:100%;background:transparent;color:#25D366;border:1.5px solid #25D366;padding:7px;border-radius:7px;font-size:10px;font-weight:700;cursor:pointer;font-family:inherit;transition:.2s;margin-top:5px;display:flex;align-items:center;justify-content:center;gap:5px}
.btn-wa-small:hover{background:#25D366;color:#fff}

/* Brand tabs */
.brand-tabs-v4{display:flex;gap:0;border-bottom:2px solid #EBEBEB;margin-bottom:16px;overflow-x:auto;scrollbar-width:none}
.brand-tabs-v4::-webkit-scrollbar{display:none}
.btab-v4{padding:9px 16px;font-size:11px;font-weight:700;color:#9CA3AF;border-bottom:2px solid transparent;margin-bottom:-2px;cursor:pointer;white-space:nowrap;transition:.15s}
.btab-v4.active,.btab-v4:hover{color:#FF6A00;border-bottom-color:#FF6A00}

/* Pourquoi nous */
.why-grid-v4{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
.why-card-v4{background:#fff;border:1px solid #EBEBEB;border-radius:12px;padding:18px 14px;text-align:center;transition:.2s}
.why-card-v4:hover{border-color:#FF6A00;box-shadow:0 4px 16px rgba(255,106,0,.1);transform:translateY(-2px)}
.why-ico-v4{width:48px;height:48px;border-radius:14px;background:#FFF3EA;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:22px}
.why-num{font-size:22px;font-weight:900;color:#FF6A00;margin-bottom:3px}
.why-title{font-size:12px;font-weight:800;color:#111;margin-bottom:5px}
.why-desc{font-size:10px;color:#6B7280;line-height:1.55}

/* Reviews */
.reviews-grid-v4{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
.review-v4{background:#fff;border:1px solid #EBEBEB;border-radius:12px;padding:16px}
.rev-head{display:flex;align-items:center;gap:10px;margin-bottom:8px}
.rev-avatar{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0}
.rev-name{font-size:11px;font-weight:700;color:#111}
.rev-info{font-size:9px;color:#9CA3AF;margin-top:1px}
.rev-stars{display:flex;gap:2px;margin-bottom:6px}
.rev-text{font-size:10px;color:#555;line-height:1.6}
.rev-verified{display:inline-flex;align-items:center;gap:4px;font-size:9px;color:#10B981;margin-top:8px;font-weight:600}

/* Blog */
.blog-grid-v4{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
.blog-v4{background:#fff;border:1px solid #EBEBEB;border-radius:12px;overflow:hidden;transition:.2s}
.blog-v4:hover{border-color:#FF6A00;transform:translateY(-2px);box-shadow:0 4px 16px rgba(0,0,0,.08)}
.blog-img-v4{height:90px;display:flex;align-items:center;justify-content:center;position:relative}
.blog-tag-v4{position:absolute;top:8px;left:8px;font-size:9px;font-weight:700;padding:3px 9px;border-radius:4px;background:#FF6A00;color:#fff}
.blog-body-v4{padding:12px}
.blog-title-v4{font-size:11.5px;font-weight:700;color:#111;line-height:1.4;margin-bottom:5px}
.blog-excerpt-v4{font-size:9.5px;color:#6B7280;line-height:1.55;margin-bottom:8px}
.blog-cta-v4{font-size:10px;font-weight:700;color:#FF6A00}

/* WhatsApp FAB */
.wa-fab{position:fixed;bottom:24px;right:24px;width:52px;height:52px;border-radius:50%;background:#25D366;color:#fff;display:flex;align-items:center;justify-content:center;font-size:24px;box-shadow:0 4px 20px rgba(37,211,102,.4);cursor:pointer;z-index:999;transition:.2s;text-decoration:none}
.wa-fab:hover{transform:scale(1.08);box-shadow:0 6px 26px rgba(37,211,102,.5)}

/* RESPONSIVE */
@media(max-width:1024px){
  .brands-grid-v4{grid-template-columns:repeat(5,1fr)}
  .cats-grid-v4{grid-template-columns:repeat(3,1fr)}
  .prods-grid{grid-template-columns:repeat(3,1fr)}
  .why-grid-v4{grid-template-columns:repeat(2,1fr)}
}
@media(max-width:768px){
  .avantages-strip .container{grid-template-columns:1fr 1fr}
  .av-bloc:nth-child(2){border-right:none}
  .av-bloc:nth-child(1),.av-bloc:nth-child(2){border-bottom:1px solid #EBEBEB}
  .hero-v4 h1{font-size:20px}
  .hero-cards{display:none}
  .brands-grid-v4{grid-template-columns:repeat(4,1fr)}
  .cats-grid-v4{grid-template-columns:repeat(2,1fr)}
  .prods-grid{grid-template-columns:repeat(2,1fr);gap:8px}
  .reviews-grid-v4{grid-template-columns:1fr}
  .blog-grid-v4{grid-template-columns:1fr}
  .why-grid-v4{grid-template-columns:1fr 1fr}
}
@media(max-width:480px){
  .prods-grid{grid-template-columns:repeat(2,1fr);gap:6px}
  .cats-grid-v4{grid-template-columns:repeat(2,1fr)}
  .brands-grid-v4{grid-template-columns:repeat(3,1fr)}
  .hero-v4{padding:24px 0}
}
</style>
<?php
$extra_head = ob_get_clean();
require_once __DIR__ . '/includes/header.php';

$pdo = getPDO();

// ── Stats ──
$nbProduits = (int)$pdo->query('SELECT COUNT(*) FROM rm_produits WHERE actif=1')->fetchColumn();
$nbMarques  = (int)$pdo->query('SELECT COUNT(DISTINCT marque) FROM rm_produits WHERE actif=1')->fetchColumn();
$nbCats     = (int)$pdo->query('SELECT COUNT(DISTINCT categorie) FROM rm_produits WHERE actif=1')->fetchColumn();

// ── Données ──
$marques_list  = $pdo->query("SELECT marque, COUNT(*) nb FROM rm_produits WHERE actif=1 GROUP BY marque ORDER BY nb DESC")->fetchAll();
$cats_data     = $pdo->query("SELECT categorie, COUNT(*) nb FROM rm_produits WHERE actif=1 GROUP BY categorie ORDER BY nb DESC LIMIT 12")->fetchAll();
$deals         = $pdo->query("SELECT * FROM rm_produits WHERE actif=1 AND deal=1 AND stock>0 ORDER BY prix ASC LIMIT 8")->fetchAll();
$populaires    = $pdo->query("SELECT * FROM rm_produits WHERE actif=1 AND stock>0 ORDER BY stock DESC LIMIT 8")->fetchAll();
$nouveautes    = $pdo->query("SELECT * FROM rm_produits WHERE actif=1 AND stock>0 ORDER BY id DESC LIMIT 4")->fetchAll();

// Par marque (4 produits chacune)
function prods_marque($pdo, $marque, $limit=4) {
    $st = $pdo->prepare("SELECT * FROM rm_produits WHERE actif=1 AND marque=? AND stock>0 ORDER BY deal DESC, id DESC LIMIT ?");
    $st->execute([$marque, $limit]);
    return $st->fetchAll();
}

// ── Maps ──
$brand_colors = [
    'Samsung'=>'#1428A0','iPhone'=>'#555','Huawei'=>'#CF0A2C','Xiaomi'=>'#F97316',
    'Motorola'=>'#5C6BC0','LG'=>'#A50034','Nokia'=>'#2563EB','Google Pixel'=>'#4285F4',
    'Oppo'=>'#059669','Tecno'=>'#0033CC','Infinix'=>'#7C3AED','Itel'=>'#EA580C',
    'OnePlus'=>'#EF4444','Vivo'=>'#415FFF','TCL'=>'#003087',
];
$brand_initials = [
    'Samsung'=>'S','iPhone'=>'A','Huawei'=>'H','Xiaomi'=>'mi','Motorola'=>'M',
    'LG'=>'LG','Nokia'=>'N','Google Pixel'=>'G','Oppo'=>'O','Tecno'=>'T',
    'Infinix'=>'∞','Itel'=>'i','OnePlus'=>'1+','Vivo'=>'V','TCL'=>'TCL',
];
$cats_config = [
    'Écran'         => ['🖥️','#EFF6FF','#2563EB','#DBEAFE'],
    'Batterie'      => ['🔋','#ECFDF5','#059669','#D1FAE5'],
    'Coque arrière' => ['📱','#FDF4FF','#9333EA','#F3E8FF'],
    'Vitre'         => ['🔲','#F0F9FF','#0284C7','#E0F2FE'],
    'Caméra'        => ['📷','#FFF7ED','#EA580C','#FED7AA'],
    'Haut-parleur'  => ['🔊','#FFF1F2','#E11D48','#FCE7F3'],
    'Connecteur'    => ['🔌','#FFFBEB','#D97706','#FEF3C7'],
    'Nappe'         => ['🔗','#F5F3FF','#7C3AED','#EDE9FE'],
    'Chassis'       => ['🔧','#F8FAFC','#475569','#F1F5F9'],
    'Autre'         => ['⚙️','#F9FAFB','#6B7280','#F3F4F6'],
];

// ── Helper image ──
function prod_img($p) {
    if (!empty($p['image'])) {
        return str_starts_with($p['image'],'http') ? $p['image'] : UPLOAD_URL.$p['image'];
    }
    $cm=['Écran'=>'ecran','Batterie'=>'batterie','Coque arrière'=>'coque','Vitre'=>'vitre',
         'Caméra'=>'camera','Haut-parleur'=>'hautparleur','Connecteur'=>'connecteur',
         'Nappe'=>'nappe','Chassis'=>'chassis'];
    $slug = $cm[$p['categorie'] ?? ''] ?? 'default';
    return SITE_URL.'/assets/img/categories/'.$slug.'.png';
}

// ── Helper card produit ──
function prod_card($p, $badge='') {
    $rup  = $p['stock'] < 1;
    $img  = prod_img($p);
    $slug = urlencode($p['slug']);
    $wa   = 'https://wa.me/'.SITE_WHATSAPP.'?text='.urlencode('Bonjour REPARE-MOI CI, je souhaite commander : '.$p['nom'].' — '.number_format((int)$p['prix'],0,'',' ').' FCFA');
    ?>
    <div class="prod-v4">
      <a href="<?= SITE_URL ?>/produit.php?slug=<?= $slug ?>">
        <div class="prod-img-v4" style="background:<?= $rup?'#F9FAFB':'#FFF8F5' ?>">
          <img src="<?= e($img) ?>" alt="<?= e($p['nom']) ?>" loading="lazy"
               onerror="this.src='<?= SITE_URL ?>/assets/img/categories/default.png'">
          <?php if($p['deal'] && !$rup): ?><span class="badge-v4 badge-deal">🔥 Deal</span><?php endif; ?>
          <?php if($rup): ?><span class="badge-v4 badge-hot" style="left:8px;right:auto">Rupture</span><?php endif; ?>
          <?php if($badge==='new' && !$rup): ?><span class="badge-v4 badge-new">Nouveau</span><?php endif; ?>
        </div>
        <div class="prod-body-v4">
          <div class="p-brand"><?= e($p['marque']) ?></div>
          <div class="p-name"><?= e($p['nom']) ?></div>
          <div class="p-price"><?= prix((int)$p['prix']) ?></div>
          <div class="p-stock <?= $rup?'out':($p['stock']<5?'low':'') ?>">
            <span class="dot"></span>
            <?= $rup ? 'Rupture de stock' : ($p['stock']<5 ? 'Derniers ('.$p['stock'].')' : 'En stock ('.$p['stock'].')') ?>
          </div>
        </div>
      </a>
      <div style="padding:0 10px 10px;display:flex;flex-direction:column;gap:5px">
        <form method="post" action="<?= SITE_URL ?>/panier.php">
          <input type="hidden" name="action"     value="ajouter">
          <input type="hidden" name="produit_id" value="<?= (int)$p['id'] ?>">
          <input type="hidden" name="csrf"       value="<?= csrf_token() ?>">
          <input type="hidden" name="redirect"   value="/">
          <button type="submit" class="btn-panier" <?= $rup?'disabled':'' ?>>
            <i class="fas fa-<?= $rup?'ban':'cart-plus' ?>"></i>
            <?= $rup ? 'Indisponible' : 'Ajouter au panier' ?>
          </button>
        </form>
        <?php if(!$rup): ?>
        <a href="<?= $wa ?>" target="_blank" rel="noopener" class="btn-wa-small">
          <i class="fab fa-whatsapp"></i> Commander sur WhatsApp
        </a>
        <?php endif; ?>
      </div>
    </div>
<?php }
?>

<!-- ══ TICKER ══ -->
<div class="ticker-wrap">
  <div class="ticker-track">
    <?php $tickers=[
      ['fa-bolt','Réparation sur place en 40 min — 1 000 FCFA'],
      ['fa-mobile-alt','+'.number_format($nbProduits,0,'',' ').' références en stock'],
      ['fa-headset','Support WhatsApp 7j/7 · '.SITE_TEL2],
      ['fa-shield-alt','Garantie 48h sur toutes les pièces'],
      ['fa-tag','Promo : -15% sur les batteries cette semaine'],
      ['fa-check-circle','Pièces certifiées — qualité OEM garantie'],
      ['fa-truck','Livraison partout en Côte d\'Ivoire'],
    ];
    foreach(array_merge($tickers,$tickers) as [$ic,$txt]): ?>
    <span class="ti"><i class="fas <?= $ic ?>"></i><?= $txt ?></span>
    <?php endforeach; ?>
  </div>
</div>

<!-- ══ HERO ══ -->
<div class="hero-v4">
  <div class="container">
    <div class="hero-inner-v4">
      <div class="hero-content-v4">
        <div class="hero-eyebrow"><span class="dot"></span><span>+<?= number_format($nbProduits,0,'',' ') ?> références en stock</span></div>
        <h1>Pièces détachées smartphones<br><span>Réparation sur place en 40 min</span></h1>
        <p class="hero-sub">
          <strong>Qualité certifiée</strong> &nbsp;•&nbsp;
          <strong>Garantie 48h</strong> &nbsp;•&nbsp;
          <strong><?= $nbProduits ?>+ références</strong>
        </p>
        <div class="hero-ctas-v4">
          <a href="<?= SITE_URL ?>/catalogue.php?categorie=%C3%89cran" class="btn-hero-primary">
            <i class="fas fa-tv"></i> Voir les écrans
          </a>
          <a href="<?= SITE_URL ?>/boutique.php" class="btn-hero-secondary">
            <i class="fas fa-search"></i> Trouver ma pièce
          </a>
        </div>
        <div class="hero-trust">
          <span class="htrust"><span class="dot"></span> Réparation 40 min Abidjan</span>
          <span class="htrust"><span class="dot"></span> Garantie 48h</span>
          <span class="htrust"><span class="dot"></span> Paiement mobile</span>
          <span class="htrust"><span class="dot"></span> <?= $nbMarques ?> marques</span>
        </div>
      </div>
      <div class="hero-cards">
        <div class="hcard">
          <div class="hcard-label">Stock disponible</div>
          <div class="hcard-val"><?= $nbProduits ?>+</div>
          <div class="hcard-sub">références en stock</div>
        </div>
        <div class="hcard">
          <div class="hcard-label">Réparation sur place</div>
          <div class="hcard-val">24h</div>
          <div class="hcard-sub">réparation immédiate</div>
        </div>
        <div class="hcard">
          <div class="hcard-label">Garantie</div>
          <div class="hcard-val">48h</div>
          <div class="hcard-sub">satisfait ou remboursé</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Promo bar -->
<div class="promo-bar">
  <span>🔥&nbsp; Promo de la semaine : -15% sur toutes les batteries — Code <strong>BATTERIE15</strong></span>
  <a href="<?= SITE_URL ?>/catalogue.php?categorie=Batterie">Voir les offres →</a>
</div>

<!-- ══ AVANTAGES ══ -->
<div class="avantages-strip">
  <div class="container">
    <div class="av-bloc">
      <div class="av-ico"><i class="fas fa-shield-alt"></i></div>
      <div><div class="av-title">Garantie 48h</div><div class="av-desc">Satisfait ou remboursé sur toutes les pièces</div></div>
    </div>
    <div class="av-bloc">
      <div class="av-ico"><i class="fas fa-shipping-fast"></i></div>
      <div><div class="av-title">Réparation 40 min</div><div class="av-desc">Diagnostic gratuit · Réparation immédiate</div></div>
    </div>
    <div class="av-bloc">
      <div class="av-ico"><i class="fas fa-check-circle"></i></div>
      <div><div class="av-title">Pièces certifiées</div><div class="av-desc">Qualité OEM testée à chaque réception</div></div>
    </div>
    <div class="av-bloc">
      <div class="av-ico" style="background:#ECFDF5;color:#25D366"><i class="fab fa-whatsapp"></i></div>
      <div><div class="av-title">Support WhatsApp 7j/7</div><div class="av-desc">Réponse rapide, conseil personnalisé</div></div>
    </div>
  </div>
</div>

<div class="container">

<!-- ══ MARQUES ══ -->
<div class="home-sec">
  <div class="sec-header">
    <div>
      <div class="sec-tag">Recherche rapide</div>
      <div class="sec-title">Toutes nos marques</div>
    </div>
    <a href="<?= SITE_URL ?>/catalogue.php" class="sec-link">Voir tout le catalogue →</a>
  </div>
  <div class="brands-grid-v4">
    <?php foreach($marques_list as $m):
      $col = $brand_colors[$m['marque']] ?? '#FF6A00';
      $ini = $brand_initials[$m['marque']] ?? mb_substr($m['marque'],0,2);
    ?>
    <a href="<?= SITE_URL ?>/catalogue.php?marque=<?= urlencode($m['marque']) ?>" class="brand-card-v4">
      <div class="bc-logo">
        <div style="width:32px;height:32px;border-radius:8px;background:<?= $col ?>;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:900;color:#fff">
          <?= htmlspecialchars($ini) ?>
        </div>
      </div>
      <div class="bc-name"><?= e($m['marque']) ?></div>
      <div class="bc-count"><?= $m['nb'] ?> pièces</div>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<!-- ══ CATÉGORIES ══ -->
<div class="home-sec alt" style="margin:0 -16px;padding:28px 16px">
  <div class="sec-header">
    <div>
      <div class="sec-tag">Navigation rapide</div>
      <div class="sec-title">Catégories principales</div>
    </div>
  </div>
  <div class="cats-grid-v4">
    <?php foreach($cats_data as $cat):
      $k = $cat['categorie'];
      [$ico,$bg,$col,$lb] = $cats_config[$k] ?? ['🔧','#F9FAFB','#6B7280','#F3F4F6'];
    ?>
    <a href="<?= SITE_URL ?>/catalogue.php?categorie=<?= urlencode($k) ?>" class="cat-v4">
      <div class="cat-top" style="background:<?= $bg ?>">
        <div style="position:absolute;inset:0;background:<?= $col ?>;opacity:.07;border-radius:12px 12px 0 0"></div>
        <div class="cat-icon-circle" style="background:<?= $col ?>;background:<?= $col ?>22">
          <span style="font-size:22px"><?= $ico ?></span>
        </div>
      </div>
      <div class="cat-bottom" style="background:<?= $lb ?>;border-color:<?= $bg ?>">
        <div class="cat-name" style="color:<?= $col ?>"><?= e($k) ?></div>
        <div class="cat-sub" style="color:<?= $col ?>"><?= $cat['nb'] ?> article<?= $cat['nb']>1?'s':'' ?></div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<!-- ══ DEALS DU JOUR ══ -->
<?php if($deals): ?>
<div class="home-sec">
  <div class="deals-header-bar">
    <div>
      <div style="display:inline-block;background:#FF6A00;color:#fff;font-size:9px;font-weight:700;padding:2px 9px;border-radius:4px;margin-bottom:6px;letter-spacing:.5px">🔥 DEALS DU JOUR</div>
      <h3 style="font-size:15px;font-weight:900;color:#fff;margin-bottom:3px">Meilleures offres du moment</h3>
      <p style="font-size:10px;color:#666">Stock limité — profitez avant rupture !</p>
    </div>
    <div style="display:flex;align-items:center;gap:8px">
      <div style="font-size:9px;color:#555">Fin dans :</div>
      <div class="countdown-wrap">
        <div class="cd-box"><span class="cd-num" id="cdH">04</span><span class="cd-unit">h</span></div>
        <span class="cd-sep">:</span>
        <div class="cd-box"><span class="cd-num" id="cdM">32</span><span class="cd-unit">m</span></div>
        <span class="cd-sep">:</span>
        <div class="cd-box"><span class="cd-num" id="cdS">18</span><span class="cd-unit">s</span></div>
      </div>
    </div>
  </div>
  <div class="prods-grid">
    <?php foreach(array_slice($deals,0,4) as $p) prod_card($p,'deal'); ?>
  </div>
</div>
<?php endif; ?>

<!-- ══ PARTENAIRE KDO ══ -->
<div style="background:#fff;border:1px solid #EBEBEB;border-radius:12px;padding:14px 18px;display:flex;align-items:center;gap:16px;margin-bottom:0">
  <div style="width:46px;height:46px;background:#FFF3EA;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0">🎁</div>
  <div style="flex:1">
    <div style="font-size:13px;font-weight:700;color:#111">AnnoncesKDO.ci — Marketplace Côte d'Ivoire</div>
    <div style="font-size:11px;color:#6B7280;margin-top:2px">Achetez &amp; vendez partout en CI · 93 villes · Dépôt 100% gratuit</div>
  </div>
  <a href="https://www.kdo.ci" target="_blank" rel="noopener" style="background:#FF6A00;color:#fff;padding:8px 18px;border-radius:8px;font-size:11px;font-weight:700;white-space:nowrap;flex-shrink:0">Découvrir →</a>
</div>

<!-- ══ PRODUITS POPULAIRES PAR MARQUE ══ -->
<div class="home-sec">
  <div class="sec-header">
    <div>
      <div class="sec-tag">Produits populaires</div>
      <div class="sec-title">Par marque</div>
    </div>
    <a href="<?= SITE_URL ?>/catalogue.php" class="sec-link">Tout voir →</a>
  </div>
  <div class="brand-tabs-v4" id="brandTabs">
    <?php
    $tab_marques = ['Samsung','iPhone','Huawei','Xiaomi','Tecno'];
    foreach($tab_marques as $i=>$m): ?>
    <div class="btab-v4 <?= $i===0?'active':'' ?>" onclick="switchBrand('<?= e($m) ?>',this)"><?= e($m) ?></div>
    <?php endforeach; ?>
  </div>
  <?php foreach($tab_marques as $i=>$m):
    $prods_m = prods_marque($pdo, $m, 4);
  ?>
  <div id="tab-<?= $i ?>" class="prods-grid brand-tab-pane" <?= $i>0?'style="display:none"':'' ?>>
    <?php foreach($prods_m as $p) prod_card($p); ?>
    <?php if(empty($prods_m)): ?>
    <div style="grid-column:1/-1;text-align:center;padding:32px;color:#9CA3AF;font-size:13px">
      <i class="fas fa-search" style="font-size:32px;display:block;margin-bottom:8px;opacity:.3"></i>
      Aucun produit <?= e($m) ?> disponible actuellement
    </div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
</div>

<!-- ══ POURQUOI NOUS CHOISIR ══ -->
<div class="home-sec alt" style="margin:0 -16px;padding:32px 16px">
  <div class="sec-header">
    <div>
      <div class="sec-tag">Nos engagements</div>
      <div class="sec-title">Pourquoi nous choisir ?</div>
    </div>
  </div>
  <div class="why-grid-v4">
    <div class="why-card-v4">
      <div class="why-ico-v4"><i class="fas fa-boxes" style="color:#FF6A00"></i></div>
      <div class="why-num"><?= $nbProduits ?>+</div>
      <div class="why-title">Références en stock</div>
      <div class="why-desc">Le plus grand stock de pièces smartphones en Côte d'Ivoire, disponible immédiatement</div>
    </div>
    <div class="why-card-v4">
      <div class="why-ico-v4"><i class="fas fa-shipping-fast" style="color:#FF6A00"></i></div>
      <div class="why-num">24h</div>
      <div class="why-title">Réparation sur place</div>
      <div class="why-desc">Commandez avant 18h, livré le lendemain à Abidjan. Technicien disponible 7j/7 à Abidjan</div>
    </div>
    <div class="why-card-v4">
      <div class="why-ico-v4"><i class="fas fa-shield-alt" style="color:#FF6A00"></i></div>
      <div class="why-num">48h</div>
      <div class="why-title">Garantie de retour</div>
      <div class="why-desc">Pièce défectueuse ? Retour et remboursement dans les 48 heures, sans discussion</div>
    </div>
    <div class="why-card-v4">
      <div class="why-ico-v4"><i class="fas fa-mobile-alt" style="color:#FF6A00"></i></div>
      <div class="why-num">4</div>
      <div class="why-title">Moyens de paiement</div>
      <div class="why-desc">Orange Money, MTN Money, Wave et Moov Money — paiement 100% mobile simplifié</div>
    </div>
  </div>
</div>

<!-- ══ AVIS CLIENTS ══ -->
<div class="home-sec">
  <div class="sec-header">
    <div>
      <div class="sec-tag">Témoignages</div>
      <div class="sec-title">Avis clients vérifiés</div>
    </div>
    <div style="display:flex;align-items:center;gap:6px">
      <span style="font-size:20px;font-weight:900;color:#111">4.9</span>
      <?php for($i=0;$i<5;$i++) echo '<span style="color:#F59E0B;font-size:14px">★</span>'; ?>
      <span style="font-size:10px;color:#9CA3AF">(186 avis)</span>
    </div>
  </div>
  <div class="reviews-grid-v4">
    <?php foreach([
      ['KO','#3B82F6','Konan Olivier','Abidjan, Plateau','Écran reçu en 24h, qualité parfaite. Mon Samsung A52 est comme neuf ! Service ultra rapide.','3 jours'],
      ['DA','#10B981','Diabaté Aminata','Yopougon','Prix imbattables et service WhatsApp très réactif. Je recommande vivement REPARE-MOI CI !','1 semaine'],
      ['TM','#F97316','Touré Mamadou','Cocody, Abidjan','Batterie iPhone 12 reçue en parfait état. Tient 2 jours sur une charge. Bluffant pour ce prix !','2 semaines'],
    ] as [$init,$col,$name,$loc,$text,$date]): ?>
    <div class="review-v4">
      <div class="rev-head">
        <div class="rev-avatar" style="background:<?= $col ?>22;color:<?= $col ?>"><?= $init ?></div>
        <div>
          <div class="rev-name"><?= $name ?></div>
          <div class="rev-info"><?= $loc ?> &nbsp;·&nbsp; Il y a <?= $date ?></div>
        </div>
      </div>
      <div class="rev-stars"><?= str_repeat('<span style="color:#F59E0B;font-size:12px">★</span>',5) ?></div>
      <div class="rev-text">"<?= $text ?>"</div>
      <div class="rev-verified"><i class="fas fa-check-circle"></i> Achat vérifié</div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- ══ BLOG SEO ══ -->
<div class="home-sec alt" style="margin:0 -16px;padding:28px 16px">
  <div class="sec-header">
    <div>
      <div class="sec-tag">Conseils &amp; Guides</div>
      <div class="sec-title">Articles récents</div>
    </div>
    <a href="<?= SITE_URL ?>/blog.php" class="sec-link">Voir tous les articles →</a>
  </div>
  <div class="blog-grid-v4">
    <?php foreach([
      ['Guide','Comment changer son écran Samsung sans aller chez un réparateur ?','Étape par étape, notre guide complet pour remplacer vous-même votre écran Samsung A-series...','#EFF6FF','#BFDBFE'],
      ['Conseil','Comment choisir la bonne batterie pour son iPhone 11, 12 ou 13 ?','Toutes les batteries ne se valent pas. Voici les critères essentiels pour bien choisir...','#ECFDF5','#A7F3D0'],
      ['Test','Écran INCELL vs OLED : Lequel choisir selon votre budget ?','On a testé les deux types d\'écran pendant 2 mois. Notre verdict complet pour vous aider...','#FFF7ED','#FED7AA'],
    ] as [$tag,$title,$excerpt,$bg,$ico_bg]): ?>
    <a href="<?= SITE_URL ?>/blog/
      <div class="blog-img-v4" style="background:<?= $bg ?>">
        <span class="blog-tag-v4"><?= $tag ?></span>
        <div style="width:80px;height:50px;border-radius:8px;background:<?= $ico_bg ?>;opacity:.5"></div>
      </div>
      <div class="blog-body-v4">
        <div class="blog-title-v4"><?= $title ?></div>
        <div class="blog-excerpt-v4"><?= $excerpt ?></div>
        <div class="blog-cta-v4">Lire l'article <i class="fas fa-arrow-right" style="font-size:9px"></i></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

</div><!-- /.container -->

<!-- ══ BOUTON WHATSAPP FLOTTANT ══ -->
<a href="https://wa.me/<?= SITE_WHATSAPP ?>?text=<?= urlencode('Bonjour REPARE-MOI CI ! Je cherche une pièce détachée, pouvez-vous m\'aider ?') ?>"
   target="_blank" rel="noopener" class="wa-fab" title="Commander sur WhatsApp">
  <i class="fab fa-whatsapp"></i>
</a>

<script>
// ── Countdown deals ──
(function(){
  var end = new Date();
  end.setHours(end.getHours()+4, end.getMinutes()+32, end.getSeconds()+18);
  function tick(){
    var diff = Math.max(0, end - Date.now());
    var h = Math.floor(diff/3600000);
    var m = Math.floor((diff%3600000)/60000);
    var s = Math.floor((diff%60000)/1000);
    var fz = function(n){return String(n).padStart(2,'0')};
    var eh=document.getElementById('cdH'),em=document.getElementById('cdM'),es=document.getElementById('cdS');
    if(eh) eh.textContent=fz(h);
    if(em) em.textContent=fz(m);
    if(es) es.textContent=fz(s);
    if(diff>0) setTimeout(tick,1000);
  }
  tick();
})();

// ── Tabs marques ──
var tabPanes = document.querySelectorAll('.brand-tab-pane');
function switchBrand(name, el){
  tabPanes.forEach(function(p){p.style.display='none'});
  document.querySelectorAll('.btab-v4').forEach(function(b){b.classList.remove('active')});
  var idx = <?= json_encode(array_values($tab_marques)) ?>.indexOf(name);
  if(idx>=0 && tabPanes[idx]) tabPanes[idx].style.display='grid';
  el.classList.add('active');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
