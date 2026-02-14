# PRD 0005 — Templates de base

## Statut

**terminé**

## Objectif

Créer les templates WordPress essentiels pour que toutes les pages du site aient un rendu fonctionnel : page par défaut, page 404, résultats de recherche.

## Dépendances

- **PRD 0003** : Header et footer en place.

## Fichiers à créer/modifier

### Nouveaux fichiers

```
latelierkiyose/
├── page.php                       # Template page par défaut
├── 404.php                        # Page d'erreur 404
├── search.php                     # Résultats de recherche
└── assets/
    └── css/
        └── components/
            ├── page.css           # Styles page par défaut
            ├── 404.css            # Styles page 404
            └── search.css         # Styles résultats de recherche
```

### Fichiers modifiés

- `index.php` — Raffinement du template fallback (sera simplifié pour rediriger vers `page.php` ou `archive.php`)

## Spécifications détaillées

### `page.php`

Template par défaut pour toutes les pages WordPress qui n'ont pas de template spécifique assigné.

**Structure** :
```
get_header()

<article class="page">
    <header class="page__header">
        <h1 class="page__title"><?php the_title(); ?></h1>
    </header>
    <div class="page__content">
        <?php the_content(); ?>
    </div>
</article>

get_footer()
```

**Styles** :
- Contenu centré avec max-width lisible (~75ch ou 800px)
- Typographie confortable (line-height 1.7, paragraphes espacés)
- Images dans le contenu : max-width 100%, height auto
- Styles pour le contenu WordPress (alignements, galeries, colonnes Gutenberg)

**Usage** : Ce template sera utilisé pour les mentions légales, la politique de confidentialité, et toute page sans template dédié.

### `404.php`

Page d'erreur 404 avec un design cohérent (pas une page technique).

**Structure** :
```
get_header()

<section class="error-404">
    <h1>Page introuvable</h1>
    <p>La page que vous recherchez n'existe pas ou a été déplacée.</p>

    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="button button--primary">
        Retour à l'accueil
    </a>

    <div class="error-404__search">
        <h2>Rechercher sur le site</h2>
        <?php get_search_form(); ?>
    </div>

    <div class="error-404__suggestions">
        <h2>Pages populaires</h2>
        <ul>
            <!-- Liens vers services, calendrier, contact -->
        </ul>
    </div>
</section>

get_footer()
```

**Points clés** :
- Un seul `<h1>` par page
- Le bouton "Retour à l'accueil" utilise la classe `.button--primary`
- Formulaire de recherche via `get_search_form()` (WordPress standard)
- Les suggestions de pages sont des liens statiques vers les pages principales

### `search.php`

Résultats de recherche avec le formulaire et la liste paginée.

**Structure** :
```
get_header()

<section class="search-results">
    <header class="search-results__header">
        <h1>Résultats de recherche pour « <?php echo esc_html( get_search_query() ); ?> »</h1>
    </header>

    <?php get_search_form(); ?>

    <?php if ( have_posts() ) : ?>
        <div class="search-results__list">
            <?php while ( have_posts() ) : the_post(); ?>
                <?php get_template_part( 'template-parts/content', 'blog' ); ?>
            <?php endwhile; ?>
        </div>
        <?php the_posts_pagination( array( ... ) ); ?>
    <?php else : ?>
        <div class="search-results__empty">
            <p>Aucun résultat trouvé pour votre recherche.</p>
            <p>Suggestions :</p>
            <ul>
                <li>Vérifiez l'orthographe des mots-clés</li>
                <li>Essayez des termes plus généraux</li>
            </ul>
        </div>
    <?php endif; ?>
</section>

get_footer()
```

**Points clés** :
- Le terme de recherche est échappé avec `esc_html()`
- Réutilise `template-parts/content-blog.php` (créé dans PRD 0006) — en attendant, créer un fallback minimal inline
- Pagination via `the_posts_pagination()` avec classes BEM personnalisées
- Message explicite quand aucun résultat n'est trouvé

### `index.php` (modification)

Simplifier le template fallback pour qu'il affiche le loop basique avec header/footer. Ce template est rarement atteint si les autres sont en place.

### Formulaire de recherche

WordPress utilise `searchform.php` s'il existe, sinon génère un formulaire par défaut. Créer `searchform.php` pour contrôler le markup :

```html
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label for="search-field" class="sr-only">Rechercher</label>
    <input
        type="search"
        id="search-field"
        class="search-form__input"
        placeholder="Rechercher..."
        value="<?php echo esc_attr( get_search_query() ); ?>"
        name="s"
    >
    <button type="submit" class="search-form__submit button button--primary">
        Rechercher
    </button>
</form>
```

**Accessibilité** :
- `role="search"` sur le formulaire
- Label explicite (masqué visuellement avec `.sr-only`)
- Bouton submit avec texte visible

## Tests et validation

### Tests automatisés
- [ ] PHPCS passe sur tous les fichiers PHP créés

### Tests manuels
- [ ] Les pages WordPress standard s'affichent avec `page.php` (contenu centré, typographie correcte)
- [ ] La page 404 s'affiche pour une URL inexistante
- [ ] La page 404 contient : titre, message, bouton accueil, formulaire de recherche, suggestions
- [ ] Le formulaire de recherche fonctionne et affiche les résultats dans `search.php`
- [ ] Message "aucun résultat" s'affiche pour une recherche sans résultat
- [ ] Pagination fonctionne quand il y a plus de résultats que `posts_per_page`

### Tests manuels — Accessibilité
- [ ] Un seul `<h1>` sur chaque template
- [ ] Hiérarchie des titres correcte (h1 → h2)
- [ ] Formulaire de recherche accessible au clavier
- [ ] Label associé au champ de recherche
- [ ] Focus visible sur tous les éléments interactifs

### Tests manuels — Responsive
- [ ] Contenu lisible à 320px (pas de débordement horizontal)
- [ ] Page 404 bien mise en page sur mobile et desktop
- [ ] Formulaire de recherche utilisable sur mobile (champ + bouton)

## Hors périmètre

- `template-parts/content-blog.php` complet (PRD 0006 — un placeholder est acceptable)
- Templates spécifiques (home, services, contact, etc.)
- Styles de contenu Gutenberg avancés (colonnes, boutons, etc.)
