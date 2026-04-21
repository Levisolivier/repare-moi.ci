<?php
// ============================================================
// REPARE-MOI.CI — Connexion base de données MySQL (PDO)
// ============================================================

define('DB_HOST', '127.0.0.1');        // ✅ Host confirmé
define('DB_NAME', 'u173818135_UkbDn');
define('DB_USER', 'u173818135_FS8Nz');
define('DB_PASS', 'RepareMoi2025!');
define('DB_CHARSET', 'utf8mb4');

// ⚠️ Mettre à true uniquement en développement local — jamais en production
define('DB_DEBUG', false);

function getPDO() {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        error_log('[DB] Connexion echouee : ' . $e->getMessage());

        if (DB_DEBUG) {
            die('
            <div style="font-family:monospace;background:#1e1e1e;color:#f8f8f8;padding:30px;margin:40px auto;max-width:700px;border-radius:8px">
                <h2 style="color:#f76b1c;margin-bottom:16px">Erreur de connexion BDD</h2>
                <pre style="background:#111;padding:14px;border-radius:4px;color:#ff6b6b;white-space:pre-wrap">'
                . htmlspecialchars($e->getMessage()) .
                '</pre>
                <hr style="border-color:#333;margin:20px 0">
                <p style="color:#aaa">Action requise dans <strong style="color:#fff">Hostinger → Bases de données</strong> :</p>
                <ol style="color:#ccc;line-height:2.2;margin-left:20px">
                    <li>Section <strong style="color:#f76b1c">"Add User To Database"</strong></li>
                    <li>User : <strong style="color:#f76b1c">u173818135_FS8Nz</strong></li>
                    <li>Database : <strong style="color:#f76b1c">u173818135_UkbDn</strong></li>
                    <li>Privileges : <strong style="color:#f76b1c">ALL PRIVILEGES</strong></li>
                </ol>
                <p style="color:#888;font-size:12px;margin-top:16px">Remettez DB_DEBUG a false apres resolution.</p>
            </div>');
        }

        die('<h1 style="font-family:sans-serif;text-align:center;margin-top:80px;color:#c00">
             Service temporairement indisponible. Veuillez reessayer.</h1>');
    }
}
