<?php
require_once __DIR__ . '/../includes/fonctions.php';
$pdo = getPDO();

$pageTitle = 'Comment choisir la bonne batterie iPhone 11, 12, 13 — REPARE-MOI CI';
$pageDesc  = 'Tout ce qu\'il faut savoir pour choisir la bonne batterie de remplacement pour votre iPhone. Critères de qualité, prix, autonomie et erreurs à éviter.';
require_once __DIR__ . '/../includes/header.php';
?>
<style>
.blog-article{max-width:780px;margin:0 auto;padding:28px 16px 48px}
.ba-back{display:inline-flex;align-items:center;gap:6px;font-size:12px;color:#FF6A00;font-weight:600;margin-bottom:18px;text-decoration:none}
.ba-back:hover{text-decoration:underline}
.ba-tag{display:inline-block;font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#FF6A00;background:#FFF3EA;padding:3px 10px;border-radius:20px;margin-bottom:10px}
.ba-title{font-size:26px;font-weight:900;color:#111;line-height:1.25;margin-bottom:10px}
.ba-meta{display:flex;align-items:center;gap:14px;font-size:11px;color:#9CA3AF;margin-bottom:20px;padding-bottom:18px;border-bottom:1px solid #EBEBEB;flex-wrap:wrap}
.ba-meta i{color:#FF6A00;font-size:10px}
.ba-cover{height:180px;background:linear-gradient(135deg,#ECFDF5,#D1FAE5);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:24px;font-size:72px}
.ba-body{font-size:14px;color:#374151;line-height:1.8}
.ba-body h2{font-size:18px;font-weight:800;color:#111;margin:28px 0 10px;padding-left:12px;border-left:4px solid #FF6A00}
.ba-body h3{font-size:15px;font-weight:700;color:#111;margin:20px 0 8px}
.ba-body p{margin-bottom:14px}
.ba-body ul,.ba-body ol{padding-left:20px;margin-bottom:14px}
.ba-body li{margin-bottom:7px}
.tip{background:#FFF3EA;border-left:4px solid #FF6A00;padding:12px 16px;border-radius:0 8px 8px 0;margin:16px 0;font-size:13px}
.tip strong{color:#E55A00}
.warn{background:#FEF3C7;border-left:4px solid #F59E0B;padding:12px 16px;border-radius:0 8px 8px 0;margin:16px 0;font-size:13px}
.warn strong{color:#92400E}
.danger{background:#FEE2E2;border-left:4px solid #EF4444;padding:12px 16px;border-radius:0 8px 8px 0;margin:16px 0;font-size:13px}
.danger strong{color:#991B1B}
.compare-table{width:100%;border-collapse:collapse;margin:16px 0;font-size:13px}
.compare-table th{background:#111;color:#fff;padding:10px 14px;text-align:left;font-size:12px}
.compare-table td{padding:10px 14px;border-bottom:1px solid #EBEBEB}
.compare-table tr:hover td{background:#FFF8F5}
.badge-best{background:#FF6A00;color:#fff;font-size:9px;font-weight:700;padding:2px 7px;border-radius:4px;margin-left:6px}
.badge-ok{background:#10B981;color:#fff;font-size:9px;font-weight:700;padding:2px 7px;border-radius:4px;margin-left:6px}
.symptom-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:8px;margin:12px 0}
.symptom{background:#FEF3C7;border:1px solid #FDE68A;border-radius:8px;padding:10px 12px;font-size:12px;display:flex;align-items:center;gap:8px}
.symptom i{color:#F59E0B;font-size:13px}
.ba-cta-box{background:linear-gradient(135deg,#111,#1E1E1E);border-radius:12px;padding:24px;text-align:center;margin-top:32px}
.ba-cta-box h3{font-size:16px;font-weight:800;color:#fff;margin-bottom:6px}
.ba-cta-box p{font-size:12px;color:#777;margin-bottom:16px}
.ctas{display:flex;gap:10px;justify-content:center;flex-wrap:wrap}
.btn-or{background:#FF6A00;color:#fff;padding:10px 20px;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:.2s}
.btn-or:hover{background:#E55A00}
.btn-wa{background:#25D366;color:#fff;padding:10px 20px;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px}
.related-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-top:14px}
.related-card{background:#fff;border:1px solid #EBEBEB;border-radius:10px;padding:12px;text-decoration:none;color:inherit;transition:.2s;display:block}
.related-card:hover{border-color:#FF6A00}
.related-tag{font-size:9px;font-weight:700;color:#FF6A00;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px}
.related-title{font-size:11px;font-weight:700;color:#111;line-height:1.35}
@media(max-width:600px){.symptom-grid{grid-template-columns:1fr}.compare-table{font-size:11px}.ba-title{font-size:20px}.related-grid{grid-template-columns:1fr}}
</style>

<div class="container">
<div class="blog-article">

  <a href="<?= SITE_URL ?>/" class="ba-back"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>
  <div class="ba-tag">Conseil d'achat</div>
  <h1 class="ba-title">Comment choisir la bonne batterie pour son iPhone 11, 12 ou 13 ? — Guide 2025</h1>
  <div class="ba-meta">
    <span><i class="fas fa-calendar"></i> Mis à jour : Janvier 2025</span>
    <span><i class="fas fa-clock"></i> 6 min de lecture</span>
    <span><i class="fas fa-mobile-alt"></i> iPhone 11, 12, 12 Pro, 13, 13 Pro</span>
  </div>
  <div class="ba-cover">🔋</div>

  <div class="ba-body">

    <p>La batterie de votre iPhone se décharge trop vite ? Votre téléphone s'éteint à 30% ? Il est peut-être temps de la remplacer. Mais attention — toutes les batteries de remplacement ne se valent pas. Ce guide vous aide à faire le bon choix pour retrouver l'autonomie d'origine de votre iPhone.</p>

    <h2>Comment savoir si votre batterie doit être remplacée ?</h2>
    <p>iOS indique directement l'état de votre batterie. Pour le vérifier :</p>
    <ol>
      <li>Allez dans <strong>Réglages → Batterie → État de la batterie</strong></li>
      <li>Si la capacité est inférieure à <strong>80%</strong>, Apple recommande le remplacement</li>
    </ol>

    <p>Mais même avant d'atteindre 80%, voici les symptômes qui indiquent une batterie en fin de vie :</p>
    <div class="symptom-grid">
      <div class="symptom"><i class="fas fa-bolt"></i> Décharge rapide (50% perdu en quelques heures)</div>
      <div class="symptom"><i class="fas fa-power-off"></i> Arrêt soudain avant 0% de batterie</div>
      <div class="symptom"><i class="fas fa-thermometer-half"></i> Téléphone qui chauffe sans raison</div>
      <div class="symptom"><i class="fas fa-tachometer-alt"></i> iPhone lent (throttling automatique d'iOS)</div>
    </div>

    <div class="tip"><strong>💡 Le saviez-vous ?</strong> Apple active automatiquement un ralentissement du processeur quand la batterie est trop dégradée. Remplacer la batterie peut suffire à redonner de la vitesse à un iPhone considéré "vieux".</div>

    <h2>Les différents types de batteries pour iPhone</h2>
    <p>Sur le marché, vous trouverez plusieurs qualités de batteries. Voici un comparatif honnête :</p>

    <table class="compare-table">
      <thead>
        <tr><th>Type</th><th>Capacité réelle</th><th>Durée de vie</th><th>Prix estimé</th></tr>
      </thead>
      <tbody>
        <tr>
          <td><strong>Originale Apple</strong><span class="badge-best">Meilleur</span></td>
          <td>100% de la capacité annoncée</td>
          <td>500+ cycles</td>
          <td>12 000 – 18 000 FCFA</td>
        </tr>
        <tr>
          <td><strong>Compatible haute qualité</strong><span class="badge-ok">Recommandé</span></td>
          <td>95–100% de capacité</td>
          <td>400–500 cycles</td>
          <td>6 000 – 10 000 FCFA</td>
        </tr>
        <tr>
          <td><strong>Compatible standard</strong></td>
          <td>80–90% de capacité</td>
          <td>200–300 cycles</td>
          <td>3 000 – 5 000 FCFA</td>
        </tr>
        <tr>
          <td><strong>Batterie de mauvaise qualité</strong></td>
          <td>50–70% seulement</td>
          <td>Moins de 150 cycles</td>
          <td>Moins de 2 000 FCFA</td>
        </tr>
      </tbody>
    </table>

    <div class="warn"><strong>⚠️ Méfiez-vous des batteries trop bon marché.</strong> Une batterie vendue moins de 3 000 FCFA pour un iPhone est presque certainement de très mauvaise qualité. Elle peut gonfler, surchauffer et endommager votre téléphone. Ne faites pas d'économies sur la sécurité.</div>

    <h2>Capacités des batteries iPhone — Valeurs officielles</h2>
    <ul>
      <li><strong>iPhone 11 :</strong> 3 110 mAh — autonomie ~16h vidéo</li>
      <li><strong>iPhone 12 :</strong> 2 815 mAh — autonomie ~17h vidéo</li>
      <li><strong>iPhone 12 Pro :</strong> 2 815 mAh — autonomie ~17h vidéo</li>
      <li><strong>iPhone 12 Pro Max :</strong> 3 687 mAh — autonomie ~20h vidéo</li>
      <li><strong>iPhone 13 :</strong> 3 227 mAh — autonomie ~19h vidéo</li>
      <li><strong>iPhone 13 Pro :</strong> 3 095 mAh — autonomie ~22h vidéo</li>
      <li><strong>iPhone 13 Pro Max :</strong> 4 352 mAh — autonomie ~28h vidéo</li>
    </ul>
    <p>Assurez-vous que la batterie achetée correspond bien à ces capacités. Tout produit affichant une capacité significativement supérieure est une arnaque — il est impossible techniquement de dépasser ces valeurs dans les dimensions de l'iPhone.</p>

    <h2>Faut-il faire appel à un technicien ?</h2>
    <p>Contrairement aux Samsung, le remplacement de batterie sur iPhone est une opération <strong>délicate</strong>. Les raisons :</p>
    <ul>
      <li>La batterie est collée au châssis avec une colle forte</li>
      <li>Des nappes fragiles passent sous la batterie</li>
      <li>L'écran doit être démonté en premier sur les modèles 11, 12 et 13</li>
    </ul>

    <div class="tip"><strong>Notre recommandation :</strong> Pour les iPhone, nous conseillons vivement de faire appel à un technicien. Chez REPARE-MOI CI, la réparation est faite <strong>sur place en 40 minutes</strong> avec garantie 48h. Vous pouvez également acheter la batterie et la faire installer par votre réparateur habituel.</div>

    <h2>Prix des batteries iPhone chez REPARE-MOI CI</h2>
    <ul>
      <li>iPhone 5S / 6 / 6 Plus : à partir de <strong>4 000 FCFA</strong></li>
      <li>iPhone 7 / 7 Plus : à partir de <strong>5 000 FCFA</strong></li>
      <li>iPhone X / XS / XR : à partir de <strong>7 000 FCFA</strong></li>
      <li>iPhone 11 / 11 Pro : à partir de <strong>8 000 FCFA</strong></li>
      <li>iPhone 12 / 12 Pro : à partir de <strong>9 000 FCFA</strong></li>
      <li>iPhone 13 / 13 Pro : à partir de <strong>10 000 FCFA</strong></li>
    </ul>
    <p>Toutes nos batteries sont testées avant envoi et bénéficient de la <strong>garantie 48h satisfait ou remboursé</strong>.</p>

    <h3>Conseils pour prolonger la durée de vie de votre nouvelle batterie</h3>
    <ul>
      <li>Activez la charge optimisée dans <strong>Réglages → Batterie</strong></li>
      <li>Évitez de laisser l'iPhone branché toute la nuit en permanence</li>
      <li>Maintenez le niveau entre 20% et 80% autant que possible</li>
      <li>Évitez les expositions prolongées à la chaleur (voiture au soleil)</li>
    </ul>

  </div>

  <div class="ba-cta-box">
    <h3>Batteries iPhone disponibles maintenant !</h3>
    <p>+95 références iPhone en stock. Qualité garantie, réparation sur place en 40 min.</p>
    <div class="ctas">
      <a href="<?= SITE_URL ?>/catalogue.php?marque=iPhone&categorie=Batterie" class="btn-or"><i class="fas fa-battery-three-quarters"></i> Voir les batteries iPhone</a>
      <a href="https://wa.me/<?= SITE_WHATSAPP ?>?text=<?= urlencode('Bonjour, je cherche une batterie pour mon iPhone. Pouvez-vous m\'aider ?') ?>" target="_blank" class="btn-wa"><i class="fab fa-whatsapp"></i> Demander conseil</a>
    </div>
  </div>

  <div style="margin-top:32px">
    <div style="font-size:14px;font-weight:800;color:#111;margin-bottom:14px">Articles similaires</div>
    <div class="related-grid">
      <a href="<?= SITE_URL ?>/blog/changer-ecran-samsung.php" class="related-card">
        <div class="related-tag">Guide pratique</div>
        <div class="related-title">Comment changer son écran Samsung soi-même — Guide complet</div>
      </a>
      <a href="<?= SITE_URL ?>/blog/incell-vs-oled.php" class="related-card">
        <div class="related-tag">Test comparatif</div>
        <div class="related-title">Écran INCELL vs OLED : Lequel choisir selon votre budget ?</div>
      </a>
    </div>
  </div>

</div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
