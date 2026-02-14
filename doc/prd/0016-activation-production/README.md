# PRD 0016 — Activation et pré-production

## Statut

**terminé**

## Objectif

Documenter et préparer la procédure d'activation du thème sur le site de production, avec la checklist complète de validation pré-lancement et les outils de vérification automatisés.

## Dépendances

- **PRD 0001 à 0015** : Toutes les fonctionnalités du thème doivent être implémentées et testées
- **CI/CD** : Le workflow GitHub Actions doit créer des artefacts de build valides
- **Validation Checklist** : `doc/validation-checklist.md` doit être complète

## Contexte

Le site WordPress existe déjà à l'adresse https://www.latelierkiyose.fr/ avec du contenu (pages, articles, événements). L'activation du nouveau thème remplace uniquement la couche de présentation. Les contenus restent en base de données.

**Important** : Il s'agit d'une **migration de thème**, pas d'un nouveau site. Toutes les données existantes doivent être préservées.

## Procédure d'activation

### Étape 1 — Sauvegarde

- [ ] Sauvegarde complète de la base de données
- [ ] Sauvegarde des fichiers du site (wp-content)
- [ ] Vérifier que la sauvegarde est restaurable

### Étape 2 — Installation du thème

**Récupération de l'artefact** :
- [ ] Aller sur la page [Releases du repository GitHub](https://github.com/latelierkiyose/wp-theme-v2/releases)
- [ ] Télécharger le dernier build pré-release (format: `latelierkiyose-theme-YYYY-MM-DD-xxxxxxx.zip`)
- [ ] **NE PAS télécharger** "Source code (zip)" ou "Source code (tar.gz)"
- [ ] Vérifier que le fichier ZIP contient bien le dossier `latelierkiyose/`

**Installation sur le serveur** :
- [ ] Se connecter au serveur via SFTP/SSH
- [ ] Naviguer vers `wp-content/themes/`
- [ ] Uploader le fichier ZIP
- [ ] Extraire l'archive : `unzip latelierkiyose-theme-*.zip`
- [ ] Vérifier que le dossier `wp-content/themes/latelierkiyose/` existe
- [ ] Vérifier les permissions : `chmod -R 755 latelierkiyose/`
- [ ] Supprimer le fichier ZIP : `rm latelierkiyose-theme-*.zip`

### Étape 3 — Vérification pré-activation

- [ ] Le thème apparaît dans Apparence > Thèmes
- [ ] La capture d'écran et les métadonnées sont correctes
- [ ] Aucune erreur PHP dans les logs

### Étape 4 — Activation

- [ ] Activer le thème dans Apparence > Thèmes
- [ ] Vérifier que le site s'affiche (même si le design n'est pas encore parfait)

### Étape 5 — Configuration post-activation

**Menus** :
- [ ] Aller dans Apparence > Menus
- [ ] Assigner le menu existant à l'emplacement "Menu Principal"
- [ ] Vérifier que le menu s'affiche dans le header

**Pages et templates** :
- [ ] Page d'accueil : assigner le template "Page d'accueil"
- [ ] Page Art-thérapie : assigner le template "Page de service"
- [ ] Page Rigologie : assigner le template "Page de service"
- [ ] Page Bols tibétains : assigner le template "Page de service"
- [ ] Page Ateliers philosophie : assigner le template "Page de service"
- [ ] Page Contact : assigner le template "Page de contact"
- [ ] Page À propos : assigner le template "À propos"
- [ ] Page Calendrier & Tarifs : assigner le template "Calendrier et tarifs"

**WordPress Settings** :
- [ ] Réglages > Lecture : vérifier que la page d'accueil statique est définie
- [ ] Réglages > Lecture : vérifier que la page de blog est définie
- [ ] Réglages > Permaliens : vérifier `/%postname%/`

**Logo** :
- [ ] Apparence > Personnaliser > Identité du site > Logo
- [ ] Uploader le logo SVG (ou PNG)
- [ ] Vérifier l'affichage dans le header

**Témoignages** :
- [ ] Ressaisir les témoignages existants dans le CPT Témoignages
- [ ] Vérifier leur affichage sur la homepage

**Shortcodes plugins** :
- [ ] Vérifier que les shortcodes Events Manager fonctionnent sur la page calendrier
- [ ] Vérifier que le shortcode CF7 fonctionne sur la page contact
- [ ] Vérifier que le shortcode Brevo fonctionne dans le footer et la homepage

### Étape 6 — Validation complète

Utiliser le script de validation automatique :

```bash
# Depuis le répertoire du thème sur le serveur
php bin/validate-production.php
```

Ou exécuter manuellement la checklist de `doc/validation-checklist.md` :

**Accessibilité** :
- [ ] axe DevTools : 0 violation sur toutes les pages (homepage, services, contact, à propos, calendrier, blog, single article)
- [ ] Navigation clavier complète testée sur toutes les pages
- [ ] Contrastes validés (WebAIM Contrast Checker)
- [ ] Skip link fonctionnel
- [ ] Formulaires accessibles (CF7, Brevo)
- [ ] Menu mobile accessible (focus trap, Esc, aria-labels)
- [ ] Carousel témoignages accessible (pause/play, flèches clavier)

**Performance** :
- [ ] Lighthouse Performance ≥ 90 sur toutes les pages principales
- [ ] LCP < 2.5s
- [ ] CLS < 0.1
- [ ] INP < 200ms
- [ ] Images optimisées et lazy loading activé
- [ ] Aucune erreur Console JavaScript

**Fonctionnel** :
- [ ] Homepage : hero, 4 piliers, témoignages, actualités, newsletter
- [ ] Pages de service : header hero, contenu, CTAs
- [ ] Page contact : formulaire CF7 + sidebar informations
- [ ] Page à propos : contenu + images
- [ ] Page calendrier : événements Events Manager affichés
- [ ] Blog : archive articles, single article, navigation
- [ ] Recherche fonctionnelle
- [ ] Page 404 fonctionnelle
- [ ] Tous les formulaires fonctionnent et envoient correctement
- [ ] Menu desktop et mobile fonctionnels
- [ ] Footer : réseaux sociaux, newsletter Brevo, liens légaux

**Responsive** :
- [ ] Test mobile 320px (iPhone SE)
- [ ] Test mobile 375px (iPhone 12/13)
- [ ] Test mobile 414px (iPhone Pro Max)
- [ ] Test tablet 768px (iPad portrait)
- [ ] Test desktop 1024px
- [ ] Test desktop 1440px
- [ ] Test desktop 1920px
- [ ] Aucun scroll horizontal
- [ ] Touch targets ≥ 44x44px

**Navigateurs** :
- [ ] Chrome (dernière version)
- [ ] Firefox (dernière version)
- [ ] Safari (dernière version)
- [ ] iOS Safari (iPhone/iPad)
- [ ] Chrome Android

**Conformité légale** :
- [ ] Mentions légales présentes et accessibles
- [ ] Politique de confidentialité présente
- [ ] SIRET affiché dans le footer
- [ ] Bannière cookies fonctionnelle (Complianz)
- [ ] CGV si applicable

### Étape 7 — Plan de rollback

En cas de problème grave :
1. Désactiver le nouveau thème dans l'admin WordPress
2. Réactiver l'ancien thème
3. Le contenu et les réglages restent intacts

Si l'admin n'est pas accessible :
1. Renommer le dossier `latelierkiyose/` sur le serveur
2. WordPress bascule automatiquement sur un thème par défaut
3. Investiguer le problème

## Hors périmètre

- Monitoring post-lancement (Google Analytics, Search Console) — configuration à faire par le client
- Formation utilisateur — à planifier séparément
- Optimisations de performance avancées post-lancement (CDN, cache avancé)
- Migration de contenu (le contenu existe déjà)
- Configuration des plugins tiers (Events Manager, Brevo, CF7, Complianz)

## Livrables

- ✅ PRD 0016 finalisé et documenté
- ✅ Script de validation pré-activation : `bin/validate-production.php`
- ✅ Guide de déploiement détaillé : `doc/deployment-guide.md`
- ✅ Checklist d'activation imprimable : incluse dans ce PRD

## Critères de succès

- [ ] Thème activé sur production sans erreur
- [ ] Toutes les pages affichées correctement
- [ ] 0 violation accessibilité sur axe DevTools
- [ ] Performance Lighthouse ≥ 90
- [ ] Tous les formulaires fonctionnels
- [ ] Navigation complète fonctionnelle
- [ ] Responsive validé sur tous les breakpoints
- [ ] Tests navigateurs validés
- [ ] Aucune régression de contenu

## Notes techniques

### Compatibilité plugins requis

Le thème nécessite les plugins suivants pour être pleinement fonctionnel :

- **Contact Form 7** : Formulaire de contact
- **Brevo (Sendinblue)** : Newsletter footer + homepage
- **Events Manager** : Calendrier et réservations
- **Complianz** : Bannière cookies RGPD (optionnel mais recommandé)
- **Yoast SEO** ou **Rank Math** : SEO (optionnel mais recommandé)

### Structure des menus

Le thème utilise un emplacement de menu :
- `primary` : Menu principal (header desktop + mobile)

### Templates personnalisés

Les templates suivants doivent être assignés manuellement :
- `templates/page-home.php` → Page d'accueil
- `templates/page-services.php` → Art-thérapie, Rigologie, Bols tibétains, Ateliers philosophie
- `templates/page-contact.php` → Page Contact
- `templates/page-about.php` → Page À propos
- `templates/page-calendar.php` → Page Calendrier & Tarifs

### Shortcodes utilisés

- `[kiyose_testimonials display="carousel"]` : Sur la homepage
- `[events_list]` ou autre shortcode Events Manager : Sur page calendrier
- `[contact-form-7 id="XXX"]` : Sur page contact
- `[brevo_form id="X"]` : Dans footer et homepage (section newsletter)
