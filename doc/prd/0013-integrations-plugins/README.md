# PRD 0013 — Intégrations plugins

## Statut

**terminé**

## Objectif

Intégrer visuellement les plugins tiers (Brevo newsletter, Contact Form 7, Events Manager) dans le design system du thème. Mettre en place le formulaire newsletter dans le footer et sur la homepage.

## Dépendances

- **PRD 0003** : Footer en place (emplacement newsletter).
- **PRD 0008** : Homepage (section newsletter).
- **PRD 0010** : Page contact avec CF7 (styles CF7 déjà en place).
- **PRD 0012** : Page calendrier avec Events Manager (styles EM déjà en place).

## Pré-requis

- **Brevo** : Plugin déjà installé et configuré dans l'environnement de production.
- **CF7** : Plugin installé (cf. PRD 0010). Styles déjà implémentés dans `cf7-override.css`.
- **Events Manager** : Plugin déjà installé (cf. PRD 0012). Styles déjà implémentés dans `events-manager.css`.

## Fichiers à créer/modifier

### Nouveaux fichiers

```
latelierkiyose/
└── assets/
    └── css/
        └── components/
            ├── brevo-override.css     # Override styles formulaire Brevo
            └── plugins-common.css     # Styles communs aux plugins
```

### Fichiers modifiés

- `footer.php` — Ajouter le shortcode Brevo dans la section newsletter
- `templates/page-home.php` — Ajouter le shortcode Brevo dans la section newsletter
- `inc/enqueue.php` — Charger les CSS d'override des plugins

## Spécifications détaillées

### Newsletter Brevo

**Intégration** :
Le formulaire Brevo est intégré via son shortcode WordPress (fourni par le plugin Brevo). 

**Shortcode à utiliser** :
```php
// Le shortcode exact sera fourni par le plugin Brevo après configuration
// Format typique : [brevo_form id="1"]
// Le shortcode sera placé dans :
// 1. footer.php — Section .site-footer__newsletter
// 2. templates/page-home.php — Section .home-newsletter__content
```

**Note importante** : Le shortcode Brevo n'existe pas tant que le plugin n'est pas installé et configuré. Pour l'implémentation :
- Si le plugin n'est pas disponible en local, utiliser un commentaire PHP avec le shortcode en placeholder
- Le shortcode sera ajouté manuellement en production après configuration du plugin

**Champs du formulaire (configuration Brevo)** :
- Email (requis)
- Prénom (requis)
- Nom (optionnel)
- Checkbox RGPD (consentement explicite)

**Styling** :
Le plugin Brevo génère son propre markup. Les styles doivent être overridés pour s'intégrer au design system :

**Classes CSS générées par Brevo** (typiques, à confirmer après installation) :
- `.sib-form` — Container principal du formulaire
- `.sib-form__label` — Labels des champs
- `.sib-form__input` — Champs de saisie (email, prénom, nom)
- `.sib-form__button` — Bouton submit
- `.sib-form__message` — Messages de succès/erreur
- `.sib-form__checkbox` — Wrapper checkbox RGPD

**Spécifications de styling** :
- **Champs** : 
  - Bordure: 1px solid var(--color-primary)
  - Focus: border var(--color-gold), outline 2px solid var(--color-gold)
  - Hauteur minimale: 44px (WCAG touch target)
  - Police: Nunito (var(--font-body))
- **Labels** : 
  - Visibles (pas de placeholder-only)
  - Police: Nunito, font-weight: 600
  - Couleur: var(--color-text)
- **Bouton submit** : 
  - Reprendre les styles `.button--primary` existants
  - Hauteur minimale: 44px
  - Couleur fond: var(--color-primary)
  - Couleur texte: var(--color-text) (contraste validé ≥4.5:1)
- **Messages de succès/erreur** :
  - Succès: couleur verte accessible (contraste ≥4.5:1)
  - Erreur: couleur rouge accessible (contraste ≥4.5:1)
  - Icon + texte (pas uniquement couleur pour distinguer)
- **Layout footer** : 
  - Formulaire compact
  - Desktop (≥768px): Champs inline (email + bouton sur une ligne)
  - Mobile (<768px): Champs empilés
- **Layout homepage** : 
  - Formulaire plus spacieux
  - Labels au-dessus des champs
  - Espacements généreux (padding, margin)

**RGPD** :
- Le double opt-in est géré côté Brevo (configuration du plugin, pas du thème)
- La checkbox de consentement doit être visible et accessible
- Label de la checkbox doit inclure le lien vers la politique de confidentialité

### Override CF7 (complément PRD 0010)

Les styles CF7 ont été implémentés dans `assets/css/components/cf7-override.css` (PRD 0010). Aucune modification supplémentaire n'est requise pour ce PRD.

**Référence** : Voir PRD 0010 pour les détails de l'implémentation CF7.

### Override Events Manager (complément PRD 0012)

Les styles Events Manager ont été implémentés dans `assets/css/components/events-manager.css` (PRD 0012). Aucune modification supplémentaire n'est requise pour ce PRD.

**Référence** : Voir PRD 0012 pour les détails de l'implémentation Events Manager.

### `plugins-common.css`

Styles partagés entre les overrides de plugins (Brevo, CF7, Events Manager).

**Objectif** : Centraliser les styles communs pour éviter la duplication de code entre `brevo-override.css`, `cf7-override.css`, et `events-manager.css`.

**Contenu** :
1. **Reset des styles de formulaires par défaut des plugins** :
   - Normalisation des `input`, `textarea`, `select`
   - Suppression des styles par défaut non souhaités (shadows, borders, backgrounds)

2. **Normalisation des boutons** :
   - Base commune pour tous les boutons de plugins
   - Application du design system (police, border-radius, transitions)

3. **Utilitaires d'accessibilité** :
   - Classes pour masquer visuellement mais garder pour lecteurs d'écran
   - Focus visible standardisé

4. **Masquage d'éléments non souhaités** :
   - Éléments générés par les plugins qu'on ne veut pas afficher (branding, powered by, etc.)

**Chargement** :
- Toujours chargé (global) car les plugins peuvent générer du markup partout
- Enqueue priorité haute (chargé avant les overrides spécifiques)

## Tests et validation

### Tests manuels — Fonctionnalité
- [ ] Le formulaire Brevo s'affiche dans le footer avec le bon styling
- [ ] Le formulaire Brevo s'affiche sur la homepage avec le bon styling
- [ ] L'inscription newsletter fonctionne (email reçu dans Brevo) — **Test en production uniquement**
- [ ] Le placeholder est visible en développement si le plugin n'est pas installé
- [ ] Les styles CF7 sont toujours fonctionnels (non-régression)
- [ ] Les styles Events Manager sont toujours fonctionnels (non-régression)

### Tests manuels — Accessibilité
- [ ] Tous les champs du formulaire Brevo ont des labels visibles
- [ ] Navigation clavier complète dans le formulaire Brevo (Tab, Shift+Tab, Enter)
- [ ] Focus visible sur tous les champs et boutons (outline 2px)
- [ ] Messages d'erreur accessibles (contraste ≥4.5:1, associés aux champs via ARIA si possible)
- [ ] Checkbox RGPD accessible (label cliquable, focus visible)
- [ ] Touch targets ≥44x44px sur tous les éléments interactifs

### Tests manuels — Responsive
- [ ] Formulaire newsletter compact dans le footer sur mobile (<768px)
- [ ] Formulaire newsletter inline dans le footer sur desktop (≥768px)
- [ ] Formulaire newsletter spacieux sur la homepage (tous breakpoints)
- [ ] Champs empilés sur mobile, inline sur desktop (footer uniquement)
- [ ] Aucun débordement horizontal sur mobile (320px+)

### Tests manuels — Contrastes
- [ ] Texte des labels : contraste ≥4.5:1
- [ ] Texte des champs : contraste ≥4.5:1
- [ ] Texte du bouton submit : contraste ≥4.5:1
- [ ] Bordures des champs : contraste ≥3:1
- [ ] Messages de succès : contraste ≥4.5:1
- [ ] Messages d'erreur : contraste ≥4.5:1

### Tests automatisés
- [ ] PHPCS passe sans erreurs ni warnings
- [ ] Aucune erreur JavaScript dans la console (si JS Brevo présent)

## Hors périmètre

- Configuration des plugins (Brevo, CF7, Events Manager) — manuel dans wp-admin
- Création du formulaire Brevo dans l'interface du plugin — manuel
- Configuration du double opt-in Brevo — paramètre du plugin
- Installation du plugin Brevo en environnement de développement local — optionnel
- Tests fonctionnels d'envoi d'email Brevo — en production uniquement (nécessite API Brevo configurée)
