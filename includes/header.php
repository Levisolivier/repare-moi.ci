<?php
require_once __DIR__ . '/../includes/fonctions.php';
$flash  = flash_get();
$nbPanier = panier_count();
$currentPage = basename($_SERVER['PHP_SELF']);
$canonical = SITE_URL . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? SITE_NOM, ENT_QUOTES, 'UTF-8') ?> — Pièces smartphones CI</title>
<meta name="description" content="<?= htmlspecialchars($pageDesc ?? 'Pièces détachées smartphones pas chers en Côte d\'Ivoire. Samsung, iPhone, Huawei, Xiaomi. Livraison Abidjan.', ENT_QUOTES, 'UTF-8') ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<!-- Open Graph -->
<meta property="og:type"        content="website">
<meta property="og:site_name"   content="<?= e(SITE_NOM) ?>">
<meta property="og:title"       content="<?= htmlspecialchars($pageTitle ?? SITE_NOM, ENT_QUOTES, 'UTF-8') ?>">
<meta property="og:description" content="<?= htmlspecialchars($pageDesc ?? 'Pièces détachées smartphones en Côte d\'Ivoire.', ENT_QUOTES, 'UTF-8') ?>">
<meta property="og:url"         content="<?= e($canonical) ?>">
<!-- Preconnect & DNS prefetch -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
<link rel="dns-prefetch" href="https://www.googletagmanager.com">
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

<!-- Skip navigation for keyboard/screen-reader users -->
<a class="skip-link" href="#main-content">Aller au contenu principal</a>

<?php if ($flash): ?>
<div class="flash flash-<?= e($flash['type']) ?>" id="flash-msg" role="alert" aria-live="polite">
  <i class="fas fa-<?= $flash['type']==='success' ? 'check-circle' : ($flash['type']==='warning' ? 'exclamation-triangle' : 'exclamation-circle') ?>" aria-hidden="true"></i>
  <?= e($flash['msg']) ?>
  <button onclick="this.parentElement.remove()" aria-label="Fermer le message">×</button>
</div>
<?php endif; ?>

<!-- TOPBAR -->
<div class="topbar">
  <div class="container">
    <div class="topbar-inner">
      <div class="topbar-left">
        <a href="tel:<?= SITE_TEL1 ?>"><i class="fas fa-phone" aria-hidden="true"></i><?= SITE_TEL1 ?></a>
        <a href="tel:<?= SITE_TEL2 ?>"><i class="fas fa-phone" aria-hidden="true"></i><?= SITE_TEL2 ?></a>
        <a href="mailto:<?= SITE_EMAIL ?>"><i class="fas fa-envelope" aria-hidden="true"></i><?= SITE_EMAIL ?></a>
      </div>
      <div class="topbar-right">
        <?php if (client_connecte()): $cl = client(); ?>
          <a href="<?= SITE_URL ?>/compte.php"><i class="fas fa-user" aria-hidden="true"></i> <?= e($cl['prenom']) ?></a>
          <a href="<?= SITE_URL ?>/deconnexion.php"><i class="fas fa-sign-out-alt" aria-hidden="true"></i> Déconnexion</a>
        <?php else: ?>
          <a href="<?= SITE_URL ?>/connexion.php"><i class="fas fa-sign-in-alt" aria-hidden="true"></i> Connexion</a>
          <a href="<?= SITE_URL ?>/inscription.php"><i class="fas fa-user-plus" aria-hidden="true"></i> Créer un compte</a>
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
        <svg class="logo-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 220 52" width="200" height="48" aria-hidden="true">
          <defs>
            <linearGradient id="lgrd" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="#FF8C3A"/>
              <stop offset="100%" stop-color="#E86808"/>
            </linearGradient>
          </defs>
          <rect x="6" y="4" width="29" height="44" rx="5" fill="url(#lgrd)" filter="drop-shadow(0 3px 6px rgba(240,120,24,0.35))"/>
          <rect x="9" y="10" width="23" height="29" rx="2" fill="#0f172a" opacity="0.88"/>
          <rect x="9" y="10" width="10" height="29" rx="2" fill="white" opacity="0.04"/>
          <rect x="15" y="6" width="10" height="2.5" rx="1.2" fill="white" opacity="0.4"/>
          <circle cx="20" cy="41.5" r="3" fill="white" opacity="0.22"/>
          <circle cx="20" cy="41.5" r="1.6" fill="white" opacity="0.38"/>
          <rect x="12" y="16" width="17" height="2.5" rx="1" fill="#FF8C3A" opacity="0.85"/>
          <rect x="12" y="21" width="12" height="1.5" rx="0.75" fill="white" opacity="0.28"/>
          <rect x="12" y="24.5" width="15" height="1.5" rx="0.75" fill="white" opacity="0.22"/>
          <rect x="12" y="28" width="9" height="1.5" rx="0.75" fill="white" opacity="0.18"/>
          <text x="20" y="34" font-family="Arial Black,Arial" font-size="10" font-weight="900" fill="#FF8C3A" text-anchor="middle" opacity="0.9">R</text>
          <text x="46" y="30" font-family="Montserrat,Arial Black,Arial" font-size="19.5" font-weight="900" fill="white" letter-spacing="0.3">REPARE-MOI</text>
          <text x="188" y="30" font-family="Montserrat,Arial Black,Arial" font-size="19.5" font-weight="900" fill="#F07818" letter-spacing="0.3"> CI</text>
          <text x="46" y="44" font-family="Open Sans,Arial" font-size="10" fill="#94a3b8" letter-spacing="1.1">Réparez sans vous ruiner !</text>
          <rect x="46" y="47.5" width="60" height="1.8" rx="0.9" fill="#F07818" opacity="0.55"/>
        </svg>
      </a>
      <form class="search-bar" action="<?= SITE_URL ?>/catalogue.php" method="get" role="search" aria-label="Rechercher une pièce">
        <select name="marque" aria-label="Filtrer par marque">
          <option value="">Toutes</option>
          <?php foreach(['Samsung','iPhone','Huawei','Xiaomi','Motorola','LG','Nokia','Google Pixel','Oppo'] as $m): ?>
          <option value="<?= e($m) ?>" <?= ($_GET['marque']??'')===$m?'selected':'' ?>><?= e($m) ?></option>
          <?php endforeach; ?>
        </select>
        <input type="text" name="q" placeholder="Rechercher une pièce, un modèle..." value="<?= e($_GET['q']??'') ?>" aria-label="Terme de recherche">
        <button type="submit" aria-label="Lancer la recherche"><i class="fas fa-search" aria-hidden="true"></i></button>
      </form>
      <div class="header-actions">
        <a href="<?= SITE_URL ?>/panier.php" class="btn-cart" aria-label="Panier (<?= $nbPanier ?> article<?= $nbPanier > 1 ? 's' : '' ?>)">
          <i class="fas fa-shopping-cart" aria-hidden="true"></i> <span class="cart-label">Panier</span>
          <span class="cart-count" id="cart-count" aria-hidden="true"><?= $nbPanier ?></span>
        </a>
      </div>
    </div>
  </div>
</header>

<!-- NAV -->
<nav class="main-nav" aria-label="Navigation principale">
  <div class="container">
    <button class="nav-toggle" id="nav-toggle" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="nav-menu">
      <i class="fas fa-bars" aria-hidden="true"></i>
    </button>
    <ul class="nav-menu" id="nav-menu" role="list">
      <li><a href="<?= SITE_URL ?>/" class="<?= $currentPage==='index.php'?'active':'' ?>">Accueil</a></li>
      <li><a href="<?= SITE_URL ?>/boutique.php" class="<?= $currentPage==='boutique.php'?'active':'' ?>" style="color:var(--primary)"><i class="fas fa-store" aria-hidden="true"></i> Boutique</a></li>
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
        <a href="<?= SITE_URL ?>/catalogue.php?marque=<?= urlencode($m) ?>" aria-haspopup="true"><?= e($m) ?> <i class="fas fa-chevron-down" aria-hidden="true"></i></a>
        <div class="dropdown" role="region" aria-label="Catégories <?= e($m) ?>">
          <a href="<?= SITE_URL ?>/catalogue.php?marque=<?= urlencode($m) ?>" style="font-weight:700;color:#F76B1C;border-bottom:1px solid #f0f0f0;padding-bottom:8px;margin-bottom:4px;display:block">
            <i class="fas fa-th-large" aria-hidden="true"></i> Tout <?= e($m) ?>
          </a>
          <?php foreach ($cats_bdd_nav as $c):
            $ico = $cats_icons_nav[$c] ?? '🔧'; ?>
          <a href="<?= SITE_URL ?>/catalogue.php?marque=<?= urlencode($m) ?>&categorie=<?= urlencode($c) ?>">
            <span aria-hidden="true"><?= $ico ?></span> <?= e($c) ?> <?= e($m) ?>
          </a>
          <?php endforeach; ?>
        </div>
      </li>
      <?php endforeach; ?>
      <li><a href="<?= SITE_URL ?>/catalogue.php?marque=Divers">Divers</a></li>
    </ul>
  </div>
</nav>

<main id="main-content">
