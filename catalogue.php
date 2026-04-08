<?php
require_once __DIR__ . '/includes/fonctions.php';

/* ── Paramètres ── */
$marque    = trim($_GET['marque']    ?? '');
$categorie = trim($_GET['categorie'] ?? '');
$couleur   = trim($_GET['couleur']   ?? '');
$q         = trim($_GET['q']         ?? '');
$tri       = in_array($_GET['tri']??'',['prix_asc','prix_desc','nom_asc','stock_desc','deal']) ? $_GET['tri'] : 'nom_asc';
$prix_min  = isset($_GET['prix_min']) && is_numeric($_GET['prix_min']) ? (int)$_GET['prix_min'] : 0;
$prix_max  = isset($_GET['prix_max']) && is_numeric($_GET['prix_max']) ? (int)$_GET['prix_max'] : 0;
$page      = max(1, (int)($_GET['page'] ?? 1));
$perPage   = 20;
$offset    = ($page - 1) * $perPage;

/* ── Requête produits ── */
function q_produits($filtres, $limit, $offset) {
    $pdo = getPDO(); $where = ['actif=1']; $bind = [];
    if (!empty($filtres['marque']))    { $where[]='marque=?';    $bind[]=$filtres['marque']; }
    if (!empty($filtres['categorie'])) { $where[]='categorie=?'; $bind[]=$filtres['categorie']; }
    if (!empty($filtres['couleur']))   { $where[]='couleur LIKE ?'; $bind[]='%'.$filtres['couleur'].'%'; }
    if (!empty($filtres['q'])) {
        $where[]='(nom LIKE ? OR marque LIKE ? OR categorie LIKE ? OR description LIKE ?)';
        $qp='%'.$filtres['q'].'%'; $bind=array_merge($bind,[$qp,$qp,$qp,$qp]);
    }
    if (!empty($filtres['prix_min'])) { $where[]='prix>=?'; $bind[]=$filtres['prix_min']; }
    if (!empty($filtres['prix_max'])) { $where[]='prix<=?'; $bind[]=$filtres['prix_max']; }
    $t = $filtres['tri'] ?? 'nom_asc';
    if     ($t==='prix_asc')   $order='prix ASC';
    elseif ($t==='prix_desc')  $order='prix DESC';
    elseif ($t==='stock_desc') $order='stock DESC';
    elseif ($t==='deal')       $order='deal DESC, id DESC';
    else                       $order='nom ASC';
    $sql = 'SELECT * FROM rm_produits WHERE '.implode(' AND ',$where)." ORDER BY $order LIMIT ? OFFSET ?";
    $bind[]=$limit; $bind[]=$offset;
    $st=$pdo->prepare($sql); $st->execute($bind);
    return $st->fetchAll(PDO::FETCH_ASSOC);
}

function q_count($filtres) {
    $pdo=getPDO(); $where=['actif=1']; $bind=[];
    if (!empty($filtres['marque']))    { $where[]='marque=?';    $bind[]=$filtres['marque']; }
    if (!empty($filtres['categorie'])) { $where[]='categorie=?'; $bind[]=$filtres['categorie']; }
    if (!empty($filtres['couleur']))   { $where[]='couleur LIKE ?'; $bind[]='%'.$filtres['couleur'].'%'; }
    if (!empty($filtres['q'])) {
        $where[]='(nom LIKE ? OR marque LIKE ? OR categorie LIKE ? OR description LIKE ?)';
        $qp='%'.$filtres['q'].'%'; $bind=array_merge($bind,[$qp,$qp,$qp,$qp]);
    }
    if (!empty($filtres['prix_min'])) { $where[]='prix>=?'; $bind[]=$filtres['prix_min']; }
    if (!empty($filtres['prix_max'])) { $where[]='prix<=?'; $bind[]=$filtres['prix_max']; }
    $st=$pdo->prepare('SELECT COUNT(*) FROM rm_produits WHERE '.implode(' AND ',$where));
    $st->execute($bind); return (int)$st->fetchColumn();
}

$filtres = array_filter(compact('marque','categorie','q','couleur'));
if ($prix_min>0) $filtres['prix_min']=$prix_min;
if ($prix_max>0) $filtres['prix_max']=$prix_max;
$filtres['tri'] = $tri;

$produits = q_produits($filtres, $perPage, $offset);
$total    = q_count($filtres);
$pages    = max(1, (int)ceil($total / $perPage));

$pdo = getPDO();
$toutes_marques = $pdo->query("SELECT marque, COUNT(*) nb FROM rm_produits WHERE actif=1 GROUP BY marque ORDER BY nb DESC")->fetchAll();
$toutes_cats    = $pdo->query("SELECT categorie, COUNT(*) nb FROM rm_produits WHERE actif=1 GROUP BY categorie ORDER BY nb DESC")->fetchAll();
$toutes_couleurs= $pdo->query("SELECT DISTINCT couleur FROM rm_produits WHERE actif=1 AND couleur IS NOT NULL AND couleur!='' ORDER BY couleur")->fetchAll(PDO::FETCH_COLUMN);
$prix_stats     = $pdo->query('SELECT MIN(prix) mn, MAX(prix) mx FROM rm_produits WHERE actif=1')->fetch();
$pmin_global    = (int)($prix_stats['mn'] ?? 0);
$pmax_global    = (int)($prix_stats['mx'] ?? 200000);

$titre = $marque ?: ($categorie ?: ($q ? "\"$q\"" : 'Tous les produits'));
$pageTitle = e($titre).' — REPARE-MOI CI';

require_once __DIR__ . '/includes/header.php';

/* ── URL helper ── */
function furl($overrides=[]) {
    global $marque,$categorie,$q,$tri,$prix_min,$prix_max,$couleur;
    $p = array_filter([
        'marque'=>$marque, 'categorie'=>$categorie, 'q'=>$q,
        'couleur'=>$couleur,
        'tri'=>($tri!=='nom_asc'?$tri:''),
        'prix_min'=>($prix_min>0?$prix_min:''),
        'prix_max'=>($prix_max>0?$prix_max:''),
    ]);
    $p = array_merge($p,$overrides);
    $p = array_filter($p, fn($v)=>$v!==''&&$v!==null);
    return 'catalogue.php'.($p?'?'.http_build_query($p):'');
}
$cats_icons = ['Écran'=>'fa-tv','Batterie'=>'fa-battery-three-quarters','Coque arrière'=>'fa-mobile-alt','Vitre'=>'fa-mobile-screen-button','Caméra'=>'fa-camera','Haut-parleur'=>'fa-volume-high','Connecteur'=>'fa-plug','Nappe'=>'fa-bezier-curve','Chassis'=>'fa-layer-group'];
$has_filters = $marque||$categorie||$q||$couleur||$prix_min||$prix_max;
?>


<style>
/* ══ CATALOGUE — CSS complet ══ */
:root{--primary:#F07818;--primary-dark:#C85F08;--primary-light:#FFF3E8;--dark-header:#111827;--dark-nav:#1F2937;--border:#E5E7EB;--text-dark:#1A1A2E;--text-muted:#6B7280;--success:#10B981;--rupture:#EF4444;--card-bg:#fff;--body-bg:#F4F6F9;--radius:8px}
*{box-sizing:border-box}
body{background:var(--body-bg);font-family:'Open Sans',sans-serif;font-size:14px;color:var(--text-dark)}
.container{max-width:1240px;margin:0 auto;padding:0 16px}
a{text-decoration:none;color:inherit}

/* ── Breadcrumb ── */
.breadcrumb{display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-muted);padding:10px 0;flex-wrap:wrap}
.breadcrumb a{color:var(--primary);font-weight:500}
.breadcrumb i{font-size:9px}

/* ── Ad banner ── */
.ad-banner{background:#f9fafb;border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:8px 0;text-align:center}
.ad-slot{position:relative;display:flex;align-items:center;justify-content:center}
.ad-label{font-size:9px;color:#d1d5db;text-transform:uppercase;letter-spacing:.5px;position:absolute;top:3px;left:8px}
.ad-placeholder{display:flex;align-items:center;justify-content:center;gap:10px;background:#f3f4f6;border:1px dashed #d1d5db;border-radius:6px;color:#9ca3af;font-size:12px;padding:14px 20px;width:100%;min-height:68px}
.ad-placeholder i{font-size:16px;opacity:.5}
.ad-placeholder-sm{min-height:200px;flex-direction:column;text-align:center}
.ad-partner{display:flex;align-items:center;gap:14px;background:var(--card-bg);border:1px solid var(--border);border-radius:var(--radius);padding:14px 18px;transition:.2s}
.ad-partner:hover{border-color:var(--primary);box-shadow:0 4px 16px rgba(240,120,24,.12)}
.ad-partner-icon{width:46px;height:46px;background:var(--primary-light);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0}
.ad-partner-text strong{display:block;font-size:13px;font-weight:700;color:var(--text-dark)}
.ad-partner-text span{font-size:11.5px;color:var(--text-muted)}
.ad-partner-cta{margin-left:auto;background:var(--primary);color:#fff;padding:7px 16px;border-radius:6px;font-size:12px;font-weight:700;white-space:nowrap;flex-shrink:0}

/* ── Cat page ── */
.cat-page{padding:14px 0}
.catalogue-wrap{display:grid;grid-template-columns:256px 1fr;gap:22px;align-items:start}

/* ── Mobile topbar ── */
.mobile-topbar{display:none;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid var(--border);margin-bottom:12px}
.btn-filter-open{display:flex;align-items:center;gap:8px;background:#fff;border:1px solid var(--border);padding:8px 16px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;position:relative;transition:.2s}
.btn-filter-open:hover{border-color:var(--primary);color:var(--primary)}
.filter-dot{position:absolute;top:5px;right:5px;width:7px;height:7px;background:var(--primary);border-radius:50%}
.mobile-sort select{border:1px solid var(--border);border-radius:6px;padding:8px 12px;font-size:12px;outline:none;background:#fff}
.mobile-count{font-size:12px;color:var(--text-muted);margin-left:auto;white-space:nowrap}

/* ── Filter panel ── */
.filter-panel{position:sticky;top:76px;display:flex;flex-direction:column;gap:10px}
.filter-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
.filter-card-head{display:flex;align-items:center;justify-content:space-between;padding:11px 16px;border-bottom:1px solid var(--border);background:#fafafa}
.filter-card-head h3{font-size:13px;font-weight:700;color:var(--text-dark);display:flex;align-items:center;gap:7px;margin:0}
.filter-card-head h3 i{color:var(--primary);width:14px}
.filter-card-body{padding:12px 16px}
.filter-reset{width:100%;background:none;border:1px solid var(--rupture);color:var(--rupture);padding:8px;border-radius:6px;font-size:12px;font-weight:600;display:flex;align-items:center;justify-content:center;gap:6px;transition:.2s;cursor:pointer;text-decoration:none}
.filter-reset:hover{background:var(--rupture);color:#fff}
.filter-input{display:flex;border:1px solid var(--border);border-radius:6px;overflow:hidden}
.filter-input:focus-within{border-color:var(--primary)}
.filter-input input{flex:1;border:none;padding:9px 12px;font-size:13px;outline:none;background:transparent}
.filter-input button{background:var(--primary);border:none;color:#fff;padding:0 14px;cursor:pointer}
.filter-input button:hover{background:var(--primary-dark)}
.filter-list{list-style:none;padding:0;max-height:220px;overflow-y:auto;scrollbar-width:thin}
.filter-list.collapsed li:nth-child(n+7){display:none}
.filter-list li a{display:flex;align-items:center;justify-content:space-between;padding:7px 4px;font-size:13px;color:var(--text-dark);border-bottom:1px solid #f3f4f6;transition:.2s;border-radius:4px}
.filter-list li a:hover{color:var(--primary);padding-left:8px}
.filter-list li a.active{color:var(--primary);font-weight:700;padding-left:8px;border-left:3px solid var(--primary);background:var(--primary-light)}
.filter-count{background:#f3f4f6;color:var(--text-muted);font-size:10px;padding:2px 7px;border-radius:10px;font-weight:600;flex-shrink:0}
.filter-list li a.active .filter-count{background:rgba(240,120,24,.15);color:var(--primary)}
.btn-see-more{width:100%;background:none;border:none;color:var(--primary);font-size:12px;font-weight:600;padding:6px 0 0;text-align:left;display:flex;align-items:center;gap:4px;cursor:pointer}
.price-inputs{display:flex;align-items:center;gap:8px;margin-bottom:10px}
.price-field{flex:1;display:flex;flex-direction:column;gap:3px}
.price-field label{font-size:10px;color:var(--text-muted);font-weight:600;text-transform:uppercase}
.price-field input{border:1px solid var(--border);border-radius:6px;padding:8px;font-size:13px;outline:none;width:100%;text-align:center}
.price-field input:focus{border-color:var(--primary)}
.price-sep{color:var(--text-muted);font-size:18px;margin-top:14px;flex-shrink:0}
.price-chips{display:flex;gap:5px;flex-wrap:wrap;margin-bottom:10px}
.price-chip{padding:4px 10px;border:1px solid var(--border);border-radius:20px;font-size:11px;font-weight:600;cursor:pointer;color:var(--text-muted);transition:.2s;background:#fff;text-decoration:none;display:inline-block}
.price-chip:hover,.price-chip.active{background:var(--primary);color:#fff;border-color:var(--primary)}
.btn-apply{width:100%;background:var(--primary);color:#fff;border:none;padding:9px;border-radius:6px;font-size:13px;font-weight:700;transition:.2s;cursor:pointer;margin-top:8px}
.btn-apply:hover{background:var(--primary-dark)}

/* ── Cat toolbar ── */
.cat-main{}
.cat-toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;gap:10px;flex-wrap:wrap}
.cat-title-wrap{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.cat-title{font-size:18px;font-weight:700;margin:0}
.cat-count-badge{background:#f3f4f6;color:var(--text-muted);font-size:12px;padding:4px 10px;border-radius:20px;font-weight:600}
.cat-sort-wrap{display:flex;align-items:center;gap:8px}
.cat-sort-wrap i{color:var(--text-muted);font-size:13px}
.cat-sort{border:1px solid var(--border);border-radius:6px;padding:8px 12px;font-size:13px;outline:none;background:#fff;color:var(--text-dark);cursor:pointer}
.cat-sort:focus{border-color:var(--primary)}

/* ── Active filters ── */
.active-filters{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:12px}
.af-label{font-size:12px;color:var(--text-muted);font-weight:600;white-space:nowrap}
.af-chip{display:inline-flex;align-items:center;gap:6px;background:var(--primary-light);border:1px solid rgba(240,120,24,.3);color:var(--primary);font-size:11.5px;font-weight:600;padding:4px 10px;border-radius:20px;transition:.2s}
.af-chip:hover{background:var(--primary);color:#fff}
.af-chip i{font-size:9px}
.af-clear{background:#fee2e2;border:1px solid #fca5a5;color:var(--rupture);font-size:11.5px;font-weight:600;padding:4px 10px;border-radius:20px;transition:.2s;display:inline-flex;align-items:center;gap:4px}
.af-clear:hover{background:var(--rupture);color:#fff}

/* ── Products grid ── */
.prod-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}

/* ── Product card ── */
.product-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;transition:.25s;display:flex;flex-direction:column}
.product-card:hover{border-color:var(--primary);box-shadow:0 6px 24px rgba(240,120,24,.18);transform:translateY(-3px)}
.product-img{background:#f9fafb;height:155px;display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative;flex-shrink:0}
.product-img img{width:100%;height:100%;object-fit:cover;transition:.3s}
.product-img .img-ph{font-size:48px;opacity:.2;color:#9ca3af}
.badge-rupture{position:absolute;top:8px;left:8px;background:#ef4444;color:#fff;font-size:9px;font-weight:700;padding:3px 8px;border-radius:4px;text-transform:uppercase}
.badge-deal{position:absolute;top:8px;right:8px;background:var(--primary);color:#fff;font-size:9px;font-weight:700;padding:3px 8px;border-radius:4px}
.product-info{padding:10px;flex:1;display:flex;flex-direction:column}
.product-meta-row{display:flex;align-items:center;justify-content:space-between;gap:4px;margin-bottom:4px}
.product-brand{font-size:10px;color:var(--primary);font-weight:700;text-transform:uppercase;letter-spacing:.5px}
.product-cat-badge{font-size:9px;background:var(--primary-light);color:var(--primary);padding:2px 6px;border-radius:3px;font-weight:600;white-space:nowrap}
.product-name{font-size:12.5px;font-weight:600;color:var(--text-dark);line-height:1.35;min-height:34px;margin-bottom:6px;flex:1;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical}
.product-qualite{font-size:10px;color:var(--text-muted);margin-bottom:4px;font-style:italic}
.product-price{font-size:16px;font-weight:800;color:var(--primary);margin-bottom:5px}
.product-stock{font-size:10px;color:var(--success);display:flex;align-items:center;gap:4px;margin-bottom:8px}
.product-stock.out{color:var(--rupture)}
.btn-add{width:100%;background:var(--primary);color:#fff;border:none;padding:9px;border-radius:5px;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;gap:6px;transition:.2s;cursor:pointer;flex:1}
.btn-add:hover:not(:disabled){background:var(--primary-dark)}
.btn-add:disabled{background:#d1d5db;cursor:not-allowed;color:#9ca3af}
.btn-wa{background:#25D366;color:#fff;border:none;border-radius:5px;padding:9px 10px;display:inline-flex;align-items:center;justify-content:center;gap:6px;font-weight:700;transition:.2s;cursor:pointer;text-decoration:none;flex-shrink:0}
.btn-wa:hover{background:#1ebe5d}

/* ── Empty state ── */
.empty-state{text-align:center;padding:60px 20px;color:var(--text-muted)}
.empty-state i{font-size:52px;margin-bottom:16px;display:block;opacity:.25}
.empty-state p{font-size:15px}
.btn-primary{background:var(--primary);color:#fff;padding:11px 24px;border-radius:6px;font-weight:700;font-size:14px;transition:.2s;display:inline-flex;align-items:center;gap:8px;border:none;cursor:pointer;text-decoration:none}
.btn-primary:hover{background:var(--primary-dark)}

/* ── Pagination ── */
.pagination{display:flex;align-items:center;gap:5px;justify-content:center;padding:28px 0 8px;flex-wrap:wrap}
.page-btn{min-width:38px;height:38px;border-radius:6px;display:flex;align-items:center;justify-content:center;border:1px solid var(--border);font-size:13px;font-weight:600;transition:.2s;background:#fff;color:var(--text-dark);text-decoration:none}
.page-btn:hover{border-color:var(--primary);color:var(--primary)}
.page-btn.active{background:var(--primary);color:#fff;border-color:var(--primary)}
.page-btn.nav-btn{background:#fff}
.page-dots{color:var(--text-muted);padding:0 4px;user-select:none;font-size:16px}
.page-info{font-size:12px;color:var(--text-muted);margin-left:6px;white-space:nowrap}

/* ── Filter overlay + drawer (mobile) ── */
.filter-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:898}
.filter-overlay.show{display:block}
.filter-drawer{position:fixed;top:0;left:0;bottom:0;width:300px;background:var(--body-bg);z-index:899;overflow-y:auto;transform:translateX(-100%);transition:transform .3s ease}
.filter-drawer.open{transform:translateX(0);box-shadow:4px 0 32px rgba(0,0,0,.2)}
.filter-drawer-head{display:flex;align-items:center;justify-content:space-between;padding:16px;background:var(--dark-header);color:#fff;position:sticky;top:0;z-index:1}
.filter-drawer-head span{font-size:15px;font-weight:700;display:flex;align-items:center;gap:8px}
.filter-drawer-head span i{color:var(--primary)}
.btn-close-drawer{background:none;border:none;color:#fff;font-size:20px;cursor:pointer;padding:4px}
.filter-drawer-body{padding:12px}

/* ── Responsive ── */
@media(max-width:768px){
  .mobile-topbar{display:flex}
  .catalogue-wrap{grid-template-columns:1fr}
  .filter-panel{display:none}
  .prod-grid{grid-template-columns:repeat(2,1fr);gap:10px}
  .cat-toolbar .cat-sort-wrap{display:none}
  .cat-title{font-size:16px}
  .product-img{height:130px}
  .product-info{padding:8px}
  .product-name{font-size:12px;min-height:30px}
  .product-price{font-size:14px}
  .btn-add{font-size:11px;padding:8px}
  .page-info{display:none}
}
@media(max-width:480px){
  .prod-grid{grid-template-columns:repeat(2,1fr);gap:8px}
  .container{padding:0 10px}
  .product-img{height:110px}
}
</style>

<!-- ── Bannière pub ── -->
<div class="ad-banner">
  <div class="container">
    <div class="ad-slot" style="max-width:728px;margin:0 auto">
      <span class="ad-label">Publicité</span>
      <div class="ad-placeholder">
        <i class="fas fa-ad"></i><span>Espace publicitaire — 728×90</span>
      </div>
    </div>
  </div>
</div>

<div class="container cat-page">

  <!-- Breadcrumb -->
  <nav class="breadcrumb">
    <a href="<?= SITE_URL ?>/">Accueil</a><i class="fas fa-chevron-right"></i>
    <a href="<?= SITE_URL ?>/catalogue.php">Catalogue</a>
    <?php if($marque):    ?><i class="fas fa-chevron-right"></i><span><?= e($marque) ?></span><?php endif; ?>
    <?php if($categorie): ?><i class="fas fa-chevron-right"></i><span><?= e($categorie) ?></span><?php endif; ?>
    <?php if($q):         ?><i class="fas fa-chevron-right"></i><span>« <?= e($q) ?> »</span><?php endif; ?>
  </nav>

  <!-- Barre mobile -->
  <div class="mobile-topbar">
    <button class="btn-filter-open" onclick="openFilters()">
      <i class="fas fa-sliders-h"></i> Filtres
      <?php if($has_filters): ?><span class="filter-dot"></span><?php endif; ?>
    </button>
    <div class="mobile-sort">
      <form method="get" id="msort">
        <?php foreach(array_filter(['marque'=>$marque,'categorie'=>$categorie,'q'=>$q,'couleur'=>$couleur,'prix_min'=>$prix_min>0?$prix_min:'','prix_max'=>$prix_max>0?$prix_max:'']) as $k=>$v): ?>
        <input type="hidden" name="<?=$k?>" value="<?=e($v)?>">
        <?php endforeach; ?>
        <select name="tri" onchange="this.form.submit()">
          <option value="nom_asc"    <?=$tri==='nom_asc'   ?'selected':''?>>A → Z</option>
          <option value="prix_asc"   <?=$tri==='prix_asc'  ?'selected':''?>>Prix ↑</option>
          <option value="prix_desc"  <?=$tri==='prix_desc' ?'selected':''?>>Prix ↓</option>
          <option value="stock_desc" <?=$tri==='stock_desc'?'selected':''?>>Stock</option>
          <option value="deal"       <?=$tri==='deal'      ?'selected':''?>>Deals</option>
        </select>
      </form>
    </div>
    <span class="mobile-count"><?=$total?> article<?=$total>1?'s':''?></span>
  </div>

  <div class="catalogue-wrap">

    <!-- ══ FILTRES (desktop sidebar + mobile drawer) ══ -->
    <?php
    // Shared filter HTML — used for both desktop and mobile drawer
    ob_start(); ?>
    <div class="filter-body-content">
      <?php if($has_filters): ?>
      <div style="margin-bottom:10px">
        <a href="catalogue.php" class="filter-reset"><i class="fas fa-times-circle"></i> Réinitialiser les filtres</a>
      </div>
      <?php endif; ?>

      <!-- 1. Recherche -->
      <div class="filter-card" style="margin-bottom:10px">
        <div class="filter-card-head"><h3><i class="fas fa-search"></i> Recherche</h3></div>
        <div class="filter-card-body">
          <form method="get">
            <?php foreach(array_filter(['marque'=>$marque,'categorie'=>$categorie,'couleur'=>$couleur,'tri'=>$tri!=='nom_asc'?$tri:'']) as $k=>$v): ?><input type="hidden" name="<?=$k?>" value="<?=e($v)?>"><?php endforeach; ?>
            <div class="filter-input">
              <input type="text" name="q" placeholder="Modèle, pièce..." value="<?=e($q)?>" autocomplete="off">
              <button type="submit"><i class="fas fa-search"></i></button>
            </div>
          </form>
        </div>
      </div>

      <!-- 2. Marque -->
      <div class="filter-card" style="margin-bottom:10px">
        <div class="filter-card-head"><h3><i class="fas fa-mobile-alt"></i> Marque</h3></div>
        <div class="filter-card-body" style="padding:8px 16px">
          <ul class="filter-list collapsed" id="marqueList">
            <li><a href="<?=furl(['marque'=>'','page'=>1])?>" class="<?=!$marque?'active':''?>">Toutes <span class="filter-count"><?=array_sum(array_column($toutes_marques,'nb'))?></span></a></li>
            <?php foreach($toutes_marques as $m): ?>
            <li><a href="<?=furl(['marque'=>$m['marque'],'page'=>1])?>" class="<?=$m['marque']===$marque?'active':''?>"><?=e($m['marque'])?> <span class="filter-count"><?=$m['nb']?></span></a></li>
            <?php endforeach; ?>
          </ul>
          <?php if(count($toutes_marques)>6): ?>
          <button class="btn-see-more" onclick="toggleList('marqueList',this)"><i class="fas fa-chevron-down"></i> Voir plus</button>
          <?php endif; ?>
        </div>
      </div>

      <!-- 3. Catégorie -->
      <div class="filter-card" style="margin-bottom:10px">
        <div class="filter-card-head"><h3><i class="fas fa-th-large"></i> Catégorie</h3></div>
        <div class="filter-card-body" style="padding:8px 16px">
          <ul class="filter-list collapsed" id="catList">
            <li><a href="<?=furl(['categorie'=>'','page'=>1])?>" class="<?=!$categorie?'active':''?>">Toutes</a></li>
            <?php foreach($toutes_cats as $c): ?>
            <li>
              <a href="<?=furl(['categorie'=>$c['categorie'],'page'=>1])?>" class="<?=$c['categorie']===$categorie?'active':''?>">
                <?php $ico=$cats_icons[$c['categorie']]??'fa-wrench'; ?>
                <i class="fas <?=$ico?>" style="width:14px;color:var(--primary);opacity:.7"></i>
                <?=e($c['categorie'])?>
                <span class="filter-count"><?=$c['nb']?></span>
              </a>
            </li>
            <?php endforeach; ?>
          </ul>
          <?php if(count($toutes_cats)>6): ?>
          <button class="btn-see-more" onclick="toggleList('catList',this)"><i class="fas fa-chevron-down"></i> Voir plus</button>
          <?php endif; ?>
        </div>
      </div>

      <!-- 4. Prix -->
      <div class="filter-card" style="margin-bottom:10px">
        <div class="filter-card-head"><h3><i class="fas fa-tag"></i> Prix (FCFA)</h3></div>
        <div class="filter-card-body">
          <form method="get" id="prixForm">
            <?php foreach(array_filter(['marque'=>$marque,'categorie'=>$categorie,'q'=>$q,'couleur'=>$couleur,'tri'=>$tri!=='nom_asc'?$tri:'']) as $k=>$v): ?><input type="hidden" name="<?=$k?>" value="<?=e($v)?>"><?php endforeach; ?>
            <div class="price-chips" style="margin-bottom:10px">
              <?php foreach([[0,10000,'< 10k'],[10000,25000,'10–25k'],[25000,50000,'25–50k'],[50000,0,'> 50k']] as [$mn,$mx,$lbl]): ?>
              <a href="<?=furl(['prix_min'=>$mn,'prix_max'=>$mx,'page'=>1])?>" class="price-chip <?=($prix_min==$mn&&$prix_max==$mx)?'active':''?>"><?=$lbl?></a>
              <?php endforeach; ?>
            </div>
            <div class="price-inputs">
              <div class="price-field">
                <label>Min</label>
                <input type="number" name="prix_min" id="pmin" value="<?=$prix_min?:$pmin_global?>" min="<?=$pmin_global?>" max="<?=$pmax_global?>" step="500">
              </div>
              <span class="price-sep">—</span>
              <div class="price-field">
                <label>Max</label>
                <input type="number" name="prix_max" id="pmax" value="<?=$prix_max?:$pmax_global?>" min="<?=$pmin_global?>" max="<?=$pmax_global?>" step="500">
              </div>
            </div>
            <button type="submit" class="btn-apply" style="margin-top:8px">Appliquer le filtre</button>
          </form>
        </div>
      </div>

      <!-- 5. Couleur -->
      <?php if($toutes_couleurs): ?>
      <div class="filter-card" style="margin-bottom:10px">
        <div class="filter-card-head"><h3><i class="fas fa-palette"></i> Couleur</h3></div>
        <div class="filter-card-body" style="padding:8px 16px">
          <ul class="filter-list">
            <li><a href="<?=furl(['couleur'=>'','page'=>1])?>" class="<?=!$couleur?'active':''?>">Toutes les couleurs</a></li>
            <?php foreach($toutes_couleurs as $cv): ?>
            <li><a href="<?=furl(['couleur'=>$cv,'page'=>1])?>" class="<?=$cv===$couleur?'active':''?>"><?=e($cv)?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <?php endif; ?>

      <!-- Espace pub sidebar -->
      <div class="ad-slot" style="margin-top:4px">
        <span class="ad-label">Pub</span>
        <div class="ad-placeholder ad-placeholder-sm">
          <i class="fas fa-ad"></i><span>300 × 250</span>
        </div>
      </div>
    </div>
    <?php $filter_html = ob_get_clean(); ?>

    <!-- Desktop sidebar -->
    <aside class="filter-panel" id="filterPanel">
      <?= $filter_html ?>
    </aside>

    <!-- Mobile drawer -->
    <div class="filter-overlay" id="filterOverlay" onclick="closeFilters()"></div>
    <div class="filter-drawer" id="filterDrawer">
      <div class="filter-drawer-head">
        <span><i class="fas fa-sliders-h"></i> Filtres</span>
        <button class="btn-close-drawer" onclick="closeFilters()"><i class="fas fa-times"></i></button>
      </div>
      <div class="filter-drawer-body">
        <?= $filter_html ?>
      </div>
    </div>

    <!-- ══ PRODUITS ══ -->
    <main class="cat-main">

      <!-- Toolbar desktop -->
      <div class="cat-toolbar">
        <div class="cat-title-wrap">
          <h1 class="cat-title"><?=e($titre)?></h1>
          <span class="cat-count-badge"><?=$total?> article<?=$total>1?'s':''?></span>
        </div>
        <div class="cat-sort-wrap">
          <i class="fas fa-sort-amount-down"></i>
          <form method="get" id="sortForm">
            <?php foreach(array_filter(['marque'=>$marque,'categorie'=>$categorie,'q'=>$q,'couleur'=>$couleur,'prix_min'=>$prix_min>0?$prix_min:'','prix_max'=>$prix_max>0?$prix_max:'']) as $k=>$v): ?><input type="hidden" name="<?=$k?>" value="<?=e($v)?>"><?php endforeach; ?>
            <select name="tri" class="cat-sort" onchange="this.form.submit()">
              <option value="nom_asc"    <?=$tri==='nom_asc'   ?'selected':''?>>Nom A → Z</option>
              <option value="prix_asc"   <?=$tri==='prix_asc'  ?'selected':''?>>Prix croissant</option>
              <option value="prix_desc"  <?=$tri==='prix_desc' ?'selected':''?>>Prix décroissant</option>
              <option value="stock_desc" <?=$tri==='stock_desc'?'selected':''?>>Mieux stocké</option>
              <option value="deal"       <?=$tri==='deal'      ?'selected':''?>>Deals en tête</option>
            </select>
          </form>
        </div>
      </div>

      <!-- Filtres actifs -->
      <?php $actifs=array_filter(compact('marque','categorie','q','couleur')); if($actifs||$prix_min||$prix_max): ?>
      <div class="active-filters">
        <span class="af-label">Filtres :</span>
        <?php foreach($actifs as $k=>$v): ?>
        <a href="<?=furl([$k=>'','page'=>1])?>" class="af-chip"><?=e($v)?> <i class="fas fa-times"></i></a>
        <?php endforeach; ?>
        <?php if($prix_min||$prix_max): ?>
        <a href="<?=furl(['prix_min'=>'','prix_max'=>'','page'=>1])?>" class="af-chip">
          <?=$prix_min?number_format($prix_min,0,'','.').' FCFA':''?><?=($prix_min&&$prix_max)?' – ':''?><?=$prix_max?number_format($prix_max,0,'','.').' FCFA':''?>
          <i class="fas fa-times"></i>
        </a>
        <?php endif; ?>
        <a href="catalogue.php" class="af-clear">Tout effacer</a>
      </div>
      <?php endif; ?>

      <!-- Grille -->
      <?php if(!$produits): ?>
      <div class="empty-state">
        <i class="fas fa-search"></i>
        <p>Aucun produit pour cette sélection.</p>
        <a href="catalogue.php" class="btn-primary" style="display:inline-flex;margin-top:16px"><i class="fas fa-th-large"></i> Voir tout</a>
      </div>
      <?php else: ?>
      <div class="prod-grid">
        <?php foreach($produits as $p):
          $rup=$p['stock']<1;
          if(!empty($p['image'])) {
            $img = strpos($p['image'],'http')===0 ? $p['image'] : UPLOAD_URL.$p['image'];
          } else {
            $cm=['Écran'=>'ecran','Ecran'=>'ecran','Batterie'=>'batterie','Coque arrière'=>'coque','Vitre'=>'vitre','Caméra'=>'camera','Haut-parleur'=>'hautparleur','Connecteur'=>'connecteur','Nappe'=>'nappe','Chassis'=>'chassis'];
            $img=SITE_URL.'/assets/img/categories/'.($cm[$p['categorie']]??'default').'.png';
          }
        ?>
        <div class="product-card">
          <a href="<?=SITE_URL?>/produit.php?slug=<?=urlencode($p['slug'])?>">
            <div class="product-img">
              <?=$img?'<img src="'.e($img).'" alt="'.e($p['nom']).'" loading="lazy">'
                     :'<span class="img-ph"><i class="fas fa-mobile-alt"></i></span>'?>
              <?=$rup?'<span class="badge-rupture">Rupture</span>':''?>
              <?=(!$rup&&$p['deal'])?'<span class="badge-deal">🔥 Deal</span>':''?>
            </div>
            <div class="product-info">
              <div class="product-meta-row">
                <span class="product-brand"><?=e($p['marque'])?></span>
                <span class="product-cat-badge"><?=e($p['categorie'])?></span>
              </div>
              <div class="product-name"><?=e($p['nom'])?></div>
              <?php if(!empty($p['qualite'])): ?><div class="product-qualite"><?=e($p['qualite'])?></div><?php endif; ?>
              <?php if(!empty($p['couleur'])): ?><div class="product-qualite">🎨 <?=e($p['couleur'])?></div><?php endif; ?>
              <div class="product-price"><?=prix((int)$p['prix'])?></div>
              <div class="product-stock <?=$rup?'out':''?>">
                <?=$rup?'<i class="fas fa-times-circle"></i>':'<i class="fas fa-check-circle"></i>'?>
                <?=$rup?'Rupture':'En stock ('.$p['stock'].')' ?>
              </div>
            </div>
          </a>
          <div style="padding:0 10px 10px">
            <form method="post" action="<?=SITE_URL?>/panier.php" style="display:flex;gap:6px">
              <input type="hidden" name="action"     value="ajouter">
              <input type="hidden" name="produit_id" value="<?=(int)$p['id']?>">
              <input type="hidden" name="csrf"       value="<?=csrf_token()?>">
              <input type="hidden" name="redirect"   value="<?=e($_SERVER['REQUEST_URI'])?>">
              <button type="submit" class="btn-add" <?=$rup?'disabled':''?> style="flex:1">
                <?=$rup?'<i class="fas fa-ban"></i> Indisponible':'<i class="fas fa-cart-plus"></i> Ajouter'?>
              </button>
              <a href="https://wa.me/<?=(defined('SITE_WHATSAPP') ? SITE_WHATSAPP : '2250545362890')?>?text=<?=urlencode('Bonjour, je souhaite commander : '.$p['nom'].' à '.prix((int)$p['prix']))?>"
                 target="_blank" class="btn-wa" style="padding:9px 10px;border-radius:5px;flex-shrink:0" title="Commander via WhatsApp">
                <i class="fab fa-whatsapp"></i>
              </a>
            </form>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Pub inline (page 1 uniquement) -->
      <?php if($page===1&&!$q): ?>
      <div style="margin:20px 0">
        <a href="https://www.kdo.ci" target="_blank" rel="noopener" class="ad-partner">
          <div class="ad-partner-icon">🎁</div>
          <div class="ad-partner-text">
            <strong>AnnoncesKDO.ci</strong>
            <span>Marketplace Côte d'Ivoire · Achetez &amp; vendez partout en CI</span>
          </div>
          <span class="ad-partner-cta">Découvrir →</span>
        </a>
      </div>
      <?php endif; ?>

      <!-- Pagination intelligente -->
      <?php if($pages>1): ?>
      <nav class="pagination" aria-label="Pages">
        <?php if($page>1): ?>
        <a href="<?=furl(['page'=>$page-1])?>" class="page-btn nav-btn" aria-label="Précédent"><i class="fas fa-chevron-left"></i></a>
        <?php endif; ?>
        <?php
        $range=2; $dl=false; $dr=false;
        for($i=1;$i<=$pages;$i++):
          $show=($i===1||$i===$pages||($i>=$page-$range&&$i<=$page+$range));
          if(!$show){
            if($i<$page&&!$dl){$dl=true;echo '<span class="page-dots">…</span>';}
            if($i>$page&&!$dr){$dr=true;echo '<span class="page-dots">…</span>';}
            continue;
          }
        ?>
        <a href="<?=furl(['page'=>$i])?>" class="page-btn <?=$i===$page?'active':''?>" <?=$i===$page?'aria-current="page"':''?>><?=$i?></a>
        <?php endfor; ?>
        <?php if($page<$pages): ?>
        <a href="<?=furl(['page'=>$page+1])?>" class="page-btn nav-btn" aria-label="Suivant"><i class="fas fa-chevron-right"></i></a>
        <?php endif; ?>
        <span class="page-info">Page <?=$page?> / <?=$pages?></span>
      </nav>
      <?php endif; ?>
      <?php endif; ?>

      <!-- Pub bas de page -->
      <div class="ad-slot" style="margin:20px 0">
        <span class="ad-label">Publicité</span>
        <div class="ad-placeholder"><i class="fas fa-ad"></i><span>Espace publicitaire — 970×90</span></div>
      </div>

    </main>
  </div>
</div>

<script>
function openFilters(){
  document.getElementById('filterDrawer').classList.add('open');
  document.getElementById('filterOverlay').classList.add('show');
  document.body.style.overflow='hidden';
}
function closeFilters(){
  document.getElementById('filterDrawer').classList.remove('open');
  document.getElementById('filterOverlay').classList.remove('show');
  document.body.style.overflow='';
}
function toggleList(id,btn){
  const ul=document.getElementById(id);
  const collapsed=ul.classList.toggle('collapsed');
  btn.innerHTML=collapsed?'<i class="fas fa-chevron-down"></i> Voir plus':'<i class="fas fa-chevron-up"></i> Voir moins';
}
window.addEventListener('resize',function(){if(window.innerWidth>768)closeFilters();});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
