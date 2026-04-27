<?php
// ============================================================
// REPARE-MOI CI — ERP API PHP v1.1
// ============================================================

// Afficher les erreurs PHP en dev (désactiver en prod)
error_reporting(E_ALL);
ini_set('display_errors', 0); // On capture les erreurs proprement

// ── HEADERS ──────────────────────────────────────────────
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Token, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); exit;
}

// ── CONFIGURATION ──────────────────────────────────────────
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'u173818135_UkbDn');
define('DB_USER', 'u173818135_FS8Nz');
define('DB_PASS', 'RepareMoi2025!');
define('SESSION_HOURS', 8);

// Capturer toute exception non gérée
set_exception_handler(function($e) {
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>$e->getMessage(),'file'=>basename($e->getFile()),'line'=>$e->getLine()]);
    exit;
});

set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// ── CONNEXION BDD ──────────────────────────────────────────
function getPDO() {
    static $pdo = null;
    if ($pdo) return $pdo;
    $pdo = new PDO(
        'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
        DB_USER, DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT            => 5
        ]
    );
    return $pdo;
}

// ── INSTALL TABLES ─────────────────────────────────────────
function installTables() {
    $db = getPDO();

    $db->exec("CREATE TABLE IF NOT EXISTS erp_users (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        login      VARCHAR(50)  UNIQUE NOT NULL,
        password   VARCHAR(255) NOT NULL,
        nom        VARCHAR(100) NOT NULL DEFAULT '',
        role       VARCHAR(20)  NOT NULL DEFAULT 'vendeur',
        color      VARCHAR(20)  NOT NULL DEFAULT '#3B82F6',
        bg         VARCHAR(20)  NOT NULL DEFAULT '#DBEAFE',
        actif      TINYINT(1)   NOT NULL DEFAULT 1,
        created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
        last_login DATETIME     NULL,
        created_by VARCHAR(50)  NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->exec("CREATE TABLE IF NOT EXISTS erp_sessions (
        token      VARCHAR(64)  PRIMARY KEY,
        user_id    INT          NOT NULL,
        login      VARCHAR(50)  NOT NULL,
        expires_at DATETIME     NOT NULL,
        ip         VARCHAR(45)  NULL,
        user_agent VARCHAR(255) NULL,
        created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_login (login),
        INDEX idx_expires (expires_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->exec("CREATE TABLE IF NOT EXISTS erp_audit (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        user_login VARCHAR(50)  NOT NULL,
        action     VARCHAR(100) NOT NULL,
        details    TEXT         NULL,
        ip         VARCHAR(45)  NULL,
        created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_login (user_login),
        INDEX idx_date (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Migration : renommer l'ancienne colonne 'ua' en 'user_agent' si nécessaire
    try {
        $old = $db->query("SHOW COLUMNS FROM erp_sessions LIKE 'ua'")->fetchAll();
        if (!empty($old)) {
            $db->exec("ALTER TABLE erp_sessions CHANGE ua user_agent VARCHAR(255) NULL");
        }
    } catch (Exception $e) { /* ignore si déjà fait */ }

    // Nettoyer les vieilles sessions expirées
    $db->exec("DELETE FROM erp_sessions WHERE expires_at < NOW()");

    // Créer admin si table vide
    $count = (int)$db->query("SELECT COUNT(*) FROM erp_users")->fetchColumn();
    if ($count === 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $db->prepare("INSERT INTO erp_users (login, password, nom, role, color, bg)
                      VALUES ('admin', ?, 'Administrateur', 'admin', '#EF4444', '#FEE2E2')")
           ->execute([$hash]);
        auditLog('system', 'INSTALL', 'Tables créées + admin par défaut');
    }
}

// ── AUDIT ──────────────────────────────────────────────────
function auditLog($login, $action, $details = '') {
    try {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        getPDO()->prepare("INSERT INTO erp_audit (user_login,action,details,ip) VALUES (?,?,?,?)")
                ->execute([$login, $action, $details, $ip]);
    } catch (Exception $e) {
        // Ne pas bloquer si l'audit échoue
    }
}

// ── SESSION ────────────────────────────────────────────────
function getToken() {
    // 1. Header HTTP
    $h = $_SERVER['HTTP_X_TOKEN'] ?? '';
    if ($h) return $h;
    // 2. GET ou POST (merged dans getBody)
    $b = getBody();
    return $b['token'] ?? '';
}

function getSession() {
    $token = getToken();
    if (!$token || strlen($token) < 32) return null;
    $st = getPDO()->prepare("
        SELECT s.token, s.login, u.id as user_id, u.nom, u.role,
               u.color, u.bg, u.actif
        FROM erp_sessions s
        JOIN erp_users u ON u.id = s.user_id
        WHERE s.token = ? AND s.expires_at > NOW() AND u.actif = 1
        LIMIT 1
    ");
    $st->execute([$token]);
    return $st->fetch() ?: null;
}

function requireAuth($roles = []) {
    $sess = getSession();
    if (!$sess) {
        http_response_code(401);
        die(json_encode(['ok'=>false,'error'=>'Session expirée — reconnectez-vous','code'=>401]));
    }
    if ($roles && !in_array($sess['role'], $roles)) {
        http_response_code(403);
        die(json_encode(['ok'=>false,'error'=>'Accès refusé pour le rôle: '.$sess['role'],'code'=>403]));
    }
    return $sess;
}

// ── LECTURE BODY ───────────────────────────────────────────
function getBody() {
    static $data = null;
    if ($data === null) {
        // 1. Commencer avec GET
        $data = $_GET;
        // 2. Merger POST (form-encoded: Content-Type: application/x-www-form-urlencoded)
        if (!empty($_POST)) {
            $data = array_merge($data, $_POST);
        }
        // 3. Essayer JSON body si POST vide
        $raw = file_get_contents('php://input');
        if ($raw && strlen(trim($raw)) > 2) {
            $json = json_decode($raw, true);
            if (is_array($json)) {
                $data = array_merge($data, $json);
            } else {
                // Essayer comme form-encoded si pas du JSON
                parse_str($raw, $parsed);
                if (!empty($parsed)) {
                    $data = array_merge($data, $parsed);
                }
            }
        }
    }
    return $data;
}

// ── INIT ───────────────────────────────────────────────────
installTables();

$action = $_GET['action']
    ?? getBody()['action']
    ?? 'ping';

$data = getBody();

// ── ROUTING ────────────────────────────────────────────────
$result = null;
switch ($action) {

    case 'ping':
        $result = [
            'ok'      => true,
            'msg'     => 'REPARE-MOI CI ERP API v1.1',
            'time'    => date('Y-m-d H:i:s'),
            'php'     => phpversion(),
            'tables'  => true
        ];
        break;

    // ── AUTH ──
    case 'login':
        $login = strtolower(trim($data['login'] ?? ''));
        $pass  = $data['password'] ?? '';

        if (!$login || !$pass) {
            $result = ['ok'=>false,'error'=>'Identifiant et mot de passe requis'];
            break;
        }

        $st = getPDO()->prepare("SELECT * FROM erp_users WHERE login=? AND actif=1 LIMIT 1");
        $st->execute([$login]);
        $user = $st->fetch();

        if (!$user || !password_verify($pass, $user['password'])) {
            auditLog($login, 'LOGIN_FAIL', 'Échec depuis '.($_SERVER['REMOTE_ADDR']??''));
            $result = ['ok'=>false,'error'=>'Identifiant ou mot de passe incorrect'];
            break;
        }

        // Token
        $token   = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + SESSION_HOURS * 3600);
        $ip      = $_SERVER['REMOTE_ADDR'] ?? '';
        $ua      = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);

        getPDO()->prepare("INSERT INTO erp_sessions (token,user_id,login,expires_at,ip,user_agent) VALUES (?,?,?,?,?,?)")
                ->execute([$token, $user['id'], $user['login'], $expires, $ip, $ua]);

        getPDO()->prepare("UPDATE erp_users SET last_login=NOW() WHERE id=?")
                ->execute([$user['id']]);

        auditLog($login, 'LOGIN', 'OK depuis '.$ip);

        $result = [
            'ok'      => true,
            'token'   => $token,
            'expires' => $expires,
            'user'    => [
                'login' => $user['login'],
                'nom'   => $user['nom'],
                'role'  => $user['role'],
                'color' => $user['color'],
                'bg'    => $user['bg'],
            ]
        ];
        break;

    case 'logout':
        $token = getToken();
        $sess  = getSession();
        if ($token) {
            getPDO()->prepare("DELETE FROM erp_sessions WHERE token=?")->execute([$token]);
        }
        if ($sess) auditLog($sess['login'], 'LOGOUT', '');
        $result = ['ok'=>true];
        break;

    case 'me':
        $sess = getSession();
        if (!$sess) {
            $result = ['ok'=>false,'error'=>'Non authentifié','code'=>401];
            break;
        }
        // Prolonger session
        $exp = date('Y-m-d H:i:s', time() + SESSION_HOURS * 3600);
        getPDO()->prepare("UPDATE erp_sessions SET expires_at=? WHERE token=?")
                ->execute([$exp, getToken()]);
        $result = ['ok'=>true,'user'=>[
            'login'=>$sess['login'],'nom'=>$sess['nom'],
            'role'=>$sess['role'],'color'=>$sess['color'],'bg'=>$sess['bg']
        ]];
        break;

    // ── USERS ──
    case 'users_list':
        requireAuth(['admin']);
        $rows = getPDO()->query("
            SELECT id, login, nom, role, color, bg, actif,
                   DATE_FORMAT(created_at,'%d/%m/%Y %H:%i') AS created_at,
                   DATE_FORMAT(last_login,'%d/%m/%Y %H:%i') AS last_login,
                   created_by
            FROM erp_users ORDER BY role, login
        ")->fetchAll();
        $result = ['ok'=>true,'users'=>$rows];
        break;

    case 'user_create':
        $sess  = requireAuth(['admin']);
        $login = strtolower(trim($data['login'] ?? ''));
        $pass  = $data['password'] ?? '';
        $nom   = trim($data['nom'] ?? '') ?: $login;
        $role  = $data['role'] ?? 'vendeur';
        $valid = ['admin','vendeur','reparateur','stock','comptable'];
        $clrs  = [
            'admin'      => ['#EF4444','#FEE2E2'],
            'vendeur'    => ['#3B82F6','#DBEAFE'],
            'reparateur' => ['#F59E0B','#FEF3C7'],
            'stock'      => ['#10B981','#D1FAE5'],
            'comptable'  => ['#8B5CF6','#EDE9FE'],
        ];

        if (!$login)                     { $result=['ok'=>false,'error'=>'Identifiant requis']; break; }
        if (strlen($pass) < 6)           { $result=['ok'=>false,'error'=>'Mot de passe min. 6 caractères']; break; }
        if (!in_array($role, $valid))    { $result=['ok'=>false,'error'=>'Rôle invalide']; break; }
        if (!preg_match('/^[a-z0-9_]+$/', $login)) { $result=['ok'=>false,'error'=>'Identifiant: lettres, chiffres et _ uniquement']; break; }

        [$color, $bg] = $clrs[$role];
        try {
            getPDO()->prepare("INSERT INTO erp_users (login,password,nom,role,color,bg,created_by) VALUES (?,?,?,?,?,?,?)")
                    ->execute([$login, password_hash($pass, PASSWORD_DEFAULT), $nom, $role, $color, $bg, $sess['login']]);
            auditLog($sess['login'], 'USER_CREATE', "$login ($role)");
            $result = ['ok'=>true,'message'=>"Utilisateur \"$login\" créé"];
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'Duplicate'))
                $result = ['ok'=>false,'error'=>'Cet identifiant existe déjà'];
            else throw $e;
        }
        break;

    case 'user_delete':
        $sess  = requireAuth(['admin']);
        $login = $data['login'] ?? '';
        if (!$login)               { $result=['ok'=>false,'error'=>'Login requis']; break; }
        if ($login===$sess['login']) { $result=['ok'=>false,'error'=>'Impossible de supprimer votre propre compte']; break; }
        $db = getPDO();
        $db->prepare("DELETE s FROM erp_sessions s JOIN erp_users u ON u.id=s.user_id WHERE u.login=?")->execute([$login]);
        $db->prepare("DELETE FROM erp_users WHERE login=?")->execute([$login]);
        auditLog($sess['login'], 'USER_DELETE', $login);
        $result = ['ok'=>true];
        break;

    case 'user_toggle':
        $sess  = requireAuth(['admin']);
        $login = $data['login'] ?? '';
        $actif = $data['actif'] ? 1 : 0;
        if (!$login)               { $result=['ok'=>false,'error'=>'Login requis']; break; }
        if ($login===$sess['login']) { $result=['ok'=>false,'error'=>'Impossible de vous désactiver']; break; }
        getPDO()->prepare("UPDATE erp_users SET actif=? WHERE login=?")->execute([$actif, $login]);
        if (!$actif) getPDO()->prepare("DELETE s FROM erp_sessions s JOIN erp_users u ON u.id=s.user_id WHERE u.login=?")->execute([$login]);
        auditLog($sess['login'], 'USER_TOGGLE', "$login → ".($actif?'activé':'désactivé'));
        $result = ['ok'=>true,'actif'=>$actif];
        break;

    // ── MOT DE PASSE ──
    case 'change_password':
        $sess = requireAuth();
        $cur  = $data['current_password']  ?? '';
        $new  = $data['new_password']      ?? '';
        $conf = $data['confirm_password']  ?? '';

        if (!$cur||!$new||!$conf) { $result=['ok'=>false,'error'=>'Tous les champs sont requis']; break; }
        if (strlen($new)<6)       { $result=['ok'=>false,'error'=>'Nouveau MDP min. 6 caractères']; break; }
        if ($new!==$conf)         { $result=['ok'=>false,'error'=>'Les mots de passe ne correspondent pas']; break; }

        $st = getPDO()->prepare("SELECT password FROM erp_users WHERE login=? LIMIT 1");
        $st->execute([$sess['login']]);
        $user = $st->fetch();

        if (!$user || !password_verify($cur, $user['password'])) {
            $result = ['ok'=>false,'error'=>'Mot de passe actuel incorrect'];
            break;
        }

        getPDO()->prepare("UPDATE erp_users SET password=? WHERE login=?")
                ->execute([password_hash($new, PASSWORD_DEFAULT), $sess['login']]);

        // Invalider les autres sessions
        getPDO()->prepare("DELETE FROM erp_sessions WHERE login=? AND token!=?")
                ->execute([$sess['login'], getToken()]);

        auditLog($sess['login'], 'PASSWORD_CHANGE', 'Changement MDP');
        $result = ['ok'=>true,'message'=>'Mot de passe mis à jour'];
        break;

    // ── AUDIT & SESSIONS ──
    case 'audit_log':
        requireAuth(['admin']);
        $rows = getPDO()->query("
            SELECT id, user_login, action, details, ip,
                   DATE_FORMAT(created_at,'%d/%m/%Y %H:%i:%s') AS created_at
            FROM erp_audit ORDER BY id DESC LIMIT 300
        ")->fetchAll();
        $result = ['ok'=>true,'logs'=>$rows];
        break;

    case 'sessions':
        requireAuth(['admin']);
        $rows = getPDO()->query("
            SELECT s.token, s.login, u.nom, u.role,
                   DATE_FORMAT(s.created_at,'%d/%m/%Y %H:%i') AS created_at,
                   DATE_FORMAT(s.expires_at,'%d/%m/%Y %H:%i') AS expires_at,
                   s.ip, s.user_agent,
                   CASE WHEN s.expires_at>NOW() THEN 'active' ELSE 'expirée' END AS statut
            FROM erp_sessions s
            JOIN erp_users u ON u.id=s.user_id
            ORDER BY s.created_at DESC LIMIT 100
        ")->fetchAll();
        foreach ($rows as &$r) {
            $r['token_short'] = substr($r['token'],0,8).'...';
            unset($r['token']);
        }
        $result = ['ok'=>true,'sessions'=>$rows];
        break;

    case 'revoke_session':
        $sess  = requireAuth(['admin']);
        $login = $data['login'] ?? '';
        getPDO()->prepare("DELETE s FROM erp_sessions s JOIN erp_users u ON u.id=s.user_id WHERE u.login=?")
                ->execute([$login]);
        auditLog($sess['login'], 'REVOKE_SESSION', $login);
        $result = ['ok'=>true];
        break;

    default:
        http_response_code(404);
        $result = ['ok'=>false,'error'=>'Action inconnue: '.$action];
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
