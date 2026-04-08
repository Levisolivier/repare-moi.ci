<?php
require_once __DIR__ . '/includes/fonctions.php';
require_client();

$pdo    = getPDO();
$client = client();

// Mes commandes
$st = $pdo->prepare('SELECT * FROM rm_commandes WHERE client_id=? ORDER BY created_at DESC');
$st->execute([$client['id']]);
$commandes = $st->fetchAll();

$pageTitle = 'Mon Compte — Repare-Moi';
require_once __DIR__ . '/includes/header.php';

$statuts = [
    'en_attente' => ['label'=>'En attente',  'class'=>'warning'],
    'confirmee'  => ['label'=>'Confirmée',   'class'=>'info'],
    'en_cours'   => ['label'=>'En cours',    'class'=>'primary'],
    'livree'     => ['label'=>'Livrée',      'class'=>'success'],
    'annulee'    => ['label'=>'Annulée',     'class'=>'danger'],
];
?>
<div class="container">
  <div class="breadcrumb">
    <a href="<?= SITE_URL ?>/">Accueil</a> <i class="fas fa-chevron-right"></i> <span>Mon Compte</span>
  </div>

  <div class="compte-layout">
    <!-- SIDEBAR -->
    <aside class="compte-sidebar">
      <div class="compte-avatar"><i class="fas fa-user-circle"></i></div>
      <div class="compte-nom"><?= e($client['prenom']) ?> <?= e($client['nom']) ?></div>
      <div class="compte-email"><?= e($client['email']) ?></div>
      <ul class="compte-menu">
        <li><a href="#commandes" class="active"><i class="fas fa-shopping-bag"></i> Mes commandes</a></li>
        <li><a href="<?= SITE_URL ?>/catalogue.php"><i class="fas fa-store"></i> Catalogue</a></li>
        <li><a href="<?= SITE_URL ?>/panier.php"><i class="fas fa-shopping-cart"></i> Mon panier</a></li>
        <li><a href="<?= SITE_URL ?>/deconnexion.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
      </ul>
    </aside>

    <!-- CONTENU -->
    <main class="compte-main">
      <h2 class="section-title" id="commandes">Mes commandes</h2>

      <?php if (!$commandes): ?>
      <div class="empty-state" style="padding:40px 0">
        <i class="fas fa-shopping-bag"></i>
        <p>Vous n'avez pas encore passé de commande.</p>
        <a href="<?= SITE_URL ?>/catalogue.php" class="btn-primary" style="display:inline-block;margin-top:12px">Voir le catalogue</a>
      </div>
      <?php else: ?>
      <div class="table-wrap">
        <table class="table">
          <thead>
            <tr><th>Référence</th><th>Date</th><th>Total</th><th>Statut</th><th>Détails</th></tr>
          </thead>
          <tbody>
            <?php foreach ($commandes as $cmd):
              $s = $statuts[$cmd['statut']] ?? ['label'=>$cmd['statut'],'class'=>'secondary'];
            ?>
            <tr>
              <td><strong><?= e($cmd['reference']) ?></strong></td>
              <td><?= date('d/m/Y H:i', strtotime($cmd['created_at'])) ?></td>
              <td><?= prix((int)$cmd['total']) ?></td>
              <td><span class="badge badge-<?= $s['class'] ?>"><?= $s['label'] ?></span></td>
              <td>
                <?php
                // Récupérer les articles de la commande
                $items_st = $pdo->prepare('SELECT * FROM rm_commandes_items WHERE commande_id=?');
                $items_st->execute([$cmd['id']]);
                $items = $items_st->fetchAll();
                ?>
                <details>
                  <summary>Voir (<?= count($items) ?> article<?= count($items)>1?'s':'' ?>)</summary>
                  <ul style="margin-top:8px;font-size:12px">
                    <?php foreach ($items as $it): ?>
                    <li><?= e($it['nom_produit']) ?> ×<?= $it['quantite'] ?> — <?= prix((int)$it['sous_total']) ?></li>
                    <?php endforeach; ?>
                  </ul>
                </details>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </main>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
