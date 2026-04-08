<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_admin();

$pdo = getPDO();

// ── Recherche & filtres ──────────────────────────────────────
$search = trim($_GET['q'] ?? '');
$filtre = $_GET['filtre'] ?? 'tous';
$page   = max(1, intval($_GET['p'] ?? 1));
$limit  = 20;
$offset = ($page - 1) * $limit;

$where  = ['1=1'];
$params = [];

if ($search) {
    $where[]  = '(c.nom LIKE ? OR c.prenom LIKE ? OR c.email LIKE ? OR c.telephone LIKE ?)';
    $s = "%$search%";
    $params = array_merge($params, [$s, $s, $s, $s]);
}
if ($filtre === 'actifs') {
    $where[] = 'c.actif = 1';
} elseif ($filtre === 'inactifs') {
    $where[] = 'c.actif = 0';
} elseif ($filtre === 'wp') {
    $where[] = 'c.wp_user_id IS NOT NULL';
}

$whereStr = implode(' AND ', $where);

// Total
$total = (int)$pdo->prepare("SELECT COUNT(*) FROM rm_clients c WHERE $whereStr")->execute($params) ?
         $pdo->prepare("SELECT COUNT(*) FROM rm_clients c WHERE $whereStr")->execute($params) && 0 : 0;
$stmt_count = $pdo->prepare("SELECT COUNT(*) FROM rm_clients c WHERE $whereStr");
$stmt_count->execute($params);
$total = (int)$stmt_count->fetchColumn();
$total_pages = max(1, ceil($total / $limit));

// Clients avec nb commandes
$stmt = $pdo->prepare("
    SELECT c.*,
           COUNT(DISTINCT cmd.id) AS nb_commandes,
           COALESCE(SUM(cmd.total), 0) AS total_achats
    FROM rm_clients c
    LEFT JOIN rm_commandes cmd ON cmd.client_id = c.id AND cmd.statut != 'annulee'
    WHERE $whereStr
    GROUP BY c.id
    ORDER BY c.created_at DESC
    LIMIT $limit OFFSET $offset
");
$stmt->execute($params);
$clients = $stmt->fetchAll();

// Stats globales
$stats = $pdo->query("
    SELECT 
        COUNT(*) as total,
        SUM(actif=1) as actifs,
        SUM(wp_user_id IS NOT NULL) as migres_wp,
        SUM(MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())) as ce_mois
    FROM rm_clients
")->fetch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Clients — Admin RepareMoi</title>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin.css">
<style>
.client-avatar {
    width: 38px; height: 38px; border-radius: 50%;
    background: linear-gradient(135deg, #f76b1c, #d45c10);
    color: #fff; font-weight: 700; font-size: 14px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.badge-wp { background: #e8f4fd; color: #2563eb; font-size: 10px; padding: 2px 7px; border-radius: 10px; font-weight: 600; }
.badge-actif { background: #dcfce7; color: #16a34a; font-size: 10px; padding: 2px 7px; border-radius: 10px; }
.badge-inactif { background: #fee2e2; color: #dc2626; font-size: 10px; padding: 2px 7px; border-radius: 10px; }
.client-row:hover { background: #fef9f5 !important; }
.filter-btn { padding: 6px 14px; border-radius: 20px; border: 1px solid #e5e7eb; background: #fff; font-size: 13px; cursor: pointer; text-decoration: none; color: #555; }
.filter-btn.active { background: #f76b1c; color: #fff; border-color: #f76b1c; font-weight: 600; }
.detail-popup { display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:1000; align-items:center; justify-content:center; }
.detail-popup.open { display:flex; }
.popup-box { background:#fff; border-radius:12px; padding:32px; max-width:520px; width:90%; max-height:80vh; overflow-y:auto; }
</style>
</head>
<body>
<div class="admin-layout">

  <!-- Sidebar -->
  <aside class="admin-sidebar">
    <div class="sidebar-brand">
      <span>Repare<em>Moi</em></span>
      <small>Administration</small>
    </div>
    <nav class="admin-nav">
      <a href="<?= SITE_URL ?>/admin/"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="<?= SITE_URL ?>/admin/produits.php"><i class="fas fa-box"></i> Produits</a>
      <a href="<?= SITE_URL ?>/admin/sync_sheets.php"><i class="fas fa-sync-alt"></i> Sync Sheets</a>
      <a href="<?= SITE_URL ?>/admin/commandes.php"><i class="fas fa-shopping-bag"></i> Commandes</a>
      <a href="<?= SITE_URL ?>/admin/clients.php" class="active"><i class="fas fa-users"></i> Clients <span class="badge-nav"><?= $stats['total'] ?></span></a>
    </nav>
    <div class="sidebar-footer">
      <a href="<?= SITE_URL ?>" target="_blank"><i class="fas fa-external-link-alt"></i> Voir le site</a>
      <a href="<?= SITE_URL ?>/admin/logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </div>
  </aside>

  <main class="admin-main">
    <div class="admin-header">
      <h1><i class="fas fa-users" style="color:#f76b1c"></i> Clients</h1>
      <span style="font-size:13px;color:#888"><?= $total ?> résultat<?= $total > 1 ? 's' : '' ?></span>
    </div>

    <!-- Stats -->
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">
      <?php foreach ([
        ['Total clients',     $stats['total'],    'users',      '#f76b1c', '#fff0e8'],
        ['Actifs',            $stats['actifs'],   'user-check', '#16a34a', '#dcfce7'],
        ['Migrés WordPress',  $stats['migres_wp'],'wordpress',  '#2563eb', '#dbeafe'],
        ['Inscrits ce mois',  $stats['ce_mois'],  'user-plus',  '#7c3aed', '#f3e8ff'],
      ] as [$label, $val, $icon, $color, $bg]): ?>
      <div style="background:<?= $bg ?>;border-radius:10px;padding:18px 20px;display:flex;align-items:center;gap:14px">
        <div style="width:42px;height:42px;border-radius:10px;background:<?= $color ?>;display:flex;align-items:center;justify-content:center">
          <i class="fas fa-<?= $icon ?>" style="color:#fff;font-size:16px"></i>
        </div>
        <div>
          <div style="font-size:26px;font-weight:700;color:<?= $color ?>"><?= $val ?></div>
          <div style="font-size:12px;color:#666"><?= $label ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Filtres & Recherche -->
    <div style="display:flex;gap:12px;align-items:center;margin-bottom:20px;flex-wrap:wrap">
      <form method="get" style="flex:1;min-width:250px;display:flex;gap:8px">
        <input type="hidden" name="filtre" value="<?= e($filtre) ?>">
        <input type="text" name="q" value="<?= e($search) ?>" placeholder="Rechercher par nom, email, téléphone…"
               style="flex:1;padding:9px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;outline:none">
        <button style="background:#f76b1c;color:#fff;border:none;border-radius:8px;padding:9px 16px;cursor:pointer">
          <i class="fas fa-search"></i>
        </button>
      </form>
      <div style="display:flex;gap:8px;flex-wrap:wrap">
        <?php foreach ([
          ['tous','Tous',''],
          ['actifs','Actifs','user-check'],
          ['inactifs','Inactifs','user-times'],
          ['wp','WordPress','wordpress'],
        ] as [$val, $label, $icon]): ?>
        <a href="?filtre=<?= $val ?><?= $search ? '&q='.urlencode($search) : '' ?>"
           class="filter-btn <?= $filtre === $val ? 'active' : '' ?>">
          <?= $icon ? '<i class="fas fa-'.$icon.'"></i> ' : '' ?><?= $label ?>
        </a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Table clients -->
    <div class="admin-card" style="padding:0;overflow:hidden">
      <table style="width:100%;border-collapse:collapse;font-size:13px">
        <thead>
          <tr style="background:#f8f9fa;border-bottom:2px solid #f0f0f0">
            <th style="padding:13px 16px;text-align:left;font-weight:600;color:#555">Client</th>
            <th style="padding:13px 16px;text-align:left;font-weight:600;color:#555">Contact</th>
            <th style="padding:13px 16px;text-align:left;font-weight:600;color:#555">Ville</th>
            <th style="padding:13px 16px;text-align:center;font-weight:600;color:#555">Commandes</th>
            <th style="padding:13px 16px;text-align:right;font-weight:600;color:#555">Total achats</th>
            <th style="padding:13px 16px;text-align:left;font-weight:600;color:#555">Inscrit le</th>
            <th style="padding:13px 16px;text-align:center;font-weight:600;color:#555">Statut</th>
            <th style="padding:13px 16px;text-align:center;font-weight:600;color:#555">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($clients)): ?>
          <tr><td colspan="8" style="padding:40px;text-align:center;color:#aaa">
            <i class="fas fa-users" style="font-size:32px;margin-bottom:12px;display:block"></i>
            Aucun client trouvé
          </td></tr>
          <?php else: ?>
          <?php foreach ($clients as $c): 
            $initiales = strtoupper(substr($c['prenom'], 0, 1) . substr($c['nom'], 0, 1));
          ?>
          <tr class="client-row" style="border-bottom:1px solid #f5f5f5">
            <td style="padding:12px 16px">
              <div style="display:flex;align-items:center;gap:10px">
                <div class="client-avatar"><?= $initiales ?></div>
                <div>
                  <div style="font-weight:600;color:#1a1a2e"><?= e($c['prenom'].' '.$c['nom']) ?></div>
                  <div style="font-size:11px;color:#aaa">ID #<?= $c['id'] ?>
                    <?php if ($c['wp_user_id']): ?>
                    <span class="badge-wp"><i class="fab fa-wordpress"></i> WP</span>
                    <?php endif; ?>
                    <?php if ($c['mdp_reset'] ?? 0): ?>
                    <span style="background:#fef9c3;color:#a16207;font-size:10px;padding:2px 6px;border-radius:8px">⚠️ Reset mdp</span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </td>
            <td style="padding:12px 16px">
              <div style="color:#374151"><?= e($c['email']) ?></div>
              <div style="font-size:12px;color:#888"><?= e($c['telephone'] ?? '—') ?></div>
            </td>
            <td style="padding:12px 16px;color:#555"><?= e($c['ville'] ?? 'Abidjan') ?></td>
            <td style="padding:12px 16px;text-align:center">
              <span style="background:#f0f4ff;color:#2563eb;padding:3px 10px;border-radius:10px;font-weight:600">
                <?= $c['nb_commandes'] ?>
              </span>
            </td>
            <td style="padding:12px 16px;text-align:right;font-weight:600;color:#16a34a">
              <?= number_format($c['total_achats'], 0, ',', ' ') ?> FCFA
            </td>
            <td style="padding:12px 16px;color:#888;font-size:12px">
              <?= date('d/m/Y', strtotime($c['created_at'])) ?>
            </td>
            <td style="padding:12px 16px;text-align:center">
              <span class="badge-<?= $c['actif'] ? 'actif' : 'inactif' ?>">
                <?= $c['actif'] ? 'Actif' : 'Inactif' ?>
              </span>
            </td>
            <td style="padding:12px 16px;text-align:center">
              <button onclick="voirClient(<?= htmlspecialchars(json_encode($c)) ?>)"
                      style="background:#f76b1c;color:#fff;border:none;border-radius:6px;padding:5px 12px;cursor:pointer;font-size:12px">
                <i class="fas fa-eye"></i> Voir
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div style="display:flex;justify-content:center;gap:8px;margin-top:20px;flex-wrap:wrap">
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
      <a href="?p=<?= $i ?>&filtre=<?= $filtre ?><?= $search ? '&q='.urlencode($search) : '' ?>"
         style="padding:7px 13px;border-radius:6px;border:1px solid #e5e7eb;text-decoration:none;font-size:13px;
                background:<?= $i === $page ? '#f76b1c' : '#fff' ?>;color:<?= $i === $page ? '#fff' : '#555' ?>">
        <?= $i ?>
      </a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
  </main>
</div>

<!-- Popup détail client -->
<div class="detail-popup" id="popup">
  <div class="popup-box">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
      <h2 style="font-size:18px;color:#1a1a2e" id="popup-titre">Client</h2>
      <button onclick="fermerPopup()" style="background:none;border:none;font-size:20px;cursor:pointer;color:#aaa">✕</button>
    </div>
    <div id="popup-content"></div>
  </div>
</div>

<script>
function voirClient(c) {
  document.getElementById('popup-titre').textContent = c.prenom + ' ' + c.nom;
  document.getElementById('popup-content').innerHTML = `
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;font-size:13px">
      <div><strong style="color:#888;font-size:11px;display:block;margin-bottom:4px">EMAIL</strong>${c.email}</div>
      <div><strong style="color:#888;font-size:11px;display:block;margin-bottom:4px">TÉLÉPHONE</strong>${c.telephone || '—'}</div>
      <div><strong style="color:#888;font-size:11px;display:block;margin-bottom:4px">VILLE</strong>${c.ville || 'Abidjan'}</div>
      <div><strong style="color:#888;font-size:11px;display:block;margin-bottom:4px">INSCRIT LE</strong>${c.created_at ? c.created_at.substring(0,10) : '—'}</div>
      <div style="grid-column:span 2"><strong style="color:#888;font-size:11px;display:block;margin-bottom:4px">ADRESSE</strong>${c.adresse || '—'}</div>
      <div><strong style="color:#888;font-size:11px;display:block;margin-bottom:4px">COMMANDES</strong>
        <span style="background:#f0f4ff;color:#2563eb;padding:3px 10px;border-radius:10px;font-weight:700">${c.nb_commandes}</span>
      </div>
      <div><strong style="color:#888;font-size:11px;display:block;margin-bottom:4px">TOTAL ACHATS</strong>
        <span style="color:#16a34a;font-weight:700">${parseInt(c.total_achats).toLocaleString('fr-FR')} FCFA</span>
      </div>
      <div><strong style="color:#888;font-size:11px;display:block;margin-bottom:4px">STATUT</strong>
        <span style="background:${c.actif=='1'?'#dcfce7':'#fee2e2'};color:${c.actif=='1'?'#16a34a':'#dc2626'};padding:3px 10px;border-radius:10px">
          ${c.actif=='1' ? 'Actif' : 'Inactif'}
        </span>
      </div>
      <div><strong style="color:#888;font-size:11px;display:block;margin-bottom:4px">ORIGINE</strong>
        ${c.wp_user_id ? '<span style="background:#e8f4fd;color:#2563eb;padding:3px 10px;border-radius:10px"><i class="fab fa-wordpress"></i> WordPress</span>' : 'Inscription directe'}
      </div>
    </div>
    <div style="margin-top:20px;padding-top:16px;border-top:1px solid #f0f0f0;display:flex;gap:10px">
      <a href="mailto:${c.email}" style="flex:1;text-align:center;background:#f76b1c;color:#fff;padding:10px;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600">
        <i class="fas fa-envelope"></i> Envoyer un email
      </a>
      ${c.telephone ? `<a href="https://wa.me/225${c.telephone.replace(/\D/g,'')}" target="_blank"
        style="flex:1;text-align:center;background:#25d366;color:#fff;padding:10px;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600">
        <i class="fab fa-whatsapp"></i> WhatsApp
      </a>` : ''}
    </div>
  `;
  document.getElementById('popup').classList.add('open');
}
function fermerPopup() {
  document.getElementById('popup').classList.remove('open');
}
document.getElementById('popup').addEventListener('click', function(e) {
  if (e.target === this) fermerPopup();
});
</script>
</body>
</html>
