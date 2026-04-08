<?php
// ============================================================
// REPARE-MOI.CI — Fonctions utilitaires globales
// ============================================================

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

/* ── Sécurité ────────────────────────────────────────────── */

function e($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_check() {
    $token = $_POST['csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(403);
        die('Token CSRF invalide.');
    }
}

function slug($s) {
    $s = mb_strtolower(trim($s));
    $s = str_replace(['é','è','ê','ë'], 'e', $s);
    $s = str_replace(['à','â','ä'], 'a', $s);
    $s = str_replace(['ù','û','ü'], 'u', $s);
    $s = str_replace(['ô','ö'], 'o', $s);
    $s = str_replace(['î','ï'], 'i', $s);
    $s = str_replace(['ç'], 'c', $s);
    $s = preg_replace('/[^a-z0-9\s-]/', '', $s);
    return preg_replace('/[\s-]+/', '-', $s);
}

/* ── Formatage ───────────────────────────────────────────── */

function prix($n) {
    return number_format($n, 0, ',', ' ') . ' ' . DEVISE;
}

function ref_commande() {
    return 'RM-' . strtoupper(substr(uniqid(), -6)) . '-' . date('Y');
}

/* ── Auth client ─────────────────────────────────────────── */

function client_connecte() {
    return !empty($_SESSION['client_id']);
}

function client() {
    if (!client_connecte()) return [];
    static $c = null;
    if ($c) return $c;
    $pdo = getPDO();
    $st  = $pdo->prepare('SELECT * FROM rm_clients WHERE id=? AND actif=1');
    $st->execute([$_SESSION['client_id']]);
    $c = $st->fetch() ?: [];
    return $c;
}

function require_client() {
    if (!client_connecte()) {
        header('Location: ' . SITE_URL . '/connexion.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

/* ── Auth admin ──────────────────────────────────────────── */

function admin_connecte() {
    return !empty($_SESSION['admin_id']);
}

function require_admin() {
    if (!admin_connecte()) {
        header('Location: ' . SITE_URL . '/admin/login.php');
        exit;
    }
}

/* ── Produits ────────────────────────────────────────────── */

function get_produits($filtres = [], $limit = 20, $offset = 0) {
    $pdo   = getPDO();
    $where = ['p.actif = 1'];
    $bind  = [];

    if (!empty($filtres['marque'])) {
        $where[] = 'p.marque = ?'; $bind[] = $filtres['marque'];
    }
    if (!empty($filtres['categorie'])) {
        $where[] = 'p.categorie = ?'; $bind[] = $filtres['categorie'];
    }
    if (!empty($filtres['serie'])) {
        $where[] = 'p.serie = ?'; $bind[] = $filtres['serie'];
    }
    if (isset($filtres['deal']) && $filtres['deal']) {
        $where[] = 'p.deal = 1';
    }
    if (!empty($filtres['q'])) {
        $where[] = '(p.nom LIKE ? OR p.marque LIKE ? OR p.categorie LIKE ?)';
        $q = '%' . $filtres['q'] . '%';
        $bind = array_merge($bind, [$q, $q, $q]);
    }

    $sql = 'SELECT * FROM rm_produits p WHERE ' . implode(' AND ', $where)
         . ' ORDER BY p.deal DESC, p.stock DESC, p.id DESC'
         . ' LIMIT ? OFFSET ?';
    $bind[] = $limit;
    $bind[] = $offset;

    $st = $pdo->prepare($sql);
    $st->execute($bind);
    return $st->fetchAll();
}

function get_produit_slug($slug) {
    $pdo = getPDO();
    $st  = $pdo->prepare('SELECT * FROM rm_produits WHERE slug=? AND actif=1');
    $st->execute([$slug]);
    return $st->fetch() ?: [];
}

function count_produits($filtres = []) {
    $pdo   = getPDO();
    $where = ['actif = 1'];
    $bind  = [];
    if (!empty($filtres['marque']))    { $where[] = 'marque=?';    $bind[] = $filtres['marque']; }
    if (!empty($filtres['categorie'])) { $where[] = 'categorie=?'; $bind[] = $filtres['categorie']; }
    $st = $pdo->prepare('SELECT COUNT(*) FROM rm_produits WHERE ' . implode(' AND ', $where));
    $st->execute($bind);
    return (int)$st->fetchColumn();
}

/* ── Panier (session) ────────────────────────────────────── */

function panier_get() {
    return $_SESSION['panier'] ?? [];
}

function panier_ajouter($id, $qty = 1) {
    $pdo = getPDO();
    $st  = $pdo->prepare('SELECT id,nom,prix,stock,image,marque FROM rm_produits WHERE id=? AND actif=1');
    $st->execute([$id]);
    $p = $st->fetch();
    if (!$p || $p['stock'] < 1) return false;

    if (!isset($_SESSION['panier'])) $_SESSION['panier'] = [];
    if (isset($_SESSION['panier'][$id])) {
        $_SESSION['panier'][$id]['qty'] = min($_SESSION['panier'][$id]['qty'] + $qty, $p['stock']);
    } else {
        $_SESSION['panier'][$id] = ['produit_id'=>$id,'nom'=>$p['nom'],'prix'=>$p['prix'],
                                    'qty'=>$qty,'image'=>$p['image'],'marque'=>$p['marque']];
    }
    return true;
}

function panier_retirer($id) {
    unset($_SESSION['panier'][$id]);
}

function panier_total() {
    $t = 0;
    foreach (panier_get() as $item) $t += $item['prix'] * $item['qty'];
    return $t;
}

function panier_count() {
    return array_sum(array_column(panier_get(), 'qty'));
}

function panier_vider() {
    $_SESSION['panier'] = [];
}

/* ── Commandes ───────────────────────────────────────────── */

function creer_commande($data) {
    $pdo = getPDO();
    $panier = panier_get();
    if (empty($panier)) return false;

    try {
        $pdo->beginTransaction();

        $ref   = ref_commande();
        $total = panier_total();

        $st = $pdo->prepare('INSERT INTO rm_commandes
            (reference,client_id,client_nom,client_email,client_tel,adresse,ville,total,paiement,notes)
            VALUES (?,?,?,?,?,?,?,?,?,?)');
        $st->execute([
            $ref,
            $_SESSION['client_id'] ?? null,
            $data['nom'],
            $data['email'],
            $data['telephone'],
            $data['adresse'],
            $data['ville'] ?? 'Abidjan',
            $total,
            $data['paiement'],
            $data['notes'] ?? null,
        ]);
        $commande_id = (int)$pdo->lastInsertId();

        foreach ($panier as $item) {
            $st2 = $pdo->prepare('INSERT INTO rm_commandes_items
                (commande_id,produit_id,nom_produit,prix_unitaire,quantite,sous_total)
                VALUES (?,?,?,?,?,?)');
            $st2->execute([
                $commande_id,
                $item['produit_id'],
                $item['nom'],
                $item['prix'],
                $item['qty'],
                $item['prix'] * $item['qty'],
            ]);
            // Décrémenter stock
            $pdo->prepare('UPDATE rm_produits SET stock = stock - ? WHERE id = ? AND stock > 0')
                ->execute([$item['qty'], $item['produit_id']]);
        }

        $pdo->commit();
        $_SESSION['last_commande'] = ['ref' => $ref, 'id' => $commande_id, 'total' => $total];
        panier_vider();
        return $commande_id;

    } catch (Exception $e) {
        $pdo->rollBack();
        error_log('[commande] ' . $e->getMessage());
        return false;
    }
}

/* ── Flash messages ──────────────────────────────────────── */

function flash($msg, $type = "success") {
    $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
}

function flash_get() {
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

/* ── Upload image ────────────────────────────────────────── */

function upload_image($file, $prefix = "prod") {
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($file['type'], $allowed)) return false;
    if ($file['size'] > 2 * 1024 * 1024) return false;

    $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = $prefix . '_' . uniqid() . '.' . $ext;
    $dest = UPLOAD_DIR . $name;

    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
    if (move_uploaded_file($file['tmp_name'], $dest)) return $name;
    return false;
}
