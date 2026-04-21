<?php
// Sidebar partagée pour toutes les pages admin
$_sb_pdo          = getPDO();
$_sb_nb_produits  = (int)$_sb_pdo->query("SELECT COUNT(*) FROM rm_produits WHERE actif=1")->fetchColumn();
$_sb_en_attente   = (int)$_sb_pdo->query("SELECT COUNT(*) FROM rm_commandes WHERE statut='en_attente'")->fetchColumn();
$_sb_nb_clients   = (int)$_sb_pdo->query("SELECT COUNT(*) FROM rm_clients")->fetchColumn();
$_sb_current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
  <div class="sidebar-brand">
    <span>Repare<em>Moi</em></span>
    <small>Administration</small>
  </div>
  <nav class="admin-nav">
    <a href="<?= SITE_URL ?>/admin/" class="<?= $_sb_current_page==='index.php'?'active':'' ?>">
      <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
    <a href="<?= SITE_URL ?>/admin/produits.php" class="<?= $_sb_current_page==='produits.php'?'active':'' ?>">
      <i class="fas fa-box"></i> Produits
      <span class="badge-nav"><?= $_sb_nb_produits ?></span>
    </a>
    <a href="<?= SITE_URL ?>/admin/sync_sheets.php" class="<?= $_sb_current_page==='sync_sheets.php'?'active':'' ?>">
      <i class="fas fa-sync-alt"></i> Sync Sheets
    </a>
    <a href="<?= SITE_URL ?>/admin/commandes.php" class="<?= $_sb_current_page==='commandes.php'?'active':'' ?>">
      <i class="fas fa-shopping-bag"></i> Commandes
      <?= $_sb_en_attente>0?"<span class='badge-nav badge-alert'>$_sb_en_attente</span>":'' ?>
    </a>
    <a href="<?= SITE_URL ?>/admin/clients.php" class="<?= $_sb_current_page==='clients.php'?'active':'' ?>">
      <i class="fas fa-users"></i> Clients
      <span class="badge-nav"><?= $_sb_nb_clients ?></span>
    </a>
  </nav>
  <div class="sidebar-footer">
    <a href="<?= SITE_URL ?>" target="_blank"><i class="fas fa-external-link-alt"></i> Voir le site</a>
    <a href="<?= SITE_URL ?>/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
  </div>
</aside>
