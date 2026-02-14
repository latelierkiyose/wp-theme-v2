# PRD 0017 — Signets de navigation (Anchor Links Menu)

## Statut

**terminé**

## Objectif

Créer un shortcode `[kiyose_signets]` pour générer un menu de navigation par ancres (signets) sur les pages de services, remplaçant les blocs Gutenberg colonnes (`div.signets`) par une solution sémantique, accessible et responsive.

## Contexte

Les pages de services (art-thérapie, rigologie, bols tibétains, ateliers philosophie) utilisent actuellement des blocs Gutenberg colonnes avec la classe `signets` pour créer un menu de navigation par ancres vers les sections H2 de la page.

**Problèmes de la solution actuelle:**
- Structure HTML non sémantique (`div` au lieu de `nav`, `ul`, `li`)
- Markup lourd et difficile à maintenir dans l'éditeur Gutenberg
- Pas de génération automatique des ancres (IDs ajoutés manuellement)
- Style de boutons trop lourds (CTA au lieu de navigation légère)

**Objectifs de la refonte:**
- Shortcode simple à utiliser dans l'éditeur
- HTML sémantique (`<nav>`, `<ul>`, `<li>`, `<a>`)
- Auto-génération des IDs sur les H2 (si absents)
- Design léger et subtil (navigation, pas CTA)
- Responsive et accessible WCAG 2.2 AA

## Dépendances

- **PRD 0002** : Design tokens (variables CSS).
- **PRD 0009** : Template services (pages utilisant les signets).

## Fichiers à créer/modifier

### Nouveaux fichiers

```
latelierkiyose/
├── inc/
│   └── content-filters.php              # Filtre auto-génération IDs H2
├── assets/
│   └── css/
│       └── components/
│           └── signets.css              # Styles du menu signets
```

### Fichiers modifiés

- `inc/shortcodes.php` — Ajout fonction `kiyose_signets_shortcode()`
- `inc/enqueue.php` — Ajout fonction `kiyose_enqueue_signets_styles()`
- `functions.php` — Ajout `require_once` de `inc/content-filters.php`, version bump
- `tests/test-shortcodes.php` — Ajout tests PHPUnit pour le shortcode signets
- `doc/deployment-guide.md` — Ajout section 3.9 migration des signets

## Spécifications détaillées

### Shortcode `[kiyose_signets]`

**Fichier**: `inc/shortcodes.php` (lignes 124-203)

**Deux modes d'utilisation:**

#### Mode 1: Auto-scan (recommandé)

Scanne automatiquement tous les titres H2 de la page pour générer les liens:

```
[kiyose_signets][/kiyose_signets]
```

**Comportement:**
- Parse le contenu de la page avec regex: `/<h2[^>]*id=["\']([^"\']+)["\'][^>]*>(.*?)<\/h2>/i`
- Extrait l'attribut `id` et le texte du H2
- Génère un lien pour chaque H2 trouvé
- Retourne vide si aucun H2 avec ID trouvé

#### Mode 2: Manuel

Spécifie manuellement les liens (un par ligne, format `Texte | #anchor`):

```
[kiyose_signets]
Comment ça marche ? | #mademarche
Bienfaits | #bienfaits
Pour qui ? | #pourqui
Déroulé d'une séance | #deroulement
[/kiyose_signets]
```

**Format:**
- Une ligne = un lien
- Séparateur: ` | ` (espace pipe espace)
- Partie gauche: Texte du lien (affiché)
- Partie droite: Ancre cible (avec `#`)
- Lignes vides ignorées

**Validation:**
- Texte sanitizé avec `sanitize_text_field()`
- Ancre sanitizée avec `esc_url()`
- Lignes invalides ignorées (pas de crash)

### Auto-génération des IDs H2

**Fichier**: `inc/content-filters.php`

**Fonction**: `kiyose_auto_add_h2_ids( $content )`

**Comportement:**
- Filtre `the_content` avec priorité 10
- Parse tous les H2 du contenu
- Ajoute un `id` aux H2 qui n'en ont pas
- ID généré: `sanitize_title( $heading_text )`
- Évite les doublons avec suffixe numérique (`-2`, `-3`, etc.)
- Non invasif (ne modifie pas la base de données)

**Exemples de génération:**

| Texte H2 | ID généré |
|----------|-----------|
| `Comment ça marche ?` | `comment-ca-marche` |
| `Bienfaits` | `bienfaits` |
| `Pour qui ?` | `pour-qui` |
| `Bienfaits` (doublon) | `bienfaits-2` |

### HTML généré

**Structure sémantique:**

```html
<nav class="kiyose-signets" aria-label="Navigation par sections">
    <ul class="kiyose-signets__list">
        <li class="kiyose-signets__item">
            <a href="#mademarche" class="kiyose-signets__link">
                Comment ça marche ?
            </a>
        </li>
        <li class="kiyose-signets__item">
            <a href="#bienfaits" class="kiyose-signets__link">
                Bienfaits
            </a>
        </li>
        <!-- ... -->
    </ul>
</nav>
```

**Éléments d'accessibilité:**
- `<nav>` avec `aria-label="Navigation par sections"`
- Structure liste sémantique (`<ul>`, `<li>`)
- Liens avec `href` valides (ancres avec `#`)
- Classes BEM pour styling

### Styles CSS

**Fichier**: `assets/css/components/signets.css`

**Design tokens utilisés:**
- `--color-taupe`: Couleur de texte par défaut
- `--color-primary`: Couleur au hover
- `--color-light-beige`: Fond léger
- `--color-medium-beige`: Bordure au hover
- `--font-size-body`: Base 18px
- `--font-weight-semibold`: 600
- `--spacing-md`: 1.5rem
- `--spacing-sm`: 1rem
- `--border-radius`: 0.5rem
- `--transition-speed`: 0.3s

**Layout responsive:**

| Breakpoint | Layout | Colonnes |
|------------|--------|----------|
| < 768px (mobile) | Stack vertical | 1 (100% width) |
| ≥ 768px (desktop) | Grille horizontale | 4 max par ligne (25% width) |

**Desktop:**
- Flexbox avec `flex-wrap: wrap`
- `justify-content: space-between` (justification totale)
- `align-items: stretch` (hauteurs égales par ligne)
- Gap: 1.5rem
- Largeur items: `calc(25% - 1.125rem)` (4 colonnes)

**Mobile:**
- Flexbox colonne (`flex-direction: column`)
- Largeur items: 100%
- Gap: 1rem

**Styling des boutons:**

| État | Border | Background | Text | Transition |
|------|--------|------------|------|------------|
| Default | `border-bottom: 2px solid rgba(taupe, 0.2)` | `rgba(beige, 0.5)` | `--color-taupe` | 0.3s |
| Hover/Focus | `border: 2px solid --color-medium-beige` (4 côtés) | `rgba(beige, 0.8)` | `--color-primary` | 0.3s |
| Active | Idem hover | Idem hover | Idem hover | Instantané |

**Typographie:**
- Font-size: 18px
- Font-weight: 600 (semi-bold)
- Line-height: 1.4
- Text-align: center
- Padding: 1rem 1.5rem

**Accessibilité:**
- Focus visible: outline 2px solid primary + offset 2px
- `:focus-visible` (pas `:focus` pour éviter outline au clic)
- `prefers-reduced-motion`: Désactive transitions
- Touch targets: min 44x44px (WCAG 2.2 AA)

**Fixes WordPress:**
- `list-style: none !important` (supprime bullets par défaut)
- `padding-left: 0 !important` (supprime padding UL par défaut)
- `margin: 0` sur UL et LI

### Enqueue conditionnel

**Fichier**: `inc/enqueue.php` (lignes 292-310)

**Fonction**: `kiyose_enqueue_signets_styles()`

**Comportement:**
- Hook: `wp_enqueue_scripts`, priorité 20
- Charge `signets.css` uniquement si shortcode `[kiyose_signets]` présent dans le contenu
- Détection: `has_shortcode( get_post()->post_content, 'kiyose_signets' )`
- Version: `filemtime()` pour cache busting
- Dépendances: aucune (styles standalone)

**Optimisation performance:**
- Pas de chargement global (uniquement pages avec shortcode)
- CSS minifié en production (à venir)

## Tests

**Fichier**: `tests/test-shortcodes.php` (lignes 192-279)

**6 tests PHPUnit:**

1. `test_kiyose_signets_shortcode_registered()`
   - Vérifie que le shortcode est enregistré dans WordPress

2. `test_kiyose_signets_shortcode_renders_manual_content()`
   - Teste le parsing manuel du contenu avec format `Text | #anchor`
   - Vérifie la génération du HTML sémantique
   - Valide les classes BEM et attributs ARIA

3. `test_kiyose_signets_shortcode_auto_scans_h2_headings()`
   - Teste le mode auto-scan (contenu vide)
   - Mock de `get_post()` avec contenu H2
   - Vérifie l'extraction correcte des IDs et textes H2

4. `test_kiyose_signets_shortcode_returns_empty_when_no_links()`
   - Vérifie retour vide si aucun lien trouvé
   - Cas 1: Contenu manuel vide
   - Cas 2: Aucun H2 dans le post

5. `test_kiyose_signets_shortcode_handles_empty_lines()`
   - Teste la robustesse face aux lignes vides
   - Lignes vides doivent être ignorées sans erreur

6. `test_kiyose_signets_shortcode_sanitizes_input()`
   - Teste la sécurité contre XSS
   - Vérifie `sanitize_text_field()` et `esc_url()`
   - HTML malveillant doit être échappé

**Validation PHPCS:**
- ✅ 0 erreurs, 0 warnings
- Respecte WordPress Coding Standards

## Accessibilité WCAG 2.2 AA

**Conformité:**

| Critère | Niveau | Conformité | Implémentation |
|---------|--------|------------|----------------|
| 1.3.1 Info et relations | A | ✅ | Markup sémantique (`<nav>`, `<ul>`, `<li>`) |
| 1.4.3 Contraste minimum | AA | ✅ | Contraste ≥4.5:1 (taupe sur beige) |
| 2.1.1 Clavier | A | ✅ | Navigation complète Tab/Shift+Tab/Entrée |
| 2.4.1 Contourner des blocs | A | ✅ | `<nav>` avec `aria-label` |
| 2.4.4 Fonction du lien | A | ✅ | Textes de liens descriptifs |
| 2.5.5 Taille de la cible | AA | ✅ | Targets min 44x44px (padding 1rem 1.5rem) |
| 3.2.4 Identification cohérente | AA | ✅ | Même pattern sur toutes les pages |
| 4.1.2 Nom, rôle, valeur | A | ✅ | Rôles ARIA implicites (nav, list, link) |

**Tests manuels requis:**
- ✅ Navigation clavier (Tab, Entrée)
- ✅ Lecteur d'écran (annonce "Navigation par sections", liste de X liens)
- ✅ Zoom 200% (lisibilité maintenue)
- ✅ Responsive 320px+ (stack mobile)

## Migration depuis Gutenberg

**Avant (Gutenberg colonnes):**

```html
<!-- wp:columns {"className":"signets"} -->
<div class="wp-block-columns signets">
    <!-- wp:column -->
    <div class="wp-block-column">
        <a href="#mademarche" class="wp-block-button__link">Comment ça marche ?</a>
    </div>
    <!-- wp:column -->
    <div class="wp-block-column">
        <a href="#bienfaits" class="wp-block-button__link">Bienfaits</a>
    </div>
    <!-- ... -->
</div>
<!-- /wp:columns -->
```

**Après (Shortcode):**

```
[kiyose_signets][/kiyose_signets]
```

**Procédure de migration:**

1. Identifier les pages avec `div.signets` (pages de services)
2. Pour chaque page:
   - Vérifier que les H2 ont des IDs (sinon auto-générés)
   - Remplacer le bloc colonnes par `[kiyose_signets][/kiyose_signets]`
   - Tester la navigation par ancres
3. Supprimer les styles CSS `.signets` (deprecated)

**Documentation complète:** `doc/deployment-guide.md` section 3.9

## Critères d'acceptance

- ✅ Shortcode `[kiyose_signets]` enregistré et fonctionnel
- ✅ Mode auto-scan extrait tous les H2 avec IDs
- ✅ Mode manuel parse le format `Text | #anchor`
- ✅ Auto-génération des IDs H2 (filtre `the_content`)
- ✅ HTML sémantique (`<nav>`, `<ul>`, `<li>`, `<a>`)
- ✅ Layout responsive (4 colonnes desktop, stack mobile)
- ✅ Boutons hauteur égale sur même ligne (stretch + flex)
- ✅ Bordures subtiles (bottom par défaut, full au hover)
- ✅ Enqueue conditionnel (performance)
- ✅ 6 tests PHPUnit passent (100% coverage critique)
- ✅ PHPCS: 0 erreurs, 0 warnings
- ✅ Accessibilité WCAG 2.2 AA (contraste, clavier, ARIA, touch targets)
- ✅ Documentation migration complète

## Notes techniques

### Regex H2 extraction

**Pattern utilisé:**
```php
'/<h2[^>]*id=["\']([^"\']+)["\'][^>]*>(.*?)<\/h2>/i'
```

**Améliorations vs. version initiale:**
- Capture l'ID quelle que soit sa position dans les attributs
- Supporte guillemets simples et doubles
- Non-greedy matching sur le contenu (`.*?`)
- Case-insensitive (`/i` flag)

### Gestion des doublons d'IDs

**Algorithme:**
```php
$used_ids = array();
foreach ( $matches[2] as $index => $heading_text ) {
    $base_id = sanitize_title( $heading_text );
    $id      = $base_id;
    $counter = 2;
    while ( in_array( $id, $used_ids, true ) ) {
        $id = $base_id . '-' . $counter;
        ++$counter;
    }
    $used_ids[] = $id;
}
```

**Comportement:**
- Premiers ID: `bienfaits`
- Doublon 1: `bienfaits-2`
- Doublon 2: `bienfaits-3`
- Évite les collisions infinies

### Fixes CSS `!important`

**Raison:**
WordPress charge des styles par défaut pour `<ul>` qui ajoutent:
- `list-style-type: disc` (bullets)
- `padding-left: 32px`

**Solution:**
```css
.kiyose-signets__list {
    list-style: none !important;
    padding-left: 0 !important;
}
```

`!important` nécessaire pour override les styles WordPress chargés après.

### Hauteurs égales des boutons

**Problème initial:**
Boutons sur la même ligne avaient des hauteurs différentes quand le texte wrappait.

**Solution:**
```css
.kiyose-signets__list {
    align-items: stretch; /* Force tous les enfants à même hauteur */
}

.kiyose-signets__item {
    display: flex; /* Permet au lien enfant de prendre toute la hauteur */
}

.kiyose-signets__link {
    display: flex;
    align-items: center; /* Centre le texte verticalement */
    justify-content: center;
}
```

## Évolutions futures possibles

- [ ] Support des H3 en sous-navigation (hiérarchie à 2 niveaux)
- [ ] Option `order` pour tri personnalisé
- [ ] Smooth scroll avec offset pour header sticky
- [ ] Indicateur visuel de section active (scroll spy)
- [ ] Option `exclude` pour masquer certaines sections
- [ ] Mode "compact" pour mobile (dropdown au lieu de stack)

## Références

- **Exemple live:** http://localhost:8000/art-therapie/
- **WCAG 2.2:** https://www.w3.org/WAI/WCAG22/quickref/
- **WordPress Shortcode API:** https://developer.wordpress.org/plugins/shortcodes/
- **BEM Methodology:** https://getbem.com/
