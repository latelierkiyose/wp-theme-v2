# PRD 0007b — Affichage des témoignages

## Statut

**terminé**

## Objectif

Créer un shortcode permettant d'afficher les témoignages sous forme de grille responsive ou de carousel accessible sur n'importe quelle page WordPress (notamment la page témoignages dédiée).

## Contexte

PRD 0007 a créé le CPT `kiyose_testimony` et le template part `content-testimony.php` pour afficher un témoignage. Ce PRD ajoute la logique d'affichage (shortcode) pour présenter plusieurs témoignages dans une mise en page flexible.

**Cas d'usage:**
- Page dédiée "Témoignages" avec tous les témoignages en grille
- Section homepage avec carousel (PRD 0008)
- Toute autre page nécessitant l'affichage de témoignages

## Dépendances

- **PRD 0005** : Templates de base (layout page).
- **PRD 0007** : CPT Témoignages (`content-testimony.php` et styles `testimony.css`).

## Fichiers à créer/modifier

### Nouveaux fichiers

```
latelierkiyose/
├── inc/
│   └── shortcodes.php                    # Déclaration des shortcodes
├── assets/
│   └── css/
│       └── components/
│           └── testimonials-grid.css     # Styles grille de témoignages
```

### Fichiers modifiés

- `functions.php` — Ajouter le `require_once` de `inc/shortcodes.php`

## Spécifications détaillées

### Shortcode `[kiyose_testimonials]`

**Fichier**: `inc/shortcodes.php`

**Paramètres:**

| Attribut | Type | Valeur par défaut | Description |
|----------|------|-------------------|-------------|
| `display` | string | `grid` | Mode d'affichage: `grid` ou `carousel` (PRD 0008) |
| `limit` | int | `-1` | Nombre de témoignages à afficher (`-1` = tous) |
| `context` | string | `''` | Filtrer par contexte: `individual`, `enterprise`, `structure` (vide = tous) |
| `columns` | int | `3` | Nombre de colonnes (mode `grid` uniquement) |

**Exemples d'utilisation:**

```php
// Tous les témoignages en grille 3 colonnes (défaut)
[kiyose_testimonials]

// 6 témoignages en grille 2 colonnes
[kiyose_testimonials limit="6" columns="2"]

// Témoignages entreprises uniquement
[kiyose_testimonials context="enterprise"]

// Mode carousel (PRD 0008)
[kiyose_testimonials display="carousel"]
```

### Fonction `kiyose_testimonials_shortcode( $atts )`

**Implémentation:**

```php
function kiyose_testimonials_shortcode( $atts ) {
    // Sanitize et merge des attributs
    $args = shortcode_atts(
        array(
            'display' => 'grid',
            'limit'   => -1,
            'context' => '',
            'columns' => 3,
        ),
        $atts,
        'kiyose_testimonials'
    );

    // Sanitization
    $display = sanitize_key( $args['display'] );
    $limit   = intval( $args['limit'] );
    $context = sanitize_key( $args['context'] );
    $columns = intval( $args['columns'] );

    // Validation
    if ( ! in_array( $display, array( 'grid', 'carousel' ), true ) ) {
        $display = 'grid';
    }
    if ( $columns < 1 || $columns > 4 ) {
        $columns = 3;
    }

    // WP_Query
    $query_args = array(
        'post_type'      => 'kiyose_testimony',
        'posts_per_page' => $limit,
        'orderby'        => 'rand', // Ordre aléatoire pour varier l'affichage
        'post_status'    => 'publish',
    );

    // Filtrer par contexte si spécifié
    if ( ! empty( $context ) ) {
        $query_args['meta_query'] = array(
            array(
                'key'   => 'kiyose_testimony_context',
                'value' => $context,
            ),
        );
    }

    $testimonials_query = new WP_Query( $query_args );

    // Début output buffering
    ob_start();

    if ( $testimonials_query->have_posts() ) {
        if ( 'grid' === $display ) {
            // Affichage grille
            echo '<div class="testimonials-grid testimonials-grid--columns-' . esc_attr( $columns ) . '">';
            while ( $testimonials_query->have_posts() ) {
                $testimonials_query->the_post();
                get_template_part( 'template-parts/content', 'testimony' );
            }
            echo '</div>';
        } elseif ( 'carousel' === $display ) {
            // Affichage carousel (PRD 0008)
            // TODO: Implémenter dans PRD 0008
            echo '<!-- Carousel mode: PRD 0008 -->';
        }
    } else {
        echo '<p class="testimonials-empty">' . esc_html__( 'Aucun témoignage disponible pour le moment.', 'kiyose' ) . '</p>';
    }

    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode( 'kiyose_testimonials', 'kiyose_testimonials_shortcode' );
```

**Points clés:**
- Sanitization complète de tous les attributs
- Validation des valeurs (`display`, `columns`)
- `WP_Query` avec support meta_query pour filtrer par contexte
- Ordre aléatoire (`orderby => 'rand'`) pour varier l'affichage à chaque chargement
- Output buffering pour retourner le HTML proprement
- Support mode `carousel` prévu (implémenté dans PRD 0008)
- Message vide i18n si aucun témoignage trouvé
- `wp_reset_postdata()` obligatoire après `WP_Query`

### Styles CSS (`testimonials-grid.css`)

**Grille responsive:**

```css
/* Grille de témoignages */
.testimonials-grid {
    display: grid;
    gap: var(--spacing-lg);
    margin-block: var(--spacing-xl);
}

/* Colonnes responsive */
.testimonials-grid--columns-1 {
    grid-template-columns: 1fr;
}

.testimonials-grid--columns-2 {
    grid-template-columns: 1fr;
}

@media (min-width: 768px) {
    .testimonials-grid--columns-2 {
        grid-template-columns: repeat(2, 1fr);
    }
}

.testimonials-grid--columns-3 {
    grid-template-columns: 1fr;
}

@media (min-width: 768px) {
    .testimonials-grid--columns-3 {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .testimonials-grid--columns-3 {
        grid-template-columns: repeat(3, 1fr);
    }
}

.testimonials-grid--columns-4 {
    grid-template-columns: 1fr;
}

@media (min-width: 768px) {
    .testimonials-grid--columns-4 {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .testimonials-grid--columns-4 {
        grid-template-columns: repeat(4, 1fr);
    }
}

/* Message vide */
.testimonials-empty {
    text-align: center;
    color: var(--color-text-muted);
    font-style: italic;
    padding: var(--spacing-xl);
}
```

**Points clés:**
- CSS Grid moderne avec `gap` pour espacement uniforme
- Responsive mobile-first (1 col mobile, 2+ cols tablet/desktop)
- Colonnes configurables (1-4)
- Variables CSS pour espacement cohérent
- Message vide stylisé

### Enqueue des styles

**Dans `functions.php`** (ou `inc/setup.php`):

```php
function kiyose_enqueue_testimonials_styles() {
    // Enqueue uniquement si le shortcode est présent
    global $post;
    if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'kiyose_testimonials' ) ) {
        wp_enqueue_style(
            'kiyose-testimonials-grid',
            get_template_directory_uri() . '/assets/css/components/testimonials-grid.css',
            array(),
            KIYOSE_VERSION
        );
    }
}
add_action( 'wp_enqueue_scripts', 'kiyose_enqueue_testimonials_styles' );
```

**Alternative (enqueue systématique):**

Si le shortcode est utilisé fréquemment, enqueue le fichier CSS dans `assets/css/components/` et l'inclure dans le CSS principal.

### Page témoignages (exemple d'utilisation)

**Template**: `page.php` (template par défaut)

**Contenu de la page WordPress** (à créer dans l'admin):

```
Titre: Témoignages
Slug: temoignages

Contenu:
<p>Découvrez les retours de celles et ceux qui ont participé aux ateliers de L'Atelier Kiyose.</p>

[kiyose_testimonials]
```

**Ou avec filtres:**

```
Titre: Témoignages Entreprises
Slug: temoignages-entreprises

Contenu:
<p>Retours de nos clients entreprises.</p>

[kiyose_testimonials context="enterprise" columns="2"]
```

## Tests et validation

### Tests automatisés

- [ ] PHPCS passe sur `inc/shortcodes.php`
- [ ] Test PHPUnit: le shortcode `kiyose_testimonials` est enregistré
- [ ] Test PHPUnit: les attributs sont correctement sanitizés et validés
- [ ] Test PHPUnit: le shortcode retourne du HTML valide avec des témoignages
- [ ] Test PHPUnit: le shortcode retourne le message vide si aucun témoignage
- [ ] Test PHPUnit: le filtre par contexte fonctionne correctement

### Tests manuels

- [ ] Le shortcode `[kiyose_testimonials]` affiche tous les témoignages en grille 3 colonnes
- [ ] Les attributs `limit`, `columns`, `context` fonctionnent correctement
- [ ] Les valeurs invalides sont correctement nettoyées (ex: `columns="10"` → 3)
- [ ] Le message "Aucun témoignage disponible" s'affiche si aucun résultat
- [ ] Les témoignages utilisent le template part `content-testimony.php`
- [ ] L'ordre des témoignages est aléatoire à chaque chargement

### Tests manuels — Responsive

- [ ] Mobile (320px-767px): 1 colonne
- [ ] Tablet (768px-1023px): 2 colonnes (grille 3/4 cols)
- [ ] Desktop (≥1024px): 3 ou 4 colonnes selon paramètre
- [ ] Espacement uniforme entre les cartes (Grid gap)
- [ ] Touch targets ≥44x44px (si éléments interactifs)

### Tests manuels — Accessibilité

- [ ] Ordre de lecture logique (screenreader)
- [ ] Chaque témoignage utilise `<blockquote>` et `<cite>` sémantiquement corrects
- [ ] Contraste WCAG 2.2 AA respecté (≥4.5:1 texte, ≥3:1 UI)
- [ ] Pas de contenu décoratif sans `aria-hidden="true"`
- [ ] Message vide accessible aux screenreaders

### Tests manuels — Performance

- [ ] Les styles CSS ne sont enqueued que si le shortcode est utilisé
- [ ] `wp_reset_postdata()` est bien appelé après `WP_Query`
- [ ] Pas de query N+1 (vérifier avec Query Monitor plugin)
- [ ] Lazy loading des images si applicable

## Hors périmètre

- **Mode carousel** : Implémenté dans PRD 0008 (homepage) avec JS accessible
- **Pagination** : Pas nécessaire pour une page dédiée (on affiche tous les témoignages)
- **Filtres interactifs** : Pas de filtrage dynamique par contexte (seulement via attribut shortcode)
- **Migration des témoignages** : Manuelle, hors PRDs

## Notes d'implémentation

**Ordre aléatoire:**
- `orderby => 'rand'` permet de varier l'affichage à chaque chargement
- Utile pour donner de la visibilité à différents témoignages
- Peut être changé en `'date'` ou `'title'` si souhaité

**Extension future:**
- Le shortcode est conçu pour supporter le mode `carousel` (PRD 0008)
- Les attributs peuvent être étendus (ex: `order`, `orderby`) si nécessaire