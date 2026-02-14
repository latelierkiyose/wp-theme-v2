# Guide de Déploiement — L'Atelier Kiyose

Ce guide détaille la procédure complète pour déployer le nouveau thème sur le site de production https://www.latelierkiyose.fr/.

## Prérequis

- [ ] Accès SFTP/SSH au serveur de production
- [ ] Accès admin WordPress (https://www.latelierkiyose.fr/wp-admin)
- [ ] Compte GitHub avec accès au repository
- [ ] Navigateurs de test installés (Chrome, Firefox, Safari)
- [ ] Temps estimé : 2-3 heures (incluant tests)

## Phase 1 — Préparation (30 min)

### 1.1 Sauvegarde complète

**Base de données** :
1. Se connecter à l'admin WordPress
2. Installer le plugin **UpdraftPlus** (si pas déjà installé)
3. Aller dans Réglages > Sauvegardes UpdraftPlus
4. Cliquer sur "Sauvegarder maintenant"
5. Cocher "Inclure la base de données" et "Inclure les fichiers"
6. Télécharger la sauvegarde sur votre ordinateur
7. Vérifier que le fichier .zip de sauvegarde s'ouvre correctement

**Alternative manuelle (via phpMyAdmin)** :
1. Se connecter à phpMyAdmin
2. Sélectionner la base de données WordPress
3. Cliquer sur "Exporter"
4. Choisir "Méthode rapide" et format "SQL"
5. Télécharger le fichier .sql
6. Vérifier que le fichier fait au moins quelques Mo (pas vide)

**Fichiers wp-content** :
1. Via SFTP, naviguer vers `/wp-content/`
2. Télécharger les dossiers suivants :
   - `/wp-content/themes/` (ancien thème)
   - `/wp-content/uploads/` (médias)
   - `/wp-content/plugins/` (plugins actifs)
3. Compresser en ZIP localement pour archivage

### 1.2 Documentation de l'état actuel

Avant de modifier quoi que ce soit, documenter :

- [ ] Nom et version du thème actuellement actif
- [ ] Liste des plugins actifs et leurs versions
- [ ] Réglages > Lecture : page d'accueil et page des articles
- [ ] Apparence > Menus : emplacements de menus assignés
- [ ] Capture d'écran du site actuel (homepage, pages principales)

**Commande utile (SSH)** :
```bash
# Version WordPress
wp core version

# Plugins actifs
wp plugin list --status=active

# Thème actif
wp theme list --status=active
```

### 1.3 Récupération de l'artefact de build

1. Aller sur [Releases GitHub](https://github.com/latelierkiyose/wp-theme-v2/releases)
2. Identifier le dernier build pré-release (tag: `build-YYYY-MM-DD-xxxxxxx`)
3. Télécharger le fichier **`latelierkiyose-theme-YYYY-MM-DD-xxxxxxx.zip`**
4. ⚠️ **NE PAS télécharger** "Source code (zip)" ou "Source code (tar.gz)"
5. Vérifier le contenu du ZIP localement :
   ```bash
   unzip -l latelierkiyose-theme-*.zip | head -20
   ```
6. Vérifier qu'il contient bien `latelierkiyose/style.css`, `latelierkiyose/functions.php`, etc.

## Phase 2 — Installation du thème (15 min)

### 2.1 Upload du thème

**Via SFTP** :
1. Se connecter au serveur SFTP
2. Naviguer vers `/wp-content/themes/`
3. Créer un dossier temporaire : `mkdir temp-upload`
4. Uploader le fichier ZIP dans `temp-upload/`
5. Se connecter en SSH et extraire :
   ```bash
   cd /path/to/wp-content/themes/temp-upload/
   unzip latelierkiyose-theme-*.zip
   mv latelierkiyose ../latelierkiyose
   cd ..
   rm -rf temp-upload
   ```
6. Vérifier les permissions :
   ```bash
   chmod -R 755 latelierkiyose/
   chown -R www-data:www-data latelierkiyose/  # Adapter selon le serveur
   ```

**Via admin WordPress (alternative)** :
1. Aller dans Apparence > Thèmes
2. Cliquer sur "Ajouter"
3. Cliquer sur "Téléverser un thème"
4. Choisir le fichier ZIP
5. Cliquer sur "Installer maintenant"
6. **NE PAS activer tout de suite**

### 2.2 Vérification pré-activation

Dans Apparence > Thèmes, vérifier :
- [ ] Le thème "L'Atelier Kiyose" apparaît
- [ ] La capture d'écran s'affiche correctement
- [ ] Version, auteur, description sont corrects
- [ ] Aucune erreur PHP dans les logs du serveur

**Vérifier les logs PHP** :
```bash
# Logs Apache
tail -f /var/log/apache2/error.log

# Logs nginx
tail -f /var/log/nginx/error.log

# Ou activer WP_DEBUG temporairement dans wp-config.php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

## Phase 3 — Activation et configuration (30 min)

### 3.1 Activation du thème

1. Aller dans Apparence > Thèmes
2. Survoler "L'Atelier Kiyose"
3. Cliquer sur **"Activer"**
4. Vérifier immédiatement que le site s'affiche (même si le design n'est pas parfait)
5. Si erreur fatale : voir [Plan de rollback](#plan-de-rollback)

### 3.2 Configuration des menus

1. Aller dans Apparence > Menus
2. Sélectionner le menu existant (probablement "Menu principal")
3. Cocher l'emplacement **"Menu Principal"**
4. Cliquer sur "Enregistrer"
5. Rafraîchir le site : le menu doit apparaître dans le header

### 3.3 Configuration du logo

1. Aller dans Apparence > Personnaliser
2. Cliquer sur "Identité du site"
3. Cliquer sur "Sélectionner le logo"
4. Uploader le logo (SVG ou PNG, idéalement 200-300px de largeur)
5. Cliquer sur "Publier"
6. Vérifier l'affichage du logo dans le header

### 3.4 Assignment des templates de page

Pour chaque page ci-dessous, faire :
1. Aller dans Pages > Toutes les pages
2. Cliquer sur le nom de la page pour l'éditer
3. Dans la sidebar droite, section "Attributs de page" > "Modèle"
4. Sélectionner le template approprié
5. Cliquer sur "Mettre à jour"

**Assignment des templates** :

| Page | Template à assigner |
|------|---------------------|
| Accueil | Page d'accueil |
| Art-thérapie | Page de service |
| Rigologie / Yoga du rire | Page de service |
| Bols tibétains | Page de service |
| Ateliers philosophie | Page de service |
| Contact | Page de contact |
| À propos | À propos |
| Calendrier & Tarifs | Calendrier et tarifs |

### 3.5 Configuration WordPress

**Page d'accueil statique** :
1. Aller dans Réglages > Lecture
2. Cocher "Une page statique"
3. Page d'accueil : sélectionner la page "Accueil"
4. Page des articles : sélectionner la page "Actualités" ou "Blog"
5. Cliquer sur "Enregistrer les modifications"

**Permaliens** :
1. Aller dans Réglages > Permaliens
2. Sélectionner "Titre de la publication" (`/%postname%/`)
3. Cliquer sur "Enregistrer les modifications"

**Images à la une** :
1. Pour chaque page de service, aller dans Pages > Modifier
2. Dans la sidebar droite, "Image à la une"
3. Sélectionner ou uploader une image (recommandé : 1920x800px)
4. Mettre à jour la page

### 3.6 Témoignages

Le nouveau thème utilise un Custom Post Type pour les témoignages.

1. Dans l'admin, vérifier que "Témoignages" apparaît dans le menu
2. Créer ou migrer les témoignages existants :
   - Aller dans Témoignages > Ajouter
   - Saisir le nom (titre du témoignage)
   - Saisir le contenu (texte du témoignage)
   - Sélectionner le contexte : Particulier / Entreprise / Structure
   - Publier
3. Répéter pour tous les témoignages

**Shortcode témoignages sur homepage** :
1. Éditer la page d'accueil
2. Dans la section appropriée, insérer le shortcode :
   ```
   [kiyose_testimonials display="carousel" limit="6"]
   ```
3. Mettre à jour la page

### 3.7 Intégrations plugins

**Contact Form 7** (Page Contact) :
1. Vérifier que le plugin Contact Form 7 est installé et activé
2. Aller dans Contact > Formulaires de contact
3. Copier le shortcode du formulaire (ex: `[contact-form-7 id="123"]`)
4. Éditer la page Contact
5. Insérer le shortcode dans le contenu de la page
6. Mettre à jour

**Brevo Newsletter** (Footer + Homepage) :
1. Vérifier que le plugin Brevo (Sendinblue) est installé et activé
2. Aller dans Brevo > Formulaires
3. Copier le shortcode du formulaire newsletter (ex: `[brevo_form id="1"]`)
4. Éditer la page d'accueil (section newsletter)
5. Insérer le shortcode
6. Mettre à jour

**Events Manager** (Page Calendrier) :
1. Vérifier que le plugin Events Manager est installé et activé
2. Aller dans Événements > Réglages
3. Vérifier que les événements sont bien configurés
4. Éditer la page "Calendrier & Tarifs"
5. Insérer le shortcode Events Manager (ex: `[events_list]` ou selon vos besoins)
6. Mettre à jour

## Phase 4 — Validation complète (60 min)

Utiliser la checklist complète de `doc/validation-checklist.md`.

### 4.1 Tests fonctionnels

**Navigation** :
- [ ] Menu desktop : tous les liens fonctionnent
- [ ] Menu mobile : hamburger s'ouvre/ferme, liens fonctionnent
- [ ] Footer : tous les liens fonctionnent (mentions légales, réseaux sociaux)
- [ ] Logo : clique et retourne à l'accueil

**Pages** :
- [ ] Homepage : toutes les sections s'affichent (hero, 4 piliers, témoignages, actualités, newsletter)
- [ ] Art-thérapie : header hero, contenu, CTAs "Me contacter" et "Voir le calendrier"
- [ ] Rigologie : idem
- [ ] Bols tibétains : idem
- [ ] Ateliers philosophie : idem
- [ ] Contact : formulaire + sidebar informations
- [ ] À propos : contenu texte + images
- [ ] Calendrier : événements affichés avec Events Manager
- [ ] Blog (archive) : liste des articles avec pagination
- [ ] Article unique : article complet, partage social, navigation précédent/suivant
- [ ] Recherche : formulaire fonctionne, résultats affichés
- [ ] Page 404 : message personnalisé, formulaire de recherche

**Formulaires** :
- [ ] Contact (CF7) : remplir et soumettre, vérifier réception email
- [ ] Newsletter (Brevo) : s'inscrire, vérifier dans Brevo
- [ ] Recherche : chercher "art-thérapie", vérifier résultats
- [ ] Réservation Events Manager : réserver un événement, vérifier confirmation

**Témoignages** :
- [ ] Carousel sur homepage : défile automatiquement
- [ ] Boutons Previous/Next fonctionnent
- [ ] Bouton Pause/Play fonctionne
- [ ] Flèches clavier fonctionnent (gauche/droite)

### 4.2 Tests accessibilité

**Navigation clavier** :
1. Utiliser uniquement Tab/Shift+Tab/Entrée/Échap pour naviguer
2. Vérifier que tous les éléments interactifs sont accessibles
3. Vérifier que le focus est toujours visible (contour bleu/doré)
4. Vérifier que le menu mobile se ferme avec Échap
5. Vérifier que le carousel se contrôle aux flèches clavier

**axe DevTools** :
1. Installer l'extension [axe DevTools](https://www.deque.com/axe/devtools/) dans Chrome
2. Ouvrir DevTools (F12) > onglet "axe DevTools"
3. Cliquer sur "Scan ALL of my page"
4. Vérifier **0 violations**
5. Répéter sur chaque page principale

**Skip link** :
1. Sur la homepage, appuyer sur Tab
2. Le premier élément focusé doit être le "Skip link" ("Aller au contenu")
3. Appuyer sur Entrée
4. Le focus doit sauter directement au contenu principal

**Contrastes** :
1. Aller sur [WebAIM Contrast Checker](https://webaim.org/resources/contrastchecker/)
2. Vérifier les combinaisons principales :
   - Texte normal (noir #2C2C2C) sur fond blanc #FFFFFF : ≥ 4.5:1 ✓
   - Texte sur fond taupe #E8E2DD : ≥ 4.5:1 ✓
   - Boutons (blanc sur doré #C9A961) : ≥ 4.5:1 ✓
   - Liens (bleu marine #3A4F66) sur fond blanc : ≥ 4.5:1 ✓

### 4.3 Tests responsive

Tester sur les breakpoints suivants :

| Breakpoint | Appareil | À vérifier |
|------------|----------|------------|
| 320px | iPhone SE | Menu hamburger, contenu lisible, pas de scroll horizontal |
| 375px | iPhone 12/13 | Idem |
| 414px | iPhone Pro Max | Idem |
| 768px | iPad portrait | Menu desktop apparaît, grilles 2 colonnes |
| 1024px | iPad landscape / petit laptop | Layout complet, grilles 3-4 colonnes |
| 1440px | Desktop standard | Espacement optimal |
| 1920px | Grand écran | Contenu centré, pas trop large |

**Outils** :
- Chrome DevTools : Device Toolbar (Ctrl+Shift+M)
- Firefox DevTools : Responsive Design Mode (Ctrl+Shift+M)
- Test sur appareils réels (iPhone, iPad, Android)

### 4.4 Tests navigateurs

Tester sur :
- [ ] Chrome (dernière version)
- [ ] Firefox (dernière version)
- [ ] Safari (dernière version macOS)
- [ ] iOS Safari (iPhone/iPad)
- [ ] Chrome Android

Vérifier sur chaque navigateur :
- [ ] Affichage correct de toutes les pages
- [ ] Menu mobile fonctionne
- [ ] Formulaires fonctionnent
- [ ] Carousel fonctionne
- [ ] Aucune erreur Console JavaScript (F12)

### 4.5 Tests performance

**Lighthouse (Chrome DevTools)** :
1. Ouvrir Chrome DevTools (F12)
2. Onglet "Lighthouse"
3. Cocher "Performance", "Accessibility", "Best Practices", "SEO"
4. Mode : Desktop
5. Cliquer sur "Analyze page load"
6. Vérifier les scores :
   - Performance : ≥ 90
   - Accessibility : ≥ 95
   - Best Practices : ≥ 90
   - SEO : ≥ 90

**Core Web Vitals** :
- [ ] LCP (Largest Contentful Paint) < 2.5s
- [ ] INP (Interaction to Next Paint) < 200ms
- [ ] CLS (Cumulative Layout Shift) < 0.1

**PageSpeed Insights** :
1. Aller sur [PageSpeed Insights](https://pagespeed.web.dev/)
2. Entrer l'URL : https://www.latelierkiyose.fr/
3. Analyser sur Mobile et Desktop
4. Vérifier les scores et recommandations

### 4.6 Validation HTML/CSS

**HTML** :
1. Aller sur [W3C Validator](https://validator.w3.org/)
2. Entrer l'URL de chaque page principale
3. Vérifier 0 erreur (les warnings sont OK)

**CSS** :
1. Aller sur [W3C CSS Validator](https://jigsaw.w3.org/css-validator/)
2. Valider les fichiers CSS principaux
3. Les warnings de vendor prefixes sont OK

### 4.7 Vérification légale

- [ ] Page "Mentions légales" accessible depuis le footer
- [ ] SIRET affiché dans le footer : 910 902 932 00010
- [ ] Adresse affichée : 8 Le Peu, 86510 Brux
- [ ] Politique de confidentialité accessible
- [ ] Bannière cookies fonctionne (plugin Complianz)

## Phase 5 — Optimisations post-activation (30 min)

### 5.1 Cache et performance

**Installation plugin de cache** (recommandé : WP Rocket ou Autoptimize) :
1. Installer et activer le plugin de cache
2. Configuration minimale :
   - Activer le cache des pages
   - Activer la minification CSS/JS
   - Activer le lazy loading des images
3. Vider le cache
4. Relancer Lighthouse pour vérifier l'amélioration

**Compression images** (recommandé : ShortPixel ou Imagify) :
1. Installer et activer le plugin
2. Lancer l'optimisation en masse des images existantes
3. Configurer pour optimiser automatiquement les nouvelles images

### 5.2 SEO

**Plugin SEO** (Yoast SEO ou Rank Math) :
1. Vérifier que le plugin est installé et activé
2. Vérifier les meta descriptions de chaque page
3. Vérifier les titres SEO (balises `<title>`)
4. Générer le sitemap XML
5. Soumettre le sitemap à Google Search Console

**Google Search Console** :
1. Se connecter à [Google Search Console](https://search.google.com/search-console)
2. Vérifier que le site est enregistré
3. Soumettre le nouveau sitemap : https://www.latelierkiyose.fr/sitemap_index.xml
4. Demander une réindexation des pages principales

### 5.3 Sécurité

**Plugin de sécurité** (Wordfence ou iThemes Security) :
1. Vérifier que le plugin est installé et activé
2. Lancer un scan de sécurité
3. Corriger les vulnérabilités éventuelles
4. Activer l'authentification à deux facteurs (2FA) pour les comptes admin

**SSL/HTTPS** :
1. Vérifier que le site est bien en HTTPS
2. Vérifier qu'il n'y a pas de contenu mixte (HTTP dans HTTPS)
3. Vérifier la redirection HTTP → HTTPS

**Sauvegardes automatiques** :
1. Configurer UpdraftPlus pour sauvegarder automatiquement
2. Fréquence recommandée : quotidien (base de données) + hebdomadaire (fichiers)
3. Stocker les sauvegardes sur un service cloud (Google Drive, Dropbox, etc.)

## Plan de rollback

En cas de problème grave lors de l'activation :

### Rollback immédiat (via admin WordPress)

1. Se connecter à l'admin WordPress (si accessible)
2. Aller dans Apparence > Thèmes
3. Activer l'ancien thème
4. Le contenu et les réglages restent intacts

### Rollback si admin inaccessible (via SFTP)

1. Se connecter en SFTP au serveur
2. Naviguer vers `/wp-content/themes/`
3. Renommer le dossier `latelierkiyose/` en `latelierkiyose-backup/`
4. WordPress bascule automatiquement sur un thème par défaut (Twenty Twenty-Four)
5. Se connecter à l'admin et activer l'ancien thème

### Rollback complet (restauration sauvegarde)

1. Se connecter à l'admin WordPress
2. Aller dans Réglages > Sauvegardes UpdraftPlus
3. Cliquer sur "Restaurer"
4. Sélectionner la sauvegarde pré-migration
5. Cocher "Base de données" et "Fichiers"
6. Cliquer sur "Restaurer"
7. Attendre la fin de la restauration
8. Se reconnecter à l'admin

## Checklist finale

Avant de déclarer le déploiement terminé :

- [ ] Toutes les pages principales s'affichent correctement
- [ ] Tous les formulaires fonctionnent et envoient des emails
- [ ] Navigation complète fonctionnelle (desktop + mobile)
- [ ] 0 violation accessibilité (axe DevTools)
- [ ] Performance Lighthouse ≥ 90
- [ ] Tests navigateurs validés (Chrome, Firefox, Safari, iOS, Android)
- [ ] Tests responsive validés (320px à 1920px)
- [ ] Conformité légale validée (mentions légales, SIRET, cookies)
- [ ] Sauvegardes automatiques configurées
- [ ] SSL/HTTPS actif
- [ ] Sitemap soumis à Google Search Console
- [ ] Cache activé et testé
- [ ] Images optimisées

## Support post-déploiement

### Monitoring (première semaine)

- Vérifier quotidiennement les logs d'erreur PHP
- Vérifier les soumissions de formulaires (Contact, Newsletter)
- Vérifier Google Analytics pour détecter des anomalies de trafic
- Vérifier Google Search Console pour les erreurs d'exploration

### Maintenance continue

- Mettre à jour WordPress, thème et plugins mensuellement
- Vérifier les sauvegardes hebdomadairement
- Relancer Lighthouse trimestriellement pour surveiller les performances
- Renouveler le certificat SSL annuellement (souvent automatique)

## Ressources

- **Documentation thème** : `/doc/`
- **Validation checklist** : `/doc/validation-checklist.md`
- **Tests manuels** : `/doc/tests-manuels.md`
- **Script de validation** : `/bin/validate-production.php`
- **Repository GitHub** : https://github.com/latelierkiyose/wp-theme-v2
- **Support** : Contacter le développeur

---

**Version** : 1.0.0  
**Dernière mise à jour** : 14 février 2026
