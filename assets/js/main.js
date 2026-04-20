// ============================================================
// REPARE-MOI.CI — JavaScript principal
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

  /* ── Tabs ─────────────────────────────────────────────── */
  document.querySelectorAll('.tab-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var tabId = this.dataset.tab;
      if (!tabId) return;

      var header = this.closest('.tabs-header');
      header.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');

      var body = this.closest('.tabs-wrap').querySelector('.tabs-body');
      body.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));

      var target = document.getElementById(tabId);
      if (target) target.classList.add('active');
    });
  });

  /* ── Mobile nav ────────────────────────────────────────── */
  var toggle = document.getElementById('nav-toggle');
  var menu   = document.getElementById('nav-menu');
  if (toggle && menu) {
    toggle.addEventListener('click', function () {
      var isOpen = menu.classList.toggle('open');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      toggle.setAttribute('aria-label', isOpen ? 'Fermer le menu' : 'Ouvrir le menu');
    });

    // Fermer au clic extérieur
    document.addEventListener('click', function (e) {
      if (!toggle.contains(e.target) && !menu.contains(e.target)) {
        menu.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Ouvrir le menu');
      }
    });

    // Fermer avec la touche Echap
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && menu.classList.contains('open')) {
        menu.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.focus();
      }
    });

    // Support clavier des items avec dropdown (mobile)
    menu.querySelectorAll('li > a[aria-haspopup]').forEach(function (link) {
      link.addEventListener('click', function (e) {
        if (window.innerWidth <= 768) {
          e.preventDefault();
          var li = this.closest('li');
          var wasOpen = li.classList.contains('open');
          menu.querySelectorAll('li.open').forEach(function (el) { el.classList.remove('open'); });
          if (!wasOpen) li.classList.add('open');
        }
      });
    });
  }

  /* ── Flash auto-hide ───────────────────────────────────── */
  var flash = document.getElementById('flash-msg');
  if (flash) {
    setTimeout(function () {
      flash.style.opacity = '0';
      flash.style.transform = 'translateY(-10px)';
      flash.style.transition = '.3s';
      setTimeout(function () { flash.remove(); }, 300);
    }, 4000);
  }

  /* ── Add to cart : feedback visuel ────────────────────── */
  // Sélectionne tous les formulaires d'ajout au panier (action=ajouter)
  document.querySelectorAll('form[action*="panier.php"]').forEach(function (form) {
    var actionInput = form.querySelector('input[name="action"]');
    if (!actionInput || actionInput.value !== 'ajouter') return;
    form.addEventListener('submit', function () {
      var btn = form.querySelector('button[type="submit"]');
      if (btn && !btn.disabled) {
        var orig = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Ajouté !';
        btn.style.background = '#28a745';
        setTimeout(function () {
          btn.innerHTML = orig;
          btn.style.background = '';
        }, 1500);
      }
    });
  });

  /* ── Sticky header shadow ──────────────────────────────── */
  var header = document.querySelector('.site-header');
  if (header) {
    window.addEventListener('scroll', function () {
      header.style.boxShadow = window.scrollY > 20
        ? '0 4px 16px rgba(0,0,0,.5)'
        : '0 2px 8px rgba(0,0,0,.4)';
    }, { passive: true });
  }

  /* ── Smooth scroll pour ancres ─────────────────────────── */
  var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (!prefersReducedMotion) {
    document.querySelectorAll('a[href^="#"]').forEach(function (a) {
      a.addEventListener('click', function (e) {
        var id = this.getAttribute('href').slice(1);
        var el = document.getElementById(id);
        if (el) {
          e.preventDefault();
          el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });
  }

});
