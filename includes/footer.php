
</main><!-- /#main-content -->

<!-- ══ FOOTER REPARE-MOI CI ══ -->
<footer class="site-footer">
  <div class="footer-top">
    <div class="container">
      <div class="footer-grid">

        <!-- Brand column -->
        <div class="footer-brand-col">
          <a href="<?= SITE_URL ?>/" class="footer-logo-link" aria-label="REPARE-MOI CI">
            <svg class="footer-logo-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 220 52" width="180" height="44" aria-hidden="true">
              <defs>
                <linearGradient id="flgrd" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%" stop-color="#FF8C3A"/>
                  <stop offset="100%" stop-color="#E86808"/>
                </linearGradient>
              </defs>
              <rect x="6" y="4" width="29" height="44" rx="5" fill="url(#flgrd)"/>
              <rect x="9" y="10" width="23" height="29" rx="2" fill="#0f172a" opacity="0.88"/>
              <rect x="15" y="6" width="10" height="2.5" rx="1.2" fill="white" opacity="0.4"/>
              <circle cx="20" cy="41.5" r="3" fill="white" opacity="0.22"/>
              <circle cx="20" cy="41.5" r="1.6" fill="white" opacity="0.38"/>
              <rect x="12" y="16" width="17" height="2.5" rx="1" fill="#FF8C3A" opacity="0.85"/>
              <rect x="12" y="21" width="12" height="1.5" rx="0.75" fill="white" opacity="0.28"/>
              <rect x="12" y="24.5" width="15" height="1.5" rx="0.75" fill="white" opacity="0.22"/>
              <text x="20" y="34" font-family="Arial Black,Arial" font-size="10" font-weight="900" fill="#FF8C3A" text-anchor="middle" opacity="0.9">R</text>
              <text x="46" y="30" font-family="Montserrat,Arial Black,Arial" font-size="19.5" font-weight="900" fill="white" letter-spacing="0.3">REPARE-MOI</text>
              <text x="188" y="30" font-family="Montserrat,Arial Black,Arial" font-size="19.5" font-weight="900" fill="#F07818" letter-spacing="0.3"> CI</text>
              <text x="46" y="44" font-family="Open Sans,Arial" font-size="10" fill="#6b7280" letter-spacing="1.1">Réparez sans vous ruiner !</text>
              <rect x="46" y="47.5" width="60" height="1.8" rx="0.9" fill="#F07818" opacity="0.5"/>
            </svg>
          </a>
          <p class="footer-brand-desc">Votre spécialiste en pièces détachées pour smartphones en Côte d'Ivoire. Qualité garantie, prix compétitifs, livraison 24h à Abidjan.</p>
          <div class="footer-socials">
            <a href="#" class="social-btn" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-btn" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="https://wa.me/<?= SITE_WHATSAPP ?>" class="social-btn social-wa" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
            <a href="#" class="social-btn" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
          </div>
          <div class="footer-contact-mini">
            <a href="tel:<?= SITE_TEL1 ?>"><i class="fas fa-phone"></i> <?= SITE_TEL1 ?></a>
            <a href="mailto:<?= SITE_EMAIL ?>"><i class="fas fa-envelope"></i> <?= SITE_EMAIL ?></a>
          </div>
        </div>

        <div class="footer-col">
          <h4>Marques</h4>
          <ul>
            <?php foreach(['Samsung','iPhone','Huawei','Xiaomi','Motorola','LG','Nokia','Google Pixel'] as $m): ?>
            <li><a href="<?= SITE_URL ?>/catalogue.php?marque=<?= urlencode($m) ?>"><?= e($m) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>

        <div class="footer-col">
          <h4>Catégories</h4>
          <ul>
            <?php foreach(['Écran'=>'Écrans','Batterie'=>'Batteries','Coque arrière'=>'Coques arrière','Vitre'=>'Vitres','Caméra'=>'Caméras','Haut-parleur'=>'Haut-parleurs'] as $slug=>$lbl): ?>
            <li><a href="<?= SITE_URL ?>/catalogue.php?categorie=<?= urlencode($slug) ?>"><?= e($lbl) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>

        <div class="footer-col">
          <h4>Mon Compte</h4>
          <ul>
            <li><a href="<?= SITE_URL ?>/boutique.php"><i class="fas fa-store"></i> Boutique</a></li>
            <li><a href="<?= SITE_URL ?>/connexion.php"><i class="fas fa-sign-in-alt"></i> Connexion</a></li>
            <li><a href="<?= SITE_URL ?>/inscription.php"><i class="fas fa-user-plus"></i> Créer un compte</a></li>
            <li><a href="<?= SITE_URL ?>/compte.php"><i class="fas fa-box"></i> Mes commandes</a></li>
            <li><a href="<?= SITE_URL ?>/panier.php"><i class="fas fa-shopping-cart"></i> Mon panier</a></li>
          </ul>
        </div>

      </div>
    </div>
  </div>

  <!-- Footer bottom bar -->
  <div class="footer-bottom">
    <div class="container">
      <div class="footer-bottom-inner">
        <span class="footer-copy">© <?= date('Y') ?> <strong style="color:#e2e8f0">REPARE-MOI.CI</strong> — Tous droits réservés</span>
        <div class="footer-payments">
          <span class="pay-badge pay-om">Orange Money</span>
          <span class="pay-badge pay-mtn">MTN Money</span>
          <span class="pay-badge pay-wave">Wave</span>
          <span class="pay-badge pay-moov">Moov Money</span>
        </div>
      </div>
    </div>
  </div>
</footer>

<style>
/* ══ FOOTER STYLES ══ */
.site-footer { background:#0a0f1e; margin-top:40px; }
.footer-top  { padding:48px 0 32px; border-top:1px solid rgba(255,255,255,.06); }
.footer-grid { display:grid; grid-template-columns:2.2fr 1fr 1fr 1fr; gap:36px; }

/* Brand col */
.footer-brand-col {}
.footer-logo-link { display:inline-block; transition:opacity .2s; margin-bottom:14px; }
.footer-logo-link:hover { opacity:.85; }
.footer-logo-svg { display:block; height:44px; width:auto; }
.footer-brand-desc { font-size:12.5px; color:#4b5563; line-height:1.75; margin-bottom:16px; max-width:280px; }
.footer-socials { display:flex; gap:8px; margin-bottom:14px; }
.social-btn { width:36px; height:36px; border-radius:50%; border:1px solid rgba(255,255,255,.1); display:flex; align-items:center; justify-content:center; color:#6b7280; font-size:14px; transition:.2s; }
.social-btn:hover { background:rgba(240,120,24,.15); border-color:#F07818; color:#F07818; transform:translateY(-2px); }
.social-wa:hover { background:rgba(37,211,102,.15); border-color:#25D366; color:#25D366; }
.footer-contact-mini { display:flex; flex-direction:column; gap:6px; }
.footer-contact-mini a { font-size:12px; color:#4b5563; display:flex; align-items:center; gap:8px; transition:.15s; }
.footer-contact-mini a:hover { color:#F07818; }
.footer-contact-mini i { color:#F07818; font-size:11px; width:14px; }

/* Cols */
.footer-col h4 { font-size:13px; font-weight:700; color:#e2e8f0; margin-bottom:14px; padding-bottom:8px; border-bottom:1px solid rgba(255,255,255,.06); display:flex; align-items:center; gap:6px; }
.footer-col h4::before { content:''; width:3px; height:14px; background:#F07818; border-radius:2px; flex-shrink:0; }
.footer-col ul { list-style:none; }
.footer-col ul li { margin-bottom:8px; }
.footer-col ul li a { font-size:12.5px; color:#4b5563; transition:.15s; display:flex; align-items:center; gap:7px; }
.footer-col ul li a i { font-size:10px; color:#F07818; opacity:.7; width:12px; }
.footer-col ul li a:hover { color:#F07818; padding-left:4px; }

/* Bottom */
.footer-bottom { background:#060a14; border-top:1px solid rgba(255,255,255,.04); padding:14px 0; }
.footer-bottom-inner { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; }
.footer-copy { font-size:12px; color:#374151; }
.footer-payments { display:flex; gap:7px; flex-wrap:wrap; }
.pay-badge { font-size:11px; font-weight:700; padding:4px 10px; border-radius:5px; border:1px solid; }
.pay-om   { color:#f97316; border-color:#f97316; background:rgba(249,115,22,.08); }
.pay-mtn  { color:#fbbf24; border-color:#fbbf24; background:rgba(251,191,36,.08); }
.pay-wave { color:#60a5fa; border-color:#60a5fa; background:rgba(96,165,250,.08); }
.pay-moov { color:#34d399; border-color:#34d399; background:rgba(52,211,153,.08); }

/* Responsive */
@media(max-width:1024px){ .footer-grid{ grid-template-columns:1.5fr 1fr 1fr; } .footer-col:last-child{ display:none; } }
@media(max-width:768px){
  .footer-grid{ grid-template-columns:1fr 1fr; gap:24px; }
  .footer-brand-col{ grid-column:1/-1; }
  .footer-logo-svg{ height:38px; }
  .footer-brand-desc{ max-width:100%; }
}
@media(max-width:480px){
  .footer-grid{ grid-template-columns:1fr; }
  .footer-bottom-inner{ flex-direction:column; text-align:center; }
}
</style>

<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
