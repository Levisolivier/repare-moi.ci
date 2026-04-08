<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_admin();
$pdo = getPDO();

$msg = '';

// ── ACTIONS POST ─────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';

    // Sauvegarder produit (nouveau ou édition)
    if (in_array($action, ['creer','modifier'])) {
        $data = [
            'nom'         => trim($_POST['nom'] ?? ''),
            'marque'      => trim($_POST['marque'] ?? ''),
            'categorie'   => trim($_POST['categorie'] ?? ''),
            'serie'       => trim($_POST['serie'] ?? ''),
            'prix'        => (int)($_POST['prix'] ?? 0),
            'stock'       => (int)($_POST['stock'] ?? 0),
            'description' => trim($_POST['description'] ?? ''),
            'deal'        => isset($_POST['deal']) ? 1 : 0,
            'actif'       => isset($_POST['actif']) ? 1 : 0,
        ];
        $data['slug'] = slug($data['nom']);

        // Gestion image
        if (!empty($_FILES['image']['tmp_name'])) {
            $fname = upload_image($_FILES['image'], 'prod');
            if ($fname) $data['image'] = $fname;
        }
        if (!empty($_POST['image_url'])) {
            $data['image'] = trim($_POST['image_url']);
        }

        if ($action === 'creer') {
            // Rendre le slug unique
            $base = $data['slug']; $i = 0;
            while ($pdo->prepare('SELECT id FROM rm_produits WHERE slug=?')->execute([$data['slug']]) &&
                   $pdo->query('SELECT id FROM rm_produits WHERE slug="'.$data['slug'].'"')->fetch()) {
                $data['slug'] = $base . '-' . (++$i);
            }
            $st = $pdo->prepare('INSERT INTO rm_produits (nom,slug,marque,categorie,serie,prix,stock,description,deal,actif,image) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
            $st->execute([$data['nom'],$data['slug'],$data['marque'],$data['categorie'],$data['serie'],
                          $data['prix'],$data['stock'],$data['description'],$data['deal'],$data['actif'],$data['image']??'']);
            $msg = 'success:Produit créé avec succès.';
        } else {
            $id = (int)($_POST['produit_id'] ?? 0);
            $sets = 'nom=?,marque=?,categorie=?,serie=?,prix=?,stock=?,description=?,deal=?,actif=?';
            $vals = [$data['nom'],$data['marque'],$data['categorie'],$data['serie'],
                     $data['prix'],$data['stock'],$data['description'],$data['deal'],$data['actif']];
            if (!empty($data['image'])) { $sets .= ',image=?'; $vals[] = $data['image']; }
            $vals[] = $id;
            $pdo->prepare("UPDATE rm_produits SET $sets WHERE id=?")->execute($vals);
            $msg = 'success:Produit modifié avec succès.';
        }
    }

    // Mise à jour rapide du stock
    if ($action === 'update_stock') {
        $id    = (int)($_POST['produit_id'] ?? 0);
        $stock = max(0, (int)($_POST['stock'] ?? 0));
        $pdo->prepare('UPDATE rm_produits SET stock=? WHERE id=?')->execute([$stock, $id]);
        header('Content-Type: application/json');
        echo json_encode(['ok'=>true,'stock'=>$stock]);
        exit;
    }

    // Supprimer (désactiver)
    if ($action === 'supprimer') {
        $pdo->prepare('UPDATE rm_produits SET actif=0 WHERE id=?')->execute([(int)$_POST['produit_id']]);
        $msg = 'success:Produit désactivé.';
    }

    // Redirection pour éviter re-soumission
    if ($msg) {
        [$type,$text] = explode(':', $msg, 2);
        flash($text, $type);
        header('Location: ' . SITE_URL . '/admin/produits.php');
        exit;
    }
}

// ── LECTURE ─────────────────────────────────────────────
$edit_id = (int)($_GET['edit'] ?? 0);
$edit    = $edit_id ? $pdo->query("SELECT * FROM rm_produits WHERE id=$edit_id")->fetch() : null;
$nouveau = isset($_GET['action']) && $_GET['action']==='nouveau';

$filtre  = $_GET['filtre'] ?? '';
$q_admin = trim($_GET['q'] ?? '');
$where   = ['1=1'];
$bind    = [];
if ($filtre === 'rupture') { $where[] = 'stock=0'; }
if ($filtre === 'faible')  { $where[] = 'stock>0 AND stock<=3'; }
if ($filtre === 'inactif') { $where[] = 'actif=0'; } else { $where[] = 'actif=1'; }
if ($q_admin) { $where[] = '(nom LIKE ? OR marque LIKE ?)'; $bind[] = "%$q_admin%"; $bind[] = "%$q_admin%"; }

$st = $pdo->prepare('SELECT * FROM rm_produits WHERE ' . implode(' AND ', $where) . ' ORDER BY stock ASC, updated_at DESC');
$st->execute($bind);
$produits = $st->fetchAll();

$marques_opts  = ['Samsung','iPhone','Huawei','Xiaomi','Motorola','LG','Nokia','Google Pixel','Oppo','Divers'];
$cats_opts     = ['Écrans','Batteries','Anti-choc','Tablettes','Divers'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Produits Admin — Repare-Moi</title>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin.css">
</head>
<body class="admin-body">

<aside class="admin-sidebar">
  <div class="admin-logo"><span>Repare<em>Moi</em></span><small>Admin</small></div>
  <nav class="admin-nav">
    <a href="<?= SITE_URL ?>/admin/"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="<?= SITE_URL ?>/admin/produits.php" class="active"><i class="fas fa-box"></i> Produits</a>
    <a href="<?= SITE_URL ?>/admin/sync_sheets.php"><i class="fas fa-sync-alt"></i> Sync Sheets</a>
    <a href="<?= SITE_URL ?>/admin/commandes.php"><i class="fas fa-shopping-bag"></i> Commandes</a>
    <hr>
    <a href="<?= SITE_URL ?>/" target="_blank"><i class="fas fa-external-link-alt"></i> Voir le site</a>
    <a href="<?= SITE_URL ?>/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
  </nav>
</aside>

<main class="admin-main">
  <div class="admin-topbar">
    <h1><?= $nouveau ? 'Nouveau produit' : ($edit ? 'Modifier : '.e($edit['nom']) : 'Gestion des produits') ?></h1>
    <?php if (!$nouveau && !$edit): ?>
    <a href="?action=nouveau" class="btn-admin-primary"><i class="fas fa-plus"></i> Nouveau produit</a>
    <?php endif; ?>
  </div>

  <?php
  $flash_admin = flash_get();
  if ($flash_admin): ?>
  <div class="alert alert-<?= $flash_admin['type'] ?>"><?= e($flash_admin['msg']) ?></div>
  <?php endif; ?>

  <?php if ($nouveau || $edit): ?>
  <!-- ── FORMULAIRE PRODUIT ── -->
  <div class="admin-card">
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="hidden" name="action" value="<?= $edit ? 'modifier' : 'creer' ?>">
      <?php if ($edit): ?><input type="hidden" name="produit_id" value="<?= $edit['id'] ?>"><?php endif; ?>

      <div class="form-grid-2">
        <div class="form-group"><label>Nom du produit *</label>
          <input type="text" name="nom" value="<?= e($edit['nom'] ?? '') ?>" required></div>
        <div class="form-group"><label>Marque *</label>
          <select name="marque" required>
            <?php foreach ($marques_opts as $m): ?>
            <option value="<?= e($m) ?>" <?= ($edit['marque']??'')===$m?'selected':'' ?>><?= e($m) ?></option>
            <?php endforeach; ?>
          </select></div>
        <div class="form-group"><label>Catégorie *</label>
          <select name="categorie" required>
            <?php foreach ($cats_opts as $c): ?>
            <option value="<?= e($c) ?>" <?= ($edit['categorie']??'')===$c?'selected':'' ?>><?= e($c) ?></option>
            <?php endforeach; ?>
          </select></div>
        <div class="form-group"><label>Série (Samsung: S/A/Note | iPhone: 6-8/X/11+)</label>
          <input type="text" name="serie" value="<?= e($edit['serie'] ?? '') ?>"></div>
        <div class="form-group"><label>Prix (FCFA) *</label>
          <input type="number" name="prix" min="0" value="<?= e($edit['prix'] ?? '') ?>" required></div>
        <div class="form-group"><label>Stock *</label>
          <input type="number" name="stock" min="0" value="<?= e($edit['stock'] ?? 0) ?>" required></div>
      </div>

      <div class="form-group"><label>Description</label>
        <textarea name="description" rows="3"><?= e($edit['description'] ?? '') ?></textarea></div>

      <div class="form-grid-2">
        <div class="form-group"><label>Image (upload — JPG/PNG/WEBP max 2Mo)</label>
          <input type="file" name="image" accept="image/*">
          <?php if (!empty($edit['image'])): ?>
          <small>Image actuelle : <?= e($edit['image']) ?></small><?php endif; ?></div>
        <div class="form-group"><label>OU URL de l'image</label>
          <input type="url" name="image_url" placeholder="https://..." value="<?= strpos($edit['image']??'','http')===0 ? e($edit['image']) : '' ?>"></div>
      </div>

      <div class="form-checkboxes">
        <label><input type="checkbox" name="deal" <?= !empty($edit['deal'])?'checked':'' ?>> 🔥 Deal du jour</label>
        <label><input type="checkbox" name="actif" <?= ($edit['actif']??1)?'checked':'' ?>> ✅ Produit actif</label>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn-admin-primary"><i class="fas fa-save"></i> Enregistrer</button>
        <a href="produits.php" class="btn-admin-outline">Annuler</a>
      </div>
    </form>
  </div>

  <?php else: ?>
  <!-- ── LISTE PRODUITS ── -->
  <div class="admin-card">
    <div class="admin-filters">
      <form method="get" style="display:flex;gap:10px;flex-wrap:wrap">
        <input type="text" name="q" placeholder="Rechercher..." value="<?= e($q_admin) ?>" style="padding:8px 12px;border:1px solid #ddd;border-radius:6px;font-size:13px">
        <select name="filtre" style="padding:8px 12px;border:1px solid #ddd;border-radius:6px;font-size:13px">
          <option value="">Tous les produits</option>
          <option value="rupture" <?= $filtre==='rupture'?'selected':'' ?>>Rupture de stock</option>
          <option value="faible"  <?= $filtre==='faible'?'selected':'' ?>>Stock faible (≤3)</option>
          <option value="inactif" <?= $filtre==='inactif'?'selected':'' ?>>Désactivés</option>
        </select>
        <button type="submit" class="btn-admin-primary">Filtrer</button>
        <a href="produits.php" class="btn-admin-outline">Réinitialiser</a>
      </form>
      <span style="color:#888;font-size:13px"><?= count($produits) ?> produit(s)</span>
    </div>

    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th><th>Produit</th><th>Marque</th><th>Prix</th>
          <th>Stock</th><th>Deal</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($produits as $p): ?>
      <tr class="<?= $p['stock']==0?'row-rupture':($p['stock']<=3?'row-faible':'') ?>">
        <td><?= $p['id'] ?></td>
        <td>
          <strong><?= e($p['nom']) ?></strong>
          <?php if ($p['categorie']): ?><br><small style="color:#888"><?= e($p['categorie']) ?></small><?php endif; ?>
        </td>
        <td><?= e($p['marque']) ?></td>
        <td><?= prix((int)$p['prix']) ?></td>
        <td>
          <!-- Stock inline edit -->
          <div class="stock-edit" data-id="<?= $p['id'] ?>">
            <input type="number" class="stock-input" value="<?= $p['stock'] ?>" min="0"
                   style="width:60px;padding:4px;border:1px solid <?= $p['stock']==0?'#dc3545':($p['stock']<=3?'#ffc107':'#ddd') ?>;border-radius:4px;text-align:center">
            <button class="btn-xs btn-stock-save" onclick="saveStock(<?= $p['id'] ?>, this)"><i class="fas fa-check"></i></button>
          </div>
        </td>
        <td><?= $p['deal'] ? '<span class="badge badge-warning">🔥 Oui</span>' : '—' ?></td>
        <td>
          <div style="display:flex;gap:6px">
            <a href="?edit=<?= $p['id'] ?>" class="btn-xs"><i class="fas fa-edit"></i></a>
            <a href="<?= SITE_URL ?>/produit.php?slug=<?= urlencode($p['slug']) ?>" target="_blank" class="btn-xs"><i class="fas fa-eye"></i></a>
            <form method="post" style="display:inline" onsubmit="return confirm('Désactiver ce produit ?')">
              <input type="hidden" name="action" value="supprimer">
              <input type="hidden" name="produit_id" value="<?= $p['id'] ?>">
              <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
              <button type="submit" class="btn-xs btn-danger"><i class="fas fa-trash"></i></button>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</main>

<script>
// Mise à jour stock inline (AJAX)
function saveStock(id, btn) {
  const input = btn.parentElement.querySelector('.stock-input');
  const stock = parseInt(input.value);
  if (isNaN(stock) || stock < 0) return;

  fetch('<?= SITE_URL ?>/admin/produits.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'action=update_stock&produit_id='+id+'&stock='+stock+'&csrf=<?= csrf_token() ?>'
  })
  .then(r => r.json())
  .then(d => {
    if (d.ok) {
      input.style.borderColor = '#28a745';
      setTimeout(() => input.style.borderColor = d.stock==0?'#dc3545':d.stock<=3?'#ffc107':'#ddd', 1500);
    }
  })
  .catch(() => alert('Erreur réseau'));
}
// Enter key on stock input
document.querySelectorAll('.stock-input').forEach(inp => {
  inp.addEventListener('keydown', e => { if(e.key==='Enter') e.target.nextElementSibling.click(); });
});
</script>
</body>
</html>
