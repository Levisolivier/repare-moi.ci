<?php
// Sidebar partagée pour toutes les pages admin
$nb_produits  = (int)getPDO()->query("SELECT COUNT(*) FROM rm_produits WHERE actif=1")->fetchColumn();
$en_attente   = (int)getPDO()->query("SELECT COUNT(*) FROM rm_commandes WHERE statut='en_attente'")->fetchColumn();
$nb_clients   = (int)getPDO()->query("SELECT COUNT(*) FROM rm_clients")->fetchColumn();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
  <div class="sidebar-brand">
    <span>Repare<em>Moi</em></span>
    <small>Administration</small>
  </div>
  <nav class="admin-nav">
    <a href="<?= SITE_URL ?>/admin/" class="<?= $current_page==='index.php'?'active':'' ?>">
      <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
    <a href="<?= SITE_URL ?>/admin/produits.php" class="<?= $current_page==='produits.php'?'active':'' ?>">
      <i class="fas fa-box"></i> Produits
      <span class="badge-nav"><?= $nb_produits ?></span>
    </a>
    <a href="<?= SITE_URL ?>/admin/sync_sheets.php" class="<?= $current_page==='sync_sheets.php'?'active':'' ?>">
      <i class="fas fa-sync-alt"></i> Sync Sheets
    </a>
    <a href="<?= SITE_URL ?>/admin/commandes.php" class="<?= $current_page==='commandes.php'?'active':'' ?>">
      <i class="fas fa-shopping-bag"></i> Commandes
      <?= $en_attente>0?"<span class='badge-nav badge-alert'>$en_attente</span>":'' ?>
    </a>
    <a href="<?= SITE_URL ?>/admin/clients.php" class="<?= $current_page==='clients.php'?'active':'' ?>">
      <i class="fas fa-users"></i> Clients
      <span class="badge-nav"><?= $nb_clients ?></span>
    </a>
  </nav>
  <div class="sidebar-footer">
    <a href="<?= SITE_URL ?>" target="_blank"><i class="fas fa-external-link-alt"></i> Voir le site</a>
    <a href="<?= SITE_URL ?>/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
  </div>
</aside>
