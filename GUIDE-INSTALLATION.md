# 🛠️ GUIDE D'INSTALLATION — repare-moi.ci
## Site PHP custom (remplacement de WordPress)

---

## 📋 PRÉREQUIS

- Hébergement mutualisé cPanel (OVH, Hostinger, o2switch…)
- PHP 8.0 ou supérieur (vérifiez dans cPanel > Versions PHP)
- MySQL 5.7+ (fourni par votre hébergeur)
- Accès FTP ou Gestionnaire de fichiers cPanel

---

## 🔢 ÉTAPES D'INSTALLATION

### Étape 1 — Sauvegarder WordPress (important !)
Avant de supprimer WordPress :
1. Dans cPanel → **phpMyAdmin** → Sélectionnez votre BDD WP → **Exporter** (format SQL)
2. Dans cPanel → **Gestionnaire de fichiers** → Compressez `/public_html` en ZIP
3. Gardez ces sauvegardes précieusement

### Étape 2 — Préparer la base de données
1. Ouvrez **cPanel → phpMyAdmin**
2. Sélectionnez votre base de données WordPress existante
3. Cliquez sur **Importer** → Sélectionnez `install.sql`
4. Cliquez **Exécuter**
   - Cela crée les tables `rm_produits`, `rm_clients`, `rm_commandes`, etc.
   - Vos tables WordPress (`wp_*`) restent intactes
5. **Optionnel** : Décommentez la section "MIGRATION" dans `install.sql`
   pour importer vos produits WooCommerce existants

### Étape 3 — Configurer la connexion BDD
Ouvrez `config/db.php` et modifiez :
```php
define('DB_HOST', 'localhost');     // Toujours localhost sur mutualisé
define('DB_NAME', 'votre_bdd');    // Ex: user123_repareamoi
define('DB_USER', 'votre_user');   // Utilisateur BDD cPanel
define('DB_PASS', 'votre_mdp');    // Mot de passe BDD
```
Trouvez ces infos dans : **cPanel → MySQL Databases**

### Étape 4 — Configurer le site
Ouvrez `config/config.php` et modifiez :
```php
define('SITE_URL', 'https://repare-moi.ci');   // Votre vrai domaine
define('SECRET_KEY', 'changez_cette_valeur_!@#'); // Clé aléatoire unique
define('ENV', 'prod');  // Mettre 'dev' pour déboguer
```

### Étape 5 — Supprimer les fichiers WordPress
Dans **cPanel → Gestionnaire de fichiers → public_html** :
- Sélectionnez TOUT sauf : votre dossier `.git` si présent, `wp-content/uploads` (vos images)
- Supprimez tout
- ⚠️ Gardez le dossier `uploads` si vous voulez récupérer vos images produits

### Étape 6 — Uploader les fichiers du site
Via **FTP** (FileZilla) ou **Gestionnaire de fichiers cPanel** :
- Uploadez tout le contenu du dossier `repare-moi/` dans `public_html/`
- Assurez-vous que l'arborescence soit : `public_html/index.php`, `public_html/config/`, etc.

### Étape 7 — Créer le dossier uploads
Dans `public_html/` créez le dossier `uploads/produits/`
Permissions : `755`

### Étape 8 — Changer le mot de passe admin
1. Allez sur `https://votre-site.ci/admin/login.php`
2. Connectez-vous avec :
   - Email : `admin@repare-moi.ci`
   - Mot de passe : `Admin2025!`
3. **Changez immédiatement le mot de passe** en base via phpMyAdmin :
```sql
UPDATE rm_admins
SET password = '$2y$12$VOTRE_HASH'
WHERE email = 'admin@repare-moi.ci';
```
Pour générer un hash sécurisé :
```php
<?php echo password_hash('VotreNouveauMdp!', PASSWORD_BCRYPT, ['cost'=>12]);
```

---

## 📁 STRUCTURE DES FICHIERS

```
public_html/
├── .htaccess              ← Sécurité + cache
├── index.php              ← Page d'accueil
├── catalogue.php          ← Catalogue avec filtres
├── produit.php            ← Fiche produit
├── panier.php             ← Panier + commande
├── confirmation.php       ← Confirmation commande
├── connexion.php          ← Login client
├── inscription.php        ← Inscription client
├── deconnexion.php        ← Logout
├── compte.php             ← Espace client
├── config/
│   ├── config.php         ← ⚙️ Configuration (URL, clés)
│   └── db.php             ← ⚙️ Base de données (credentials)
├── includes/
│   ├── fonctions.php      ← Fonctions PHP (auth, panier, BDD)
│   ├── header.php         ← En-tête + nav
│   └── footer.php         ← Pied de page
├── admin/
│   ├── index.php          ← Dashboard admin
│   ├── produits.php       ← Gestion produits + stock
│   ├── commandes.php      ← Gestion commandes
│   ├── login.php          ← Connexion admin
│   └── logout.php         ← Déconnexion admin
├── assets/
│   ├── css/style.css      ← Styles frontend
│   ├── css/admin.css      ← Styles admin
│   └── js/main.js         ← JavaScript
└── uploads/
    └── produits/          ← Images produits (créer manuellement)
```

---

## 🔧 GESTION DES PRODUITS (Admin)

### Accéder à l'admin
👉 `https://votre-site.ci/admin/`

### Ajouter un produit
1. Admin → **Produits** → **Nouveau produit**
2. Remplir : nom, marque, catégorie, prix, stock, image
3. **Colonne "Série"** : Samsung → `S` / `A` / `Note` | iPhone → `6-8` / `X` / `11+`
4. **Deal du jour** : coché = affiché en priorité en page d'accueil

### Mettre à jour le stock
- **Méthode rapide** : Dans la liste produits, modifiez le stock directement et appuyez sur ✓ (sauvegarde AJAX instantanée)
- **Méthode complète** : Cliquez sur ✏️ pour éditer le produit

### Stock à 0
→ Le produit affiche automatiquement le badge rouge "Rupture de stock" et le bouton est désactivé

---

## 🔒 SÉCURITÉ

- ✅ Mots de passe hashés avec bcrypt (cost=12)
- ✅ Protection CSRF sur tous les formulaires
- ✅ Requêtes PDO préparées (anti-injection SQL)
- ✅ Sessions sécurisées (httpOnly, SameSite)
- ✅ `.htaccess` bloque l'accès direct à `config/` et `includes/`
- ✅ Erreurs PHP masquées en production
- 🔧 **Optionnel** : Restreignez `/admin/` par IP dans `admin/.htaccess`

---

## ❓ PROBLÈMES COURANTS

| Problème | Solution |
|----------|---------- |
| Page blanche | Mettez `ENV = 'dev'` dans config.php puis regardez les erreurs |
| Erreur BDD | Vérifiez host/user/pass dans `config/db.php` |
| Images ne s'affichent pas | Vérifiez que `uploads/produits/` existe avec droits 755 |
| Admin inaccessible | Vérifiez que `session_start()` fonctionne (PHP sessions activées) |
| `.htaccess` erreur 500 | Demandez à votre hébergeur d'activer `mod_rewrite` |

---

## 📞 SUPPORT
Pour toute question : `info@repare-moi.ci`
