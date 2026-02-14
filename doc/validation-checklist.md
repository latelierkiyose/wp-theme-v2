# Checklist de Validation

Utilisez cette checklist pour valider chaque fonctionnalité et avant la mise en production.

## Accessibilité (WCAG 2.2 AA)

### Contraste de couleurs
- [ ] Tous les textes normaux ont un contraste ≥ 4.5:1
- [ ] Tous les textes larges (18pt+/14pt+ gras) ont un contraste ≥ 3:1
- [ ] Tous les composants UI ont un contraste ≥ 3:1
- [ ] Testé avec WebAIM Contrast Checker
- [ ] Information jamais transmise uniquement par la couleur

### Navigation clavier
- [ ] Tous les éléments interactifs accessibles avec Tab
- [ ] Focus visible sur tous les éléments interactifs
- [ ] Ordre de tabulation logique (suit l'ordre visuel)
- [ ] Liens et boutons activables avec Entrée/Espace
- [ ] Menus déroulants navigables avec flèches
- [ ] Modales/surcouches fermables avec Échap
- [ ] Piège de focus dans les modales quand ouvertes
- [ ] Focus retourne au déclencheur à la fermeture de modale
- [ ] Aucun piège clavier

### Structure sémantique et ARIA
- [ ] Un seul `<h1>` par page
- [ ] Hiérarchie des titres logique (pas de niveau sauté)
- [ ] Landmarks ARIA présents (`<nav>`, `<main>`, `<aside>`, `<footer>`)
- [ ] Rôles ARIA appropriés où nécessaire
- [ ] `aria-label` ou `aria-labelledby` sur éléments interactifs sans étiquette visible
- [ ] Lien d'évitement (skip link) présent et fonctionnel

### Images
- [ ] Toutes les images ont un attribut `alt`
- [ ] Images porteuses d'information ont `alt` descriptif
- [ ] Images décoratives ont `alt=""` (vide)
- [ ] Logos ont `alt` avec nom de l'entreprise
- [ ] Aucune image de texte (sauf logos)

### Formulaires
- [ ] Tous les champs ont des `<label>` explicites associées
- [ ] Étiquettes visibles (pas seulement placeholders)
- [ ] Champs requis indiqués visuellement et avec `aria-required="true"`
- [ ] Messages d'erreur associés avec `aria-describedby`
- [ ] Messages d'erreur avec `role="alert"` ou `aria-live="polite"`
- [ ] Champs invalides marqués avec `aria-invalid="true"`
- [ ] Instructions claires avant les champs

### Liens et navigation
- [ ] Textes de liens descriptifs (pas "cliquez ici")
- [ ] Liens visuellement distincts du texte normal
- [ ] Liens visités distincts des liens non-visités
- [ ] Liens externes avec indication (icône + `aria-label`)

### Animations et mouvement
- [ ] Aucune animation automatique (autoplay) sans contrôle utilisateur
- [ ] Animations respectent `prefers-reduced-motion`
- [ ] Aucun contenu clignotant > 3 fois par seconde
- [ ] Carrousels avec contrôles pause/lecture

### Mobile et responsive
- [ ] Site fonctionnel à 320px de largeur
- [ ] Zoom jusqu'à 200% sans perte de fonctionnalité
- [ ] Pas de défilement horizontal
- [ ] Cibles tactiles minimum 44x44px
- [ ] Texte redimensionnable sans casser la mise en page

## Performance (Core Web Vitals)

### Métriques
- [ ] LCP (Largest Contentful Paint) < 2.5s
- [ ] INP (Interaction to Next Paint) < 200ms
- [ ] CLS (Cumulative Layout Shift) < 0.1
- [ ] TTFB (Time to First Byte) < 800ms
- [ ] Score Lighthouse Performance ≥ 90

### Optimisations
- [ ] Images optimisées (WebP avec repli)
- [ ] Images avec `srcset` et `sizes`
- [ ] Chargement différé (lazy loading) sur images sous la ligne de flottaison
- [ ] CSS minifié en production
- [ ] JavaScript minifié en production
- [ ] JavaScript non-critique chargé en `defer` ou `async`
- [ ] Polices auto-hébergées (pas de CDN externe)
- [ ] Polices préchargées avec `<link rel="preload">`
- [ ] Pas de jQuery (sauf si absolument nécessaire)
- [ ] Requêtes HTTP minimisées

## Qualité du code (WordPress)

### WordPress Coding Standards
- [ ] Indentation avec tabs (pas espaces)
- [ ] Toutes les fonctions préfixées avec `kiyose_`
- [ ] Hooks custom préfixés avec `kiyose_`
- [ ] Text domain `kiyose` utilisé partout
- [ ] Classes CSS en BEM
- [ ] Code validé avec PHP_CodeSniffer (WPCS)

### Sécurité
- [ ] Toutes les entrées utilisateur sanitisées
- [ ] Toutes les sorties échappées (`esc_html()`, `esc_attr()`, `esc_url()`)
- [ ] Nonces utilisés pour tous les formulaires
- [ ] Nonces vérifiés avant traitement
- [ ] Instructions préparées (prepared statements) pour requêtes DB
- [ ] Capacités vérifiées pour actions admin
- [ ] Pas de secrets dans le code (clés API, mots de passe)

### Documentation
- [ ] Fonctions documentées avec PHPDoc
- [ ] Paramètres et types documentés
- [ ] `@since` version indiquée
- [ ] Commentaires pour code complexe uniquement

## Tests

### Tests automatisés
- [ ] Tests unitaires PHPUnit pour fonctions critiques
- [ ] Tests PHPUnit passent tous (100%)
- [ ] Tests accessibilité axe DevTools (0 violations)
- [ ] Tests accessibilité pa11y passent

### Tests manuels - Navigateurs
- [ ] Chrome (dernière version)
- [ ] Firefox (dernière version)
- [ ] Safari (dernière version)
- [ ] iOS Safari (iOS récent)
- [ ] Chrome Android (version récente)

### Tests manuels - Accessibilité
- [ ] Navigation clavier complète testée
- [ ] Lecteur d'écran testé (NVDA ou VoiceOver)
- [ ] Zoom 200% testé
- [ ] Mode contraste élevé testé (Windows)

### Tests manuels - Responsive
- [ ] Mobile 320px
- [ ] Mobile 375px (iPhone SE)
- [ ] Mobile 414px (iPhone Pro Max)
- [ ] Tablet 768px (iPad portrait)
- [ ] Desktop 1024px
- [ ] Desktop 1440px
- [ ] Desktop 1920px

### Tests performance
- [ ] Lighthouse audit exécuté
- [ ] PageSpeed Insights consulté
- [ ] Temps de chargement < 3s (4G)

### Validation
- [ ] HTML validé (W3C Validator) - 0 erreur
- [ ] CSS validé (W3C CSS Validator)
- [ ] Console navigateur - 0 erreur JavaScript

## Fonctionnalités WordPress

### Configuration thème
- [ ] `style.css` avec métadonnées complètes
- [ ] `screenshot.png` présent (1200x900px)
- [ ] Fonctions dans `inc/` chargées depuis `functions.php`
- [ ] Supports de thème enregistrés (`title-tag`, `post-thumbnails`, etc.)
- [ ] Menus enregistrés
- [ ] Tailles d'images personnalisées déclarées

### Templates
- [ ] Tous les templates ont en-têtes appropriées
- [ ] `index.php` présent (fallback)
- [ ] Templates personnalisés assignables dans admin
- [ ] Parties de templates réutilisables

### Types de contenu personnalisés
- [ ] CPT enregistrés correctement
- [ ] Étiquettes complètes et traduisibles
- [ ] Capacités appropriées
- [ ] Archives et templates single si nécessaire

## Contenu et UX

### Contenu
- [ ] Textes relus (orthographe, grammaire)
- [ ] Tous les placeholders remplacés
- [ ] Images avec dimensions appropriées
- [ ] Tous les liens fonctionnels
- [ ] Pas de Lorem Ipsum

### Formulaires
- [ ] Tous les formulaires testés (soumission réussie)
- [ ] Messages de succès affichés
- [ ] Messages d'erreur clairs et affichés
- [ ] Validation côté serveur (pas seulement client)
- [ ] Protection anti-spam (reCAPTCHA ou honeypot)
- [ ] RGPD: consentement explicite pour newsletter

### Navigation
- [ ] Menu principal complet et fonctionnel
- [ ] Menu déroulant accessible au clavier
- [ ] Menu mobile hamburger fonctionnel
- [ ] Fil d'ariane si applicable
- [ ] Pied de page avec tous les liens légaux

## Conformité légale (France)

### RGPD
- [ ] Politique de confidentialité présente
- [ ] Bannière cookies (plugin Complianz ou similaire)
- [ ] Formulaires avec consentement explicite
- [ ] Double opt-in pour newsletter

### Mentions légales
- [ ] Page mentions légales présente
- [ ] SIRET affiché
- [ ] Adresse affichée
- [ ] Hébergeur mentionné
- [ ] Responsable de publication mentionné

## Avant mise en production

### Pré-lancement
- [ ] Toutes les checklist ci-dessus validées
- [ ] Tests sur environnement staging
- [ ] Sauvegarde complète du site
- [ ] Plan de rollback préparé

### Configuration WordPress
- [ ] Permaliens configurés (`/%postname%/`)
- [ ] Page d'accueil statique définie
- [ ] Page blog définie
- [ ] Timezone configurée
- [ ] Visibilité moteurs recherche activée

### SEO
- [ ] Plugin SEO installé et configuré (Yoast ou Rank Math)
- [ ] Meta descriptions rédigées
- [ ] Balises title optimisées
- [ ] Sitemap XML généré
- [ ] robots.txt configuré

### Sécurité production
- [ ] Plugin de sécurité installé (Wordfence ou iThemes)
- [ ] Authentification à deux facteurs (2FA) activée pour admin
- [ ] Mots de passe forts pour tous les comptes
- [ ] Préfixe DB changé (si nouvelle installation)
- [ ] SSL/HTTPS activé
- [ ] Sauvegardes automatiques configurées (UpdraftPlus)

### Performance production
- [ ] Plugin cache installé (WP Rocket ou Autoptimize)
- [ ] Minification CSS/JS activée
- [ ] Compression images automatique (ShortPixel ou Imagify)
- [ ] CDN configuré si applicable
- [ ] Cache navigateur configuré

## Post-lancement

### Monitoring
- [ ] Google Analytics ou alternative configurée
- [ ] Google Search Console configurée
- [ ] Uptime monitoring activé
- [ ] Erreurs 404 surveillées

### Maintenance
- [ ] Plan de mise à jour défini (WordPress, thème, plugins)
- [ ] Contacts support identifiés
- [ ] Documentation utilisateur fournie au client
