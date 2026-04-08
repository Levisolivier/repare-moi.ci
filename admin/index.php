<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_admin();

$pdo = getPDO();

// Stats
$nb_produits   = (int)$pdo->query("SELECT COUNT(*) FROM rm_produits WHERE actif=1")->fetchColumn();
$nb_ruptures   = (int)$pdo->query("SELECT COUNT(*) FROM rm_produits WHERE actif=1 AND stock=0")->fetchColumn();
$nb_commandes  = (int)$pdo->query("SELECT COUNT(*) FROM rm_commandes")->fetchColumn();
$ca_total      = (int)$pdo->query("SELECT COALESCE(SUM(total),0) FROM rm_commandes WHERE statut='livree'")->fetchColumn();
$nb_clients    = (int)$pdo->query("SELECT COUNT(*) FROM rm_clients WHERE actif=1")->fetchColumn();
$en_attente    = (int)$pdo->query("SELECT COUNT(*) FROM rm_commandes WHERE statut='en_attente'")->fetchColumn();

// Dernières commandes
$commandes_recentes = $pdo->query("SELECT * FROM rm_commandes ORDER BY created_at DESC LIMIT 8")->fetchAll();
// Produits faible stock
$faible_stock = $pdo->query("SELECT * FROM rm_produits WHERE actif=1 AND stock > 0 AND stock <= 3 ORDER BY stock ASC LIMIT 6")->fetchAll();

$statuts = ['en_attente'=>'warning','confirmee'=>'info','en_cours'=>'primary','livree'=>'success','annulee'=>'danger'];
$statuts_labels = ['en_attente'=>'En attente','confirmee'=>'Confirmée','en_cours'=>'En cours','livree'=>'Livrée','annulee'=>'Annulée'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard Admin — Repare-Moi</title>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin.css">
</head>
<body class="admin-body">

<!-- SIDEBAR -->
<aside class="admin-sidebar">
  <div class="admin-logo">
    <span>Repare<em>Moi</em></span>
    <small>Administration</small>
  </div>
  <nav class="admin-nav">
    <a href="<?= SITE_URL ?>/admin/" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="<?= SITE_URL ?>/admin/produits.php"><i class="fas fa-box"></i> Produits <span class="badge-nav"><?= $nb_produits ?></span></a>
    <a href="<?= SITE_URL ?>/admin/sync_sheets.php"><i class="fas fa-sync-alt"></i> Sync Sheets</a>
    <a href="<?= SITE_URL ?>/admin/commandes.php"><i class="fas fa-shopping-bag"></i> Commandes <?= $en_attente>0?"<span class='badge-nav badge-alert'>$en_attente</span>":'' ?></a>
    <a href="<?= SITE_URL ?>/admin/clients.php"><i class="fas fa-users"></i> Clients</a>
    <hr>
    <a href="<?= SITE_URL ?>/" target="_blank"><i class="fas fa-external-link-alt"></i> Voir le site</a>
    <a href="<?= SITE_URL ?>/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
  </nav>
</aside>

<!-- CONTENU -->
<main class="admin-main">
  <div class="admin-topbar">
    <h1>Dashboard</h1>
    <div class="admin-user"><i class="fas fa-user-circle"></i> <?= e($_SESSION['admin_nom']) ?></div>
  </div>

  <!-- STATS -->
  <div class="stats-grid">
    <div class="stat-card"><div class="stat-icon blue"><i class="fas fa-box"></i></div><div class="stat-body"><div class="stat-num"><?= $nb_produits ?></div><div class="stat-lbl">Produits actifs</div></div></div>
    <div class="stat-card <?= $nb_ruptures>0?'alert':'' ?>"><div class="stat-icon red"><i class="fas fa-exclamation-triangle"></i></div><div class="stat-body"><div class="stat-num"><?= $nb_ruptures ?></div><div class="stat-lbl">Ruptures de stock</div></div></div>
    <div class="stat-card <?= $en_attente>0?'alert':'' ?>"><div class="stat-icon orange"><i class="fas fa-clock"></i></div><div class="stat-body"><div class="stat-num"><?= $en_attente ?></div><div class="stat-lbl">Commandes en attente</div></div></div>
    <div class="stat-card"><div class="stat-icon green"><i class="fas fa-chart-line"></i></div><div class="stat-body"><div class="stat-num"><?= prix($ca_total) ?></div><div class="stat-lbl">CA livré</div></div></div>
    <div class="stat-card"><div class="stat-icon purple"><i class="fas fa-users"></i></div><div class="stat-body"><div class="stat-num"><?= $nb_clients ?></div><div class="stat-lbl">Clients</div></div></div>
    <div class="stat-card"><div class="stat-icon gray"><i class="fas fa-shopping-bag"></i></div><div class="stat-body"><div class="stat-num"><?= $nb_commandes ?></div><div class="stat-lbl">Total commandes</div></div></div>
  </div>

  <div class="admin-grid-2">
    <!-- COMMANDES RÉCENTES -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h2>Commandes récentes</h2>
        <a href="commandes.php" class="admin-link">Voir tout →</a>
      </div>
      <table class="admin-table">
        <thead><tr><th>Référence</th><th>Client</th><th>Total</th><th>Statut</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($commandes_recentes as $c): ?>
        <tr>
          <td><strong><?= e($c['reference']) ?></strong></td>
          <td><?= e($c['client_nom']) ?></td>
          <td><?= prix((int)$c['total']) ?></td>
          <td><span class="badge badge-<?= $statuts[$c['statut']]??'secondary' ?>"><?= $statuts_labels[$c['statut']]??$c['statut'] ?></span></td>
          <td><a href="commandes.php?id=<?= $c['id'] ?>" class="btn-xs">Gérer</a></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- STOCK FAIBLE -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h2>⚠️ Stock faible (&le; 3)</h2>
        <a href="produits.php?filtre=faible" class="admin-link">Voir tout →</a>
      </div>
      <?php if (!$faible_stock): ?>
      <p style="color:#888;padding:16px">Aucun produit en stock faible 🎉</p>
      <?php else: ?>
      <table class="admin-table">
        <thead><tr><th>Produit</th><th>Marque</th><th>Stock</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($faible_stock as $p): ?>
        <tr>
          <td><?= e($p['nom']) ?></td>
          <td><?= e($p['marque']) ?></td>
          <td><span class="badge badge-warning"><?= $p['stock'] ?></span></td>
          <td><a href="produits.php?edit=<?= $p['id'] ?>" class="btn-xs">Modifier</a></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </div>

  <!-- ACTIONS RAPIDES -->
  <div class="admin-card" style="margin-top:20px">
    <div class="admin-card-header"><h2>Actions rapides</h2></div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;padding:16px">
      <a href="produits.php?action=nouveau" class="btn-admin-primary"><i class="fas fa-plus"></i> Nouveau produit</a>
      <a href="produits.php?filtre=rupture" class="btn-admin-danger"><i class="fas fa-exclamation"></i> Voir ruptures (<?= $nb_ruptures ?>)</a>
      <a href="commandes.php?statut=en_attente" class="btn-admin-warning"><i class="fas fa-clock"></i> Commandes en attente (<?= $en_attente ?>)</a>
    </div>
  </div>
</main>

</body>
</html>
