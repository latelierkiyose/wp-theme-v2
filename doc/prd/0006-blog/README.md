# PRD 0006 — Blog

## Statut

**terminé**

## Objectif

Implémenter les templates de blog : archive (liste d'articles), article unique, et le template part réutilisable pour afficher un article dans une liste.

## Dépendances

- **PRD 0005** : Templates de base (`page.php` et structure de recherche).

## Fichiers à créer/modifier

### Nouveaux fichiers

```
latelierkiyose/
├── archive.php                        # Archive blog (liste d'articles)
├── single.php                         # Article unique
├── template-parts/
│   └── content-blog.php               # Affichage d'un article dans une liste
└── assets/
    └── css/
        └── components/
            ├── blog-archive.css       # Styles liste d'articles
            ├── blog-single.css        # Styles article unique
            └── blog-card.css          # Styles carte d'article
```

### Fichiers modifiés

- `inc/setup.php` — Désactiver les commentaires sur les articles (`remove_post_type_support`)

## Spécifications détaillées

### `inc/setup.php` (modification)

Ajouter dans le hook `after_setup_theme` ou `init` :

```php
remove_post_type_support( 'post', 'comments' );
remove_post_type_support( 'post', 'trackbacks' );
```

### `template-parts/content-blog.php`

Carte d'article réutilisable (dans archives, résultats de recherche, homepage).

**Structure** :
```
<article class="blog-card" <?php post_class(); ?>>
    <?php if ( has_post_thumbnail() ) : ?>
        <div class="blog-card__thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) ); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="blog-card__content">
        <header class="blog-card__header">
            <time class="blog-card__date" datetime="...">
                <?php echo get_the_date(); ?>
            </time>
            <h2 class="blog-card__title">
                <a href="<?php the_permalink(); ?>">
                    <?php the_title(); ?>
                </a>
            </h2>
        </header>
        <div class="blog-card__excerpt">
            <?php the_excerpt(); ?>
        </div>
        <a href="<?php the_permalink(); ?>" class="blog-card__read-more" aria-label="Lire la suite de <?php the_title_attribute(); ?>">
            Lire la suite
        </a>
    </div>
</article>
```

**Points clés** :
- Le lien "Lire la suite" a un `aria-label` avec le titre de l'article pour le contexte
- Image à la une avec `loading="lazy"`
- La date utilise l'élément `<time>` avec attribut `datetime`

### `archive.php`

Liste d'articles avec titre de l'archive et pagination.

**Structure** :
```
get_header()

<section class="blog-archive">
    <header class="blog-archive__header">
        <h1 class="blog-archive__title"><?php the_archive_title(); ?></h1>
        <?php the_archive_description( '<div class="blog-archive__description">', '</div>' ); ?>
    </header>

    <?php if ( have_posts() ) : ?>
        <div class="blog-archive__grid">
            <?php while ( have_posts() ) : the_post(); ?>
                <?php get_template_part( 'template-parts/content', 'blog' ); ?>
            <?php endwhile; ?>
        </div>

        <?php the_posts_pagination( array(
            'mid_size'  => 2,
            'prev_text' => 'Précédent',
            'next_text' => 'Suivant',
        ) ); ?>
    <?php else : ?>
        <p>Aucun article pour le moment.</p>
    <?php endif; ?>
</section>

get_footer()
```

**Layout** :
- Mobile : 1 colonne
- Tablet (≥768px) : 2 colonnes
- Desktop (≥1024px) : 3 colonnes
- Utiliser CSS Grid

### `single.php`

Article unique avec contenu complet.

**Structure** :
```
get_header()

<article class="blog-single" <?php post_class(); ?>>
    <header class="blog-single__header">
        <time class="blog-single__date" datetime="...">
            <?php echo get_the_date(); ?>
        </time>
        <h1 class="blog-single__title"><?php the_title(); ?></h1>
        <?php if ( has_post_thumbnail() ) : ?>
            <div class="blog-single__featured-image">
                <?php the_post_thumbnail( 'large' ); ?>
            </div>
        <?php endif; ?>
    </header>

    <div class="blog-single__content">
        <?php the_content(); ?>
    </div>

    <footer class="blog-single__footer">
        <div class="blog-single__categories">
            <?php the_category( ', ' ); ?>
        </div>

        <nav class="blog-single__navigation" aria-label="Navigation entre articles">
            <?php
            the_post_navigation( array(
                'prev_text' => '← %title',
                'next_text' => '%title →',
            ) );
            ?>
        </nav>
    </footer>
</article>

get_footer()
```

**Points clés** :
- Image à la une affichée en taille `large` (pas de lazy loading car above-the-fold)
- Catégories affichées en bas de l'article
- Navigation article précédent/suivant
- Pas de commentaires (désactivés dans setup.php)
- Contenu centré avec max-width lisible

### Partage réseaux sociaux

Les boutons de partage dans `single.php` :
- Liens simples vers les URLs de partage (pas de SDK tiers)
- Facebook, LinkedIn (les réseaux présents dans le footer)
- Icônes SVG avec `aria-label`
- `target="_blank" rel="noopener noreferrer"`

Exemple de lien de partage :
```
https://www.facebook.com/sharer/sharer.php?u=URL_DE_L_ARTICLE
https://www.linkedin.com/sharing/share-offsite/?url=URL_DE_L_ARTICLE
```

## Tests et validation

### Tests automatisés
- [ ] PHPCS passe sur tous les fichiers PHP créés/modifiés
- [ ] Test PHPUnit : les commentaires sont bien désactivés sur les posts

### Tests manuels
- [ ] La page blog (archive) affiche les articles avec pagination
- [ ] Un article unique affiche le contenu complet avec date, catégories, navigation
- [ ] Les cartes d'articles ont un rendu cohérent (image, titre, extrait, lien)
- [ ] La recherche utilise `content-blog.php` pour afficher les résultats

### Tests manuels — Accessibilité
- [ ] Liens "Lire la suite" ont des `aria-label` différenciés
- [ ] Navigation entre articles a un `aria-label`
- [ ] Hiérarchie des titres correcte (h1 titre article, h2 sections)
- [ ] Images à la une ont un attribut `alt`

### Tests manuels — Responsive
- [ ] Archive : 1 colonne mobile, 2 colonnes tablet, 3 colonnes desktop
- [ ] Article unique : contenu lisible sur mobile (pas de débordement)
- [ ] Images responsives (pas de scroll horizontal)

## Hors périmètre

- Section "Derniers articles" sur la homepage (PRD 0008)
- Styling avancé du contenu Gutenberg
