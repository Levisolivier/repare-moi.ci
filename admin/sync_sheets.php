<?php
require_once __DIR__ . '/../includes/fonctions.php';
require_admin();

$message = '';
$type    = '';
$stats   = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sync'])) {
    csrf_check();
    $result  = sync_depuis_sheets();
    $message = $result['message'];
    $type    = $result['type'];
    $stats   = $result['stats'] ?? null;
}

function sync_depuis_sheets() {
    $pdo = getPDO();
    $url = defined('SHEETS_URL') ? SHEETS_URL : '';

    if (empty($url)) {
        return ['type'=>'error','message'=>'URL Google Sheets non configurée dans config/config.php'];
    }

    // ── 1. Récupérer le JSON depuis Apps Script ──
    $opts = ['http' => ['timeout' => 30, 'follow_location' => true,
             'header' => 'User-Agent: RepareMoi-Sync/1.0']];
    $json = @file_get_contents($url, false, stream_context_create($opts));

    if ($json === false) {
        return ['type'=>'error','message'=>'Impossible de contacter Google Sheets. Vérifiez que le script est bien déployé en "Tout le monde".'];
    }

    // Nettoyer le JSON (enlever BOM et espaces parasites)
    $json = trim($json);
    $json = preg_replace('/^\xEF\xBB\xBF/', '', $json);
    $data = json_decode($json, true);

    // ── 2. Parser selon format reçu ──
    if (isset($data['produits']) && is_array($data['produits'])) {
        $produits = $data['produits'];
    } elseif (is_array($data) && isset($data[0])) {
        $produits = $data;
    } elseif (isset($data['values'])) {
        $rows    = $data['values'];
        $headers = array_map('strtolower', array_map('trim', $rows[0]));
        $produits = [];
        for ($i = 1; $i < count($rows); $i++) {
            $obj = [];
            foreach ($headers as $j => $h) { $obj[$h] = $rows[$i][$j] ?? ''; }
            $produits[] = $obj;
        }
    } else {
        return ['type'=>'error',
                'message'=>'Réponse inattendue de Google Sheets : '.substr(htmlspecialchars($json), 0, 200)];
    }

    if (empty($produits)) {
        return ['type'=>'error','message'=>'Le Google Sheets ne contient aucun produit (ou la feuille est vide).'];
    }

    // ── 3. S'assurer que la colonne couleur existe en BDD ──
    try {
        $cols = $pdo->query("SHOW COLUMNS FROM rm_produits LIKE 'couleur'")->fetchAll();
        if (empty($cols)) {
            $pdo->exec("ALTER TABLE rm_produits ADD COLUMN couleur VARCHAR(50) DEFAULT NULL AFTER serie");
        }
    } catch (Exception $e) { /* ignore */ }

    // ── 3b. Nettoyer les slugs dupliqués existants avant la sync ──
    try {
        // Vider les slugs en double en gardant le plus récent
        $pdo->exec("
            UPDATE rm_produits p1
            INNER JOIN rm_produits p2 ON p1.slug = p2.slug AND p1.id < p2.id
            SET p1.slug = CONCAT(p1.slug, '-old-', p1.id)
        ");
    } catch (Exception $e) { /* ignore */ }

    $stats = ['ajoutes' => 0, 'mis_a_jour' => 0, 'supprimes' => 0, 'erreurs' => 0];

    $pdo->beginTransaction();
    try {
        // ── 4. Traiter chaque produit du Sheets ──
        $slugs_sheets = [];

        foreach ($produits as $p) {
            $nom = trim($p['nom'] ?? '');
            if (empty($nom)) { $stats['erreurs']++; continue; }

            $marque    = trim($p['marque']    ?? 'Divers');
            $categorie = trim($p['categorie'] ?? 'Divers');
            $serie     = trim($p['serie']     ?? '');
            $couleur   = trim($p['couleur']   ?? '');
            $prix      = intval(preg_replace('/[^0-9]/', '', $p['prix'] ?? '0'));
            $stock     = intval($p['stock']   ?? 0);
            $desc      = trim($p['description'] ?? '');
            $deal      = in_array(strtolower($p['deal']  ?? ''), ['oui','yes','1','true']) ? 1 : 0;
            $actif     = !in_array(strtolower($p['actif'] ?? 'oui'), ['non','no','0','false']) ? 1 : 0;
            $image     = traiter_image_url($p['image'] ?? '');
            $slug      = generer_slug($nom);

            // Chercher si existe déjà (par nom+marque ou slug existant)
            $st = $pdo->prepare('SELECT id, slug FROM rm_produits WHERE nom=? AND marque=? LIMIT 1');
            $st->execute([$nom, $marque]);
            $row = $st->fetch(PDO::FETCH_ASSOC);
            $id  = $row ? $row['id'] : false;

            // Slug unique (évite les doublons)
            $slug = slug_unique($pdo, $slug, $id ?: null);
            $slugs_sheets[] = $slug;

            if ($id) {
                $pdo->prepare('UPDATE rm_produits SET nom=?,slug=?,description=?,marque=?,categorie=?,serie=?,couleur=?,prix=?,stock=?,image=?,deal=?,actif=?,updated_at=NOW() WHERE id=?')
                    ->execute([$nom,$slug,$desc,$marque,$categorie,$serie,$couleur,$prix,$stock,$image,$deal,$actif,$id]);
                $stats['mis_a_jour']++;
            } else {
                $pdo->prepare('INSERT INTO rm_produits (nom,slug,description,marque,categorie,serie,couleur,prix,stock,image,deal,actif) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')
                    ->execute([$nom,$slug,$desc,$marque,$categorie,$serie,$couleur,$prix,$stock,$image,$deal,$actif]);
                $stats['ajoutes']++;
            }
        }

        // ── 5. Suppression automatique des produits absents du Sheets ──
        if (!empty($slugs_sheets)) {
            $all_in_db = $pdo->query('SELECT id, slug FROM rm_produits')->fetchAll(PDO::FETCH_KEY_PAIR);
            $ids_to_delete = [];
            foreach ($all_in_db as $id => $slug_db) {
                if (!in_array($slug_db, $slugs_sheets)) {
                    $ids_to_delete[] = $id;
                }
            }
            if (!empty($ids_to_delete)) {
                $placeholders = implode(',', array_fill(0, count($ids_to_delete), '?'));
                $pdo->prepare("DELETE FROM rm_produits WHERE id IN ($placeholders)")
                    ->execute($ids_to_delete);
                $stats['supprimes'] = count($ids_to_delete);
            }
        }

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['type'=>'error','message'=>'Erreur BDD : '.$e->getMessage()];
    }

    $msg  = "✅ Synchronisation réussie ! ";
    $msg .= "{$stats['ajoutes']} ajoutés, {$stats['mis_a_jour']} mis à jour, {$stats['supprimes']} supprimés.";
    if ($stats['erreurs']) $msg .= " ({$stats['erreurs']} lignes ignorées car nom vide)";
    return ['type'=>'success','message'=>$msg,'stats'=>$stats];
}

function traiter_image_url($url) {
    if (empty($url)) return '';
    if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $url, $m))
        return 'https://drive.google.com/uc?export=view&id='.$m[1];
    if (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $url, $m))
        return 'https://drive.google.com/uc?export=view&id='.$m[1];
    return $url;
}

function generer_slug($str) {
    $str = strtolower(trim($str));
    $str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
    $str = preg_replace('/[^a-z0-9]+/', '-', $str);
    return substr(trim($str, '-'), 0, 200);
}

function slug_unique(PDO $pdo, string $base_slug, ?int $exclude_id = null): string {
    $slug = $base_slug;
    $i    = 2;
    while (true) {
        $st = $pdo->prepare('SELECT id FROM rm_produits WHERE slug=?' . ($exclude_id ? ' AND id<>?' : '') . ' LIMIT 1');
        $params = [$slug];
        if ($exclude_id) $params[] = $exclude_id;
        $st->execute($params);
        if (!$st->fetchColumn()) break; // slug libre
        $slug = $base_slug . '-' . $i++;
    }
    return $slug;
}

$pageTitle = 'Sync Google Sheets';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= $pageTitle ?> — Admin RepareMoi</title>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin.css">
</head>
<body class="admin-body">
<div class="admin-layout">
  <?php include __DIR__ . '/partials/sidebar.php'; ?>
  <main class="admin-main">
    <div class="admin-header">
      <h1><i class="fas fa-sync-alt" style="color:#0ea5e9"></i> Synchronisation Google Sheets</h1>
    </div>

    <?php if ($message): ?>
    <div class="alert alert-<?= $type==='success'?'success':'error' ?>" style="margin-bottom:24px;padding:14px 18px;border-radius:8px;background:<?= $type==='success'?'#dcfce7':'#fee2e2' ?>;color:<?= $type==='success'?'#16a34a':'#dc2626' ?>;border:1px solid <?= $type==='success'?'#86efac':'#fca5a5' ?>">
      <?= $message ?>
    </div>
    <?php endif; ?>

    <?php if ($stats): ?>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px">
      <div style="background:#dcfce7;border:1px solid #86efac;border-radius:10px;padding:20px;text-align:center">
        <div style="font-size:36px;font-weight:700;color:#16a34a"><?= $stats['ajoutes'] ?></div>
        <div style="font-size:13px;color:#15803d">Produits ajoutés</div>
      </div>
      <div style="background:#dbeafe;border:1px solid #93c5fd;border-radius:10px;padding:20px;text-align:center">
        <div style="font-size:36px;font-weight:700;color:#2563eb"><?= $stats['mis_a_jour'] ?></div>
        <div style="font-size:13px;color:#1d4ed8">Mis à jour</div>
      </div>
      <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:20px;text-align:center">
        <div style="font-size:36px;font-weight:700;color:#dc2626"><?= $stats['supprimes'] ?></div>
        <div style="font-size:13px;color:#b91c1c">Supprimés auto</div>
      </div>
      <div style="background:#fef9c3;border:1px solid #fde047;border-radius:10px;padding:20px;text-align:center">
        <div style="font-size:36px;font-weight:700;color:#ca8a04"><?= $stats['erreurs'] ?></div>
        <div style="font-size:13px;color:#a16207">Erreurs ignorées</div>
      </div>
    </div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px">
      <div class="admin-card">
        <h2 style="font-size:16px;margin-bottom:16px"><i class="fas fa-play-circle" style="color:#f76b1c"></i> Lancer la synchronisation</h2>
        <p style="font-size:13px;color:#666;margin-bottom:12px">
          Importe tous les produits depuis votre Google Sheets. Les produits existants sont mis à jour, les nouveaux sont ajoutés.
        </p>
        <div style="background:#fff3cd;border:1px solid #ffc107;border-radius:6px;padding:10px 14px;margin-bottom:16px;font-size:12px;color:#856404">
          <i class="fas fa-exclamation-triangle"></i> <strong>Suppression automatique activée</strong> — tout produit absent du Sheets sera définitivement supprimé de la BDD à chaque sync.
        </div>
        <form method="post">
          <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
          <button type="submit" name="sync" style="width:100%;background:#f76b1c;color:#fff;border:none;padding:14px;border-radius:8px;font-size:15px;font-weight:700;cursor:pointer">
            <i class="fas fa-sync-alt"></i> Synchroniser maintenant
          </button>
        </form>
      </div>

      <div class="admin-card">
        <h2 style="font-size:16px;margin-bottom:16px"><i class="fas fa-cog" style="color:#0ea5e9"></i> URL Apps Script</h2>
        <code style="display:block;background:#f4f4f4;padding:10px;border-radius:6px;font-size:11px;word-break:break-all;color:<?= defined('SHEETS_URL') && SHEETS_URL ? '#16a34a' : '#dc2626' ?>">
          <?= defined('SHEETS_URL') && SHEETS_URL ? e(SHEETS_URL) : '⚠️ Non configurée — ajoutez SHEETS_URL dans config/config.php' ?>
        </code>
      </div>
    </div>

    <!-- Structure Sheets -->
    <div class="admin-card" style="margin-bottom:24px">
      <h2 style="font-size:16px;margin-bottom:16px"><i class="fab fa-google" style="color:#ea4335"></i> Colonnes attendues dans Google Sheets</h2>
      <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse;font-size:13px">
          <thead><tr style="background:#f76b1c;color:#fff">
            <th style="padding:10px">Colonne</th><th style="padding:10px">Valeurs</th><th style="padding:10px">Exemple</th><th style="padding:10px">Requis</th>
          </tr></thead>
          <tbody>
          <?php foreach([
            ['nom','Catégorie + Marque + Modèle','Écran Samsung A55','✅'],
            ['marque','Samsung/iPhone/Huawei/Xiaomi/Motorola/LG/Nokia/Oppo/Divers','Samsung','✅'],
            ['categorie','Écran/Batterie/Connecteur/Vitre/Caméra/Haut-parleur/Autre','Écran','✅'],
            ['serie','Série du modèle','A',''],
            ['qualite','Original / INCELL / OLED / Copy…','Original',''],
            ['couleur','Couleur de la pièce ← NOUVEAU','Noir',''],
            ['prix','Chiffre entier FCFA','10000','✅'],
            ['stock','Quantité en stock','5','✅'],
            ['description','Texte descriptif','Écran original Samsung…',''],
            ['deal','Oui / Non','Non',''],
            ['actif','Oui / Non (Non = caché du site)','Oui',''],
            ['image','URL Google Drive ou lien direct','https://drive.google.com/…',''],
          ] as [$col,$vals,$ex,$req]): ?>
          <tr style="border-bottom:1px solid #f0f0f0<?= $col==='couleur' ? ';background:#fffbeb' : '' ?>">
            <td style="padding:9px 12px">
              <code style="background:<?= $col==='couleur'?'#fef08a':'#f4f4f4' ?>;padding:2px 8px;border-radius:4px;font-weight:700"><?= $col ?></code>
              <?= $col==='couleur' ? '<span style="font-size:10px;background:#f76b1c;color:#fff;padding:1px 6px;border-radius:3px;margin-left:4px">NEW</span>' : '' ?>
            </td>
            <td style="padding:9px 12px;color:#555;font-size:12px"><?= $vals ?></td>
            <td style="padding:9px 12px;color:#888;font-style:italic;font-size:12px"><?= $ex ?></td>
            <td style="padding:9px 12px;text-align:center"><?= $req ?></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>
</body>
</html>
