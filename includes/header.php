<?php
require_once __DIR__ . '/../includes/fonctions.php';
$flash  = flash_get();
$nbPanier = panier_count();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= e($pageTitle ?? SITE_NOM) ?> — Pièces smartphones CI</title>
<meta name="description" content="<?= e($pageDesc ?? 'Pièces détachées smartphones pas chers en Côte d\'Ivoire. Samsung, iPhone, Huawei, Xiaomi. Livraison Abidjan.') ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;900&family=Open+Sans:wght@400;600;700&family=Roboto:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
<!-- Google Analytics GA4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-F10H3HK04K"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-F10H3HK04K');
</script>
<style>
/* ── Logo SVG ── */
.logo-link { display:flex; align-items:center; flex-shrink:0; text-decoration:none; transition:opacity .2s, transform .2s; }
.logo-link:hover { opacity:.88; transform:scale(1.02); }
.logo-svg { display:block; height:46px; width:auto; max-width:210px; }
@media(max-width:768px){ .logo-svg { height:36px; max-width:170px; } }
@media(max-width:480px){ .logo-svg { height:30px; max-width:145px; } }
</style>
<?php if(!empty($extra_head)) echo $extra_head; ?>
</head>
<body>

<?php if ($flash): ?>
<div class="flash flash-<?= e($flash['type']) ?>" id="flash-msg">
  <i class="fas fa-<?= $flash['type']==='success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
  <?= e($flash['msg']) ?>
  <button onclick="this.parentElement.remove()">×</button>
</div>
<?php endif; ?>

<!-- TOPBAR -->
<div class="topbar">
  <div class="container">
    <div class="topbar-inner">
      <div class="topbar-left">
        <a href="tel:<?= SITE_TEL1 ?>"><i class="fas fa-phone"></i><?= SITE_TEL1 ?></a>
        <a href="tel:<?= SITE_TEL2 ?>"><i class="fas fa-phone"></i><?= SITE_TEL2 ?></a>
        <a href="mailto:<?= SITE_EMAIL ?>"><i class="fas fa-envelope"></i><?= SITE_EMAIL ?></a>
      </div>
      <div class="topbar-right">
        <?php if (client_connecte()): $cl = client(); ?>
          <a href="<?= SITE_URL ?>/compte.php"><i class="fas fa-user"></i> <?= e($cl['prenom']) ?></a>
          <a href="<?= SITE_URL ?>/deconnexion.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        <?php else: ?>
          <a href="<?= SITE_URL ?>/connexion.php"><i class="fas fa-sign-in-alt"></i> Connexion</a>
          <a href="<?= SITE_URL ?>/inscription.php"><i class="fas fa-user-plus"></i> Créer un compte</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- HEADER -->
<header class="site-header">
  <div class="container">
    <div class="header-inner">
      <a href="<?= SITE_URL ?>/" class="logo logo-link" aria-label="REPARE-MOI CI - Accueil">
        <!-- Logo SVG inline — fond transparent, adaptatif -->
        <svg class="logo-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 220 52" width="200" height="48" aria-hidden="true">
          <defs>
            <linearGradient id="lgrd" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="#FF8C3A"/>
              <stop offset="100%" stop-color="#E86808"/>
            </linearGradient>
          </defs>
          <!-- Phone body -->
          <rect x="6" y="4" width="29" height="44" rx="5" fill="url(#lgrd)" filter="drop-shadow(0 3px 6px rgba(240,120,24,0.35))"/>
          <!-- Screen -->
          <rect x="9" y="10" width="23" height="29" rx="2" fill="#0f172a" opacity="0.88"/>
          <!-- Screen glow -->
          <rect x="9" y="10" width="10" height="29" rx="2" fill="white" opacity="0.04"/>
          <!-- Speaker -->
          <rect x="15" y="6" width="10" height="2.5" rx="1.2" fill="white" opacity="0.4"/>
          <!-- Home btn -->
          <circle cx="20" cy="41.5" r="3" fill="white" opacity="0.22"/>
          <circle cx="20" cy="41.5" r="1.6" fill="white" opacity="0.38"/>
          <!-- Screen lines -->
          <rect x="12" y="16" width="17" height="2.5" rx="1" fill="#FF8C3A" opacity="0.85"/>
          <rect x="12" y="21" width="12" height="1.5" rx="0.75" fill="white" opacity="0.28"/>
          <rect x="12" y="24.5" width="15" height="1.5" rx="0.75" fill="white" opacity="0.22"/>
          <rect x="12" y="28" width="9" height="1.5" rx="0.75" fill="white" opacity="0.18"/>
          <!-- R letter -->
          <text x="20" y="34" font-family="Arial Black,Arial" font-size="10" font-weight="900" fill="#FF8C3A" text-anchor="middle" opacity="0.9">R</text>
          <!-- REPARE-MOI text -->
          <text x="46" y="30" font-family="Montserrat,Arial Black,Arial" font-size="19.5" font-weight="900" fill="white" letter-spacing="0.3">REPARE-MOI</text>
          <!-- CI in orange -->
          <text x="188" y="30" font-family="Montserrat,Arial Black,Arial" font-size="19.5" font-weight="900" fill="#F07818" letter-spacing="0.3"> CI</text>
          <!-- Tagline -->
          <text x="46" y="44" font-family="Open Sans,Arial" font-size="10" fill="#94a3b8" letter-spacing="1.1">Réparez sans vous ruiner !</text>
          <!-- Accent bar -->
          <rect x="46" y="47.5" width="60" height="1.8" rx="0.9" fill="#F07818" opacity="0.55"/>
        </svg>
      </a>
      <form class="search-bar" action="<?= SITE_URL ?>/catalogue.php" method="get">
        <select name="marque">
          <option value="">Toutes</option>
          <?php foreach(['Samsung','iPhone','Huawei','Xiaomi','Motorola','LG','Nokia','Google Pixel','Oppo'] as $m): ?>
          <option value="<?= e($m) ?>" <?= ($_GET['marque']??'')===$m?'selected':'' ?>><?= e($m) ?></option>
          <?php endforeach; ?>
        </select>
        <input type="text" name="q" placeholder="Rechercher une pièce, un modèle..." value="<?= e($_GET['q']??'') ?>">
        <button type="submit"><i class="fas fa-search"></i></button>
      </form>
      <div class="header-actions">
        <a href="<?= SITE_URL ?>/panier.php" class="btn-cart">
          <i class="fas fa-shopping-cart"></i> Panier
          <span class="cart-count" id="cart-count"><?= $nbPanier ?></span>
        </a>
      </div>
    </div>
  </div>
</header>

<!-- NAV -->
<nav class="main-nav">
  <div class="container">
    <button class="nav-toggle" onclick="this.closest('nav').querySelector('.nav-menu').classList.toggle('open')" id="nav-toggle" aria-label="Menu">
      <i class="fas fa-bars"></i>
    </button>
    <ul class="nav-menu" id="nav-menu">
      <li><a href="<?= SITE_URL ?>/" class="<?= $currentPage==='index.php'?'active':'' ?>">Accueil</a></li>
      <li><a href="<?= SITE_URL ?>/boutique.php" class="<?= $currentPage==='boutique.php'?'active':'' ?>" style="color:var(--primary)"><i class="fas fa-store"></i> Boutique</a></li>
      <?php
      $marques = ['Samsung','iPhone','Huawei','Xiaomi','Motorola','LG','Nokia','Google Pixel','Oppo'];
      $cats_icons_nav = [
        'Écran'        => '🖥️',
        'Batterie'     => '🔋',
        'Connecteur'   => '🔌',
        'Vitre'        => '🔲',
        'Caméra'       => '📷',
        'Haut-parleur' => '🔊',
        'Microphone'   => '🎤',
        'Nappe'        => '🔧',
        'Chassis'      => '📦',
        'Autre'        => '🛠️',
      ];
      try {
        $pdo_nav = getPDO();
        $cats_bdd_nav = $pdo_nav->query("SELECT DISTINCT categorie FROM rm_produits WHERE actif=1 ORDER BY categorie")->fetchAll(PDO::FETCH_COLUMN);
      } catch(Exception $e) {
        $cats_bdd_nav = array_keys($cats_icons_nav);
      }
      foreach ($marques as $m): ?>
      <li>
        <a href="<?= SITE_URL ?>/catalogue.php?marque=<?= urlencode($m) ?>"><?= e($m) ?> <i class="fas fa-chevron-down"></i></a>
        <div class="dropdown">
          <a href="<?= SITE_URL ?>/catalogue.php?marque=<?= urlencode($m) ?>" style="font-weight:700;color:#F76B1C;border-bottom:1px solid #f0f0f0;padding-bottom:8px;margin-bottom:4px;display:block">
            <i class="fas fa-th-large"></i> Tout <?= e($m) ?>
          </a>
          <?php foreach ($cats_bdd_nav as $c):
            $ico = $cats_icons_nav[$c] ?? '🔧'; ?>
          <a href="<?= SITE_URL ?>/catalogue.php?marque=<?= urlencode($m) ?>&categorie=<?= urlencode($c) ?>">
            <?= $ico ?> <?= e($c) ?> <?= e($m) ?>
          </a>
          <?php endforeach; ?>
        </div>
      </li>
      <?php endforeach; ?>
      <li><a href="<?= SITE_URL ?>/catalogue.php?marque=Divers">Divers</a></li>
    </ul>
  </div>
</nav>
