# PRD 0016 — Activation et pré-production

## Objectif

Documenter la procédure d'activation du thème sur le site de production, avec la checklist complète de validation pré-lancement.

## Dépendances

- **Toutes les étapes précédentes** : Le thème doit être complet et testé.

## Contexte

Le site WordPress existe déjà avec du contenu (pages, articles, événements). L'activation du nouveau thème remplace uniquement la couche de présentation. Les contenus restent en base de données.

## Procédure d'activation

### Étape 1 — Sauvegarde

- [ ] Sauvegarde complète de la base de données
- [ ] Sauvegarde des fichiers du site (wp-content)
- [ ] Vérifier que la sauvegarde est restaurable

### Étape 2 — Installation du thème

- [ ] Télécharger l'artefact ZIP depuis GitHub Actions (dernière version validée par la CI)
- [ ] Uploader l'artefact dans `wp-content/themes/` via SFTP
- [ ] Extraire l'archive
- [ ] Vérifier que le dossier `latelierkiyose/` est présent dans `wp-content/themes/`

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

Exécuter la checklist de `doc/validation-checklist.md` :

**Accessibilité** :
- [ ] axe DevTools : 0 violation sur les pages principales
- [ ] Navigation clavier complète testée
- [ ] Contrastes validés
- [ ] Skip link fonctionnel
- [ ] Formulaires accessibles

**Performance** :
- [ ] Lighthouse Performance ≥ 90
- [ ] LCP < 2.5s
- [ ] CLS < 0.1
- [ ] INP < 200ms

**Fonctionnel** :
- [ ] Toutes les pages s'affichent correctement
- [ ] Tous les formulaires fonctionnent (contact, newsletter, réservation)
- [ ] Menu desktop et mobile fonctionnels
- [ ] Recherche fonctionnelle
- [ ] Page 404 fonctionnelle
- [ ] Blog et articles fonctionnels
- [ ] Événements Events Manager fonctionnels

**Responsive** :
- [ ] Test sur mobile 320px, 375px, 414px
- [ ] Test sur tablet 768px
- [ ] Test sur desktop 1024px, 1440px, 1920px

**Navigateurs** :
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] iOS Safari
- [ ] Chrome Android

**Conformité légale** :
- [ ] Mentions légales présentes et accessibles
- [ ] Politique de confidentialité présente
- [ ] SIRET affiché dans le footer
- [ ] Bannière cookies fonctionnelle (Complianz)

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

- Monitoring post-lancement (Google Analytics, Search Console)
- Formation utilisateur
- Optimisations de performance avancées post-lancement
