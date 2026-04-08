<?php
require_once __DIR__ . '/../includes/fonctions.php';
$pdo = getPDO();

$pageTitle = 'Comment changer son écran Samsung soi-même — Guide complet — REPARE-MOI CI';
$pageDesc  = 'Guide étape par étape pour remplacer l\'écran de votre Samsung Galaxy A-series en moins d\'une heure. Outils, astuces et erreurs à éviter.';
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
.ba-cover{height:180px;background:linear-gradient(135deg,#EFF6FF,#DBEAFE);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:24px;font-size:72px}
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
.step-box{background:#F9FAFB;border:1px solid #EBEBEB;border-radius:10px;padding:14px 16px;margin:10px 0;display:flex;gap:12px;align-items:flex-start}
.step-num{width:30px;height:30px;border-radius:50%;background:#FF6A00;color:#fff;font-size:13px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px}
.step-text strong{display:block;font-size:13px;font-weight:700;color:#111;margin-bottom:3px}
.step-text span{font-size:12px;color:#6B7280;line-height:1.55;display:block}
.tools-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:8px;margin:12px 0}
.tool-item{background:#F9FAFB;border:1px solid #EBEBEB;border-radius:8px;padding:10px 12px;font-size:12px;display:flex;align-items:center;gap:8px}
.tool-item i{color:#FF6A00;width:14px}
.ba-cta-box{background:linear-gradient(135deg,#111,#1E1E1E);border-radius:12px;padding:24px;text-align:center;margin-top:32px}
.ba-cta-box h3{font-size:16px;font-weight:800;color:#fff;margin-bottom:6px}
.ba-cta-box p{font-size:12px;color:#777;margin-bottom:16px}
.ctas{display:flex;gap:10px;justify-content:center;flex-wrap:wrap}
.btn-or{background:#FF6A00;color:#fff;padding:10px 20px;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:.2s}
.btn-or:hover{background:#E55A00}
.btn-wa{background:#25D366;color:#fff;padding:10px 20px;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:6px}
.btn-wa:hover{background:#1ebe5d}
.related-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-top:14px}
.related-card{background:#fff;border:1px solid #EBEBEB;border-radius:10px;padding:12px;text-decoration:none;color:inherit;transition:.2s;display:block}
.related-card:hover{border-color:#FF6A00}
.related-tag{font-size:9px;font-weight:700;color:#FF6A00;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px}
.related-title{font-size:11px;font-weight:700;color:#111;line-height:1.35}
@media(max-width:600px){.tools-grid{grid-template-columns:1fr}.ba-title{font-size:20px}.related-grid{grid-template-columns:1fr}}
</style>

<div class="container">
<div class="blog-article">

  <a href="<?= SITE_URL ?>/" class="ba-back"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>
  <div class="ba-tag">Guide pratique</div>
  <h1 class="ba-title">Comment changer son écran Samsung soi-même — Guide complet 2025</h1>
  <div class="ba-meta">
    <span><i class="fas fa-calendar"></i> Mis à jour : Janvier 2025</span>
    <span><i class="fas fa-clock"></i> 8 min de lecture</span>
    <span><i class="fas fa-mobile-alt"></i> Compatible : Galaxy A-series, M-series, S-series</span>
  </div>
  <div class="ba-cover">🛠️</div>

  <div class="ba-body">

    <p>Votre écran Samsung est fissuré ou ne s'affiche plus correctement ? Bonne nouvelle : changer son écran Samsung soi-même est tout à fait possible, même sans être un technicien expert. Ce guide vous accompagne étape par étape pour réussir cette réparation en moins d'une heure.</p>

    <div class="tip"><strong>💡 Bon à savoir :</strong> Chez REPARE-MOI CI, nous proposons également une <strong>réparation sur place en 40 minutes</strong> avec un technicien certifié. Si vous n'êtes pas à l'aise avec les manipulations techniques, n'hésitez pas à nous contacter.</div>

    <h2>Avant de commencer : vérifiez la compatibilité</h2>
    <p>Chaque modèle Samsung nécessite un écran spécifique. Voici comment identifier votre modèle :</p>
    <ol>
      <li>Allez dans <strong>Paramètres → À propos du téléphone → Informations sur l'appareil</strong></li>
      <li>Notez le numéro de modèle (ex : SM-A525F pour le Galaxy A52)</li>
      <li>Ce numéro est aussi inscrit sous la batterie ou sur la boîte d'origine</li>
    </ol>
    <div class="warn"><strong>⚠️ Attention :</strong> Un écran non compatible peut endommager définitivement votre téléphone. En cas de doute, contactez-nous via WhatsApp avec votre numéro de modèle.</div>

    <h2>Les outils nécessaires</h2>
    <div class="tools-grid">
      <div class="tool-item"><i class="fas fa-screwdriver"></i> Tournevis cruciforme T5 / T6</div>
      <div class="tool-item"><i class="fas fa-fire"></i> Pistolet thermique ou sèche-cheveux</div>
      <div class="tool-item"><i class="fas fa-cut"></i> Spatule ou médiator plastique</div>
      <div class="tool-item"><i class="fas fa-mobile-alt"></i> Ventouse de démontage</div>
      <div class="tool-item"><i class="fas fa-tint"></i> Alcool isopropylique 90%+</div>
      <div class="tool-item"><i class="fas fa-band-aid"></i> Bande adhésive double-face (optionnel)</div>
    </div>

    <h2>Étapes de remplacement — Samsung Galaxy A-series</h2>

    <div class="step-box">
      <div class="step-num">1</div>
      <div class="step-text">
        <strong>Éteignez complètement le téléphone</strong>
        <span>Maintenez le bouton power et sélectionnez "Éteindre". Attendez que l'écran soit totalement noir avant de continuer.</span>
      </div>
    </div>

    <div class="step-box">
      <div class="step-num">2</div>
      <div class="step-text">
        <strong>Chauffez les bords de l'écran</strong>
        <span>Avec un sèche-cheveux ou pistolet thermique, chauffez légèrement les bords de l'écran pendant 1 à 2 minutes. Cela ramollit la colle qui maintient l'écran. Ne pas dépasser 60°C.</span>
      </div>
    </div>

    <div class="step-box">
      <div class="step-num">3</div>
      <div class="step-text">
        <strong>Découpez la colle avec la ventouse et le médiator</strong>
        <span>Placez la ventouse sur le bas de l'écran et tirez doucement. Glissez le médiator plastique dans l'interstice créé, puis faites-le coulisser lentement sur tout le pourtour. Soyez patient — ne forcez jamais.</span>
      </div>
    </div>

    <div class="step-box">
      <div class="step-num">4</div>
      <div class="step-text">
        <strong>Déconnectez les nappes de l'écran</strong>
        <span>Soulevez délicatement l'écran (ne tirez pas trop : des nappes sont encore connectées). Repérez les connecteurs plats et déverrouillez-les en soulevant le petit loquet avant de les débrancher.</span>
      </div>
    </div>

    <div class="step-box">
      <div class="step-num">5</div>
      <div class="step-text">
        <strong>Installez le nouvel écran</strong>
        <span>Branchez les nappes du nouvel écran dans l'ordre inverse. Vérifiez le bon fonctionnement AVANT de recoller. Allumez le téléphone et testez : toucher, luminosité, couleurs.</span>
      </div>
    </div>

    <div class="step-box">
      <div class="step-num">6</div>
      <div class="step-text">
        <strong>Refermez et recolllez</strong>
        <span>Si tout fonctionne, nettoyez les bords avec l'alcool isopropylique, appliquez la nouvelle colle (double-face ou colle LOCA selon le modèle), pressez l'écran uniformément et laissez sécher 24h.</span>
      </div>
    </div>

    <div class="danger"><strong>🚫 Erreurs fréquentes à éviter :</strong><br>
    • Forcer l'ouverture sans chauffer → brise les clips en plastique<br>
    • Tirer sur les nappes sans déverrouiller le loquet → casse irréparable<br>
    • Recoller sans tester → impossible de rouvrir si problème<br>
    • Utiliser un écran "universel" → incompatibilité avec le tactile</div>

    <h2>Modèles Samsung les plus réparés</h2>
    <p>Les écrans disponibles chez REPARE-MOI CI couvrent les modèles les plus populaires en Côte d'Ivoire :</p>
    <ul>
      <li><strong>Samsung Galaxy A-series :</strong> A01, A02, A03, A10, A12, A13, A22, A31, A32, A51, A52, A53, A54, A71, A72</li>
      <li><strong>Samsung Galaxy S-series :</strong> S8, S9, S10, S20, S21, S22, S23 (écrans OLED)</li>
      <li><strong>Samsung Galaxy M-series :</strong> M10, M21, M31, M32, M52</li>
      <li><strong>Samsung Galaxy Note :</strong> Note 8, Note 9, Note 10, Note 20</li>
    </ul>

    <h3>Quelle qualité d'écran choisir ?</h3>
    <p>Il existe 3 niveaux de qualité pour les écrans Samsung :</p>
    <ul>
      <li><strong>Original (OEM) :</strong> Qualité identique à l'écran d'origine. Couleurs parfaites, tactile précis. Prix plus élevé mais durabilité maximale.</li>
      <li><strong>INCELL :</strong> Bonne qualité, prix intermédiaire. Couleurs légèrement différentes mais tactile très correct. Idéal pour les petits budgets.</li>
      <li><strong>Copy :</strong> Qualité basique. À réserver en dépannage d'urgence uniquement.</li>
    </ul>
    <div class="tip"><strong>Notre recommandation :</strong> Optez toujours pour la qualité Original ou INCELL. La différence de prix est faible, mais la durabilité est incomparablement supérieure. Tous nos écrans sont testés avant expédition.</div>

    <h2>Prix des écrans Samsung chez REPARE-MOI CI</h2>
    <p>Nos tarifs sont parmi les plus compétitifs de Côte d'Ivoire :</p>
    <ul>
      <li>Galaxy A01 à A13 : à partir de <strong>10 000 FCFA</strong></li>
      <li>Galaxy A52 / A53 / A54 : à partir de <strong>18 000 FCFA</strong></li>
      <li>Galaxy S21 / S22 (OLED) : à partir de <strong>35 000 FCFA</strong></li>
      <li>Galaxy Note 10 / Note 20 : à partir de <strong>25 000 FCFA</strong></li>
    </ul>
    <p>Tous les prix incluent la garantie 48h satisfait ou remboursé.</p>

  </div>

  <!-- CTA -->
  <div class="ba-cta-box">
    <h3>Trouvez votre écran Samsung en stock !</h3>
    <p>+340 références Samsung disponibles immédiatement. Garantie 48h sur toutes les pièces.</p>
    <div class="ctas">
      <a href="<?= SITE_URL ?>/catalogue.php?marque=Samsung&categorie=%C3%89cran" class="btn-or"><i class="fas fa-tv"></i> Voir les écrans Samsung</a>
      <a href="https://wa.me/<?= SITE_WHATSAPP ?>?text=<?= urlencode('Bonjour, je cherche un écran Samsung pour mon téléphone. Pouvez-vous m\'aider ?') ?>" target="_blank" class="btn-wa"><i class="fab fa-whatsapp"></i> Nous contacter</a>
    </div>
  </div>

  <!-- Articles liés -->
  <div style="margin-top:32px">
    <div style="font-size:14px;font-weight:800;color:#111;margin-bottom:14px">Articles similaires</div>
    <div class="related-grid">
      <a href="<?= SITE_URL ?>/blog/choisir-batterie-iphone.php" class="related-card">
        <div class="related-tag">Conseil</div>
        <div class="related-title">Comment choisir la bonne batterie pour son iPhone 11, 12 ou 13 ?</div>
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
