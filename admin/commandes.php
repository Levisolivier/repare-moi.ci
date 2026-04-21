<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_admin();
$pdo = getPDO();

// Mise à jour statut
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if ($_POST['action'] === 'update_statut') {
        $statuts_valides = ['en_attente','confirmee','en_cours','livree','annulee'];
        $id     = (int)$_POST['commande_id'];
        $statut = $_POST['statut'] ?? '';
        if (in_array($statut, $statuts_valides)) {
            $pdo->prepare('UPDATE rm_commandes SET statut=? WHERE id=?')->execute([$statut, $id]);
            flash('Statut mis à jour.');
        }
        header('Location: ' . SITE_URL . '/admin/commandes.php?id='.$id);
        exit;
    }
}

// Voir détail commande
$detail_id = (int)($_GET['id'] ?? 0);
$detail = null;
$items  = [];
if ($detail_id) {
    $st_detail = $pdo->prepare('SELECT * FROM rm_commandes WHERE id=?');
    $st_detail->execute([$detail_id]);
    $detail = $st_detail->fetch();
    if ($detail) {
        $st_items = $pdo->prepare('SELECT * FROM rm_commandes_items WHERE commande_id=?');
        $st_items->execute([$detail_id]);
        $items = $st_items->fetchAll();
    }
}

// Liste
$statuts_valides_filtre = ['en_attente','confirmee','en_cours','livree','annulee'];
$statut_filtre = $_GET['statut'] ?? '';
if ($statut_filtre && !in_array($statut_filtre, $statuts_valides_filtre)) $statut_filtre = '';
if ($statut_filtre) {
    $st_list = $pdo->prepare('SELECT * FROM rm_commandes WHERE statut=? ORDER BY created_at DESC');
    $st_list->execute([$statut_filtre]);
} else {
    $st_list = $pdo->query('SELECT * FROM rm_commandes ORDER BY created_at DESC');
}
$commandes = $st_list->fetchAll();

$statuts = ['en_attente'=>['warning','En attente'],'confirmee'=>['info','Confirmée'],'en_cours'=>['primary','En cours'],'livree'=>['success','Livrée'],'annulee'=>['danger','Annulée']];
$paiements = ['orange_money'=>'Orange Money','mtn_money'=>'MTN Money','wave'=>'Wave','moov'=>'Moov Money','especes'=>'Livraison'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Commandes Admin — Repare-Moi</title>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin.css">
</head>
<body class="admin-body">

<aside class="admin-sidebar">
  <div class="admin-logo"><span>Repare<em>Moi</em></span><small>Admin</small></div>
  <nav class="admin-nav">
    <a href="<?= SITE_URL ?>/admin/"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="<?= SITE_URL ?>/admin/produits.php"><i class="fas fa-box"></i> Produits</a>
    <a href="<?= SITE_URL ?>/admin/sync_sheets.php"><i class="fas fa-sync-alt"></i> Sync Sheets</a>
    <a href="<?= SITE_URL ?>/admin/commandes.php" class="active"><i class="fas fa-shopping-bag"></i> Commandes</a>
    <a href="<?= SITE_URL ?>/admin/clients.php"><i class="fas fa-users"></i> Clients</a>
    <hr>
    <a href="<?= SITE_URL ?>/" target="_blank"><i class="fas fa-external-link-alt"></i> Voir le site</a>
    <a href="<?= SITE_URL ?>/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
  </nav>
</aside>

<main class="admin-main">
  <div class="admin-topbar"><h1>Commandes</h1></div>

  <?php $fmsg = flash_get(); if($fmsg): ?>
  <div class="alert alert-<?= $fmsg['type'] ?>"><?= e($fmsg['msg']) ?></div>
  <?php endif; ?>

  <!-- FILTRES PAR STATUT -->
  <div class="admin-tabs">
    <a href="commandes.php" class="<?= !$statut_filtre?'active':'' ?>">Toutes</a>
    <?php foreach ($statuts as $k=>[$cls,$lbl]): ?>
    <a href="?statut=<?= $k ?>" class="<?= $statut_filtre===$k?'active':'' ?>"><?= $lbl ?></a>
    <?php endforeach; ?>
  </div>

  <?php if ($detail): ?>
  <!-- DÉTAIL COMMANDE -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h2>Commande <?= e($detail['reference']) ?></h2>
      <a href="commandes.php" class="btn-admin-outline">← Retour</a>
    </div>
    <div class="detail-grid">
      <div>
        <h3 style="margin-bottom:12px">Client</h3>
        <p><strong><?= e($detail['client_nom']) ?></strong></p>
        <p>📧 <?= e($detail['client_email']) ?></p>
        <p>📞 <?= e($detail['client_tel']) ?></p>
        <p>📍 <?= e($detail['adresse']) ?>, <?= e($detail['ville']) ?></p>
        <p>💳 <?= e($paiements[$detail['paiement']] ?? $detail['paiement']) ?></p>
        <?php if($detail['notes']): ?><p>📝 <?= e($detail['notes']) ?></p><?php endif; ?>
      </div>
      <div>
        <h3 style="margin-bottom:12px">Statut</h3>
        <form method="post">
          <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
          <input type="hidden" name="action" value="update_statut">
          <input type="hidden" name="commande_id" value="<?= $detail['id'] ?>">
          <select name="statut" style="padding:8px;border:1px solid #ddd;border-radius:6px;font-size:14px;margin-bottom:10px">
            <?php foreach ($statuts as $k=>[$cls,$lbl]): ?>
            <option value="<?= $k ?>" <?= $detail['statut']===$k?'selected':'' ?>><?= $lbl ?></option>
            <?php endforeach; ?>
          </select><br>
          <button type="submit" class="btn-admin-primary">Mettre à jour</button>
        </form>
        <p style="margin-top:12px;font-size:13px;color:#888">
          Date : <?= date('d/m/Y H:i', strtotime($detail['created_at'])) ?>
        </p>
      </div>
    </div>
    <h3 style="margin:20px 0 12px">Articles commandés</h3>
    <table class="admin-table">
      <thead><tr><th>Produit</th><th>Prix unitaire</th><th>Quantité</th><th>Sous-total</th></tr></thead>
      <tbody>
      <?php foreach ($items as $it): ?>
      <tr>
        <td><?= e($it['nom_produit']) ?></td>
        <td><?= prix((int)$it['prix_unitaire']) ?></td>
        <td><?= $it['quantite'] ?></td>
        <td><?= prix((int)$it['sous_total']) ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr><td colspan="3"><strong>Total</strong></td><td><strong><?= prix((int)$detail['total']) ?></strong></td></tr>
      </tfoot>
    </table>
    <div style="margin-top:16px;display:flex;gap:10px">
      <a href="https://wa.me/<?= preg_replace('/[^0-9]/','',$detail['client_tel']) ?>?text=Bonjour+<?= urlencode($detail['client_nom']) ?>,+votre+commande+<?= urlencode($detail['reference']) ?>+est+en+cours."
         class="btn-admin-primary" target="_blank"><i class="fab fa-whatsapp"></i> Contacter par WhatsApp</a>
      <a href="mailto:<?= e($detail['client_email']) ?>?subject=Commande+<?= urlencode($detail['reference']) ?>"
         class="btn-admin-outline"><i class="fas fa-envelope"></i> Email</a>
    </div>
  </div>

  <?php else: ?>
  <!-- LISTE COMMANDES -->
  <div class="admin-card">
    <table class="admin-table">
      <thead><tr><th>Référence</th><th>Client</th><th>Téléphone</th><th>Total</th><th>Paiement</th><th>Statut</th><th>Date</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($commandes as $c): [$cls,$lbl] = $statuts[$c['statut']]??['secondary',$c['statut']]; ?>
      <tr>
        <td><strong><?= e($c['reference']) ?></strong></td>
        <td><?= e($c['client_nom']) ?></td>
        <td><a href="tel:<?= e($c['client_tel']) ?>"><?= e($c['client_tel']) ?></a></td>
        <td><?= prix((int)$c['total']) ?></td>
        <td><?= $paiements[$c['paiement']]??$c['paiement'] ?></td>
        <td><span class="badge badge-<?= $cls ?>"><?= $lbl ?></span></td>
        <td><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></td>
        <td><a href="?id=<?= $c['id'] ?>" class="btn-xs"><i class="fas fa-eye"></i> Voir</a></td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$commandes): ?><tr><td colspan="8" style="text-align:center;color:#888;padding:30px">Aucune commande.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</main>
</body>
</html>
