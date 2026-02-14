# PRD 0009 — Template services

## Statut

**terminé**

## Objectif

Créer le template générique partagé par les 4 pages de services (art-thérapie, rigologie, bols tibétains, ateliers philosophie). Le contenu spécifique est géré dans l'éditeur WordPress, le template fournit la structure.

## Dépendances

- **PRD 0005** : Templates de base (`page.php`).

## Fichiers à créer/modifier

### Nouveaux fichiers

```
latelierkiyose/
├── templates/
│   └── page-services.php              # Template générique services
└── assets/
    └── css/
        └── components/
            └── service-page.css       # Styles page de service
```

## Spécifications détaillées

### `templates/page-services.php`

**Header du fichier** :
```php
<?php
/*
Template Name: Page de service
*/
```

Ce template est assigné aux 4 pages de services dans l'admin WordPress. Le contenu (texte, images) est entièrement géré depuis l'éditeur WordPress (Gutenberg).

**Structure** :
```
get_header()

<article class="service-page">
    <header class="service-page__header">
        <h1 class="service-page__title"><?php the_title(); ?></h1>
        <?php if ( has_post_thumbnail() ) : ?>
            <div class="service-page__hero-image">
                <?php the_post_thumbnail( 'kiyose-hero' ); ?>
            </div>
        <?php endif; ?>
    </header>

    <div class="service-page__content">
        <?php the_content(); ?>
    </div>

    <footer class="service-page__footer">
        <div class="service-page__cta">
            <a href="/contact/" class="button button--primary">
                Me contacter
            </a>
            <a href="/calendrier-tarifs/" class="button button--secondary">
                Voir le calendrier
            </a>
        </div>
    </footer>
</article>

get_footer()
```

**Points clés** :
- Le template est simple : il affiche `the_content()` — tout le contenu est géré dans Gutenberg
- Les CTAs en bas de page sont communs aux 4 services (contact + calendrier)
- L'image à la une est affichée en bandeau hero si présente

### Styles CSS

**Structure de la page** :
- Contenu centré avec max-width lisible
- Image hero pleine largeur (ou largeur max du container)
- Bonne typographie pour le contenu long (line-height 1.7, espacement paragraphes)

**Styles pour le contenu Gutenberg** :
Le contenu étant géré dans l'éditeur WordPress, il faut styler les blocs Gutenberg courants :
- Paragraphes
- Titres (h2, h3, h4)
- Listes (ul, ol)
- Images et galeries
- Colonnes
- Séparateurs
- Citations (blockquote)

Ces styles sont partagés avec `page.php` — les placer dans un fichier CSS commun ou dans `main.css`.

**CTAs** :
- Deux boutons côte à côte (flex), empilés sur mobile
- Espacement suffisant avec le contenu au-dessus

### Utilisation

Workflow pour assigner le template aux pages de services :
1. Dans l'admin WordPress, éditer la page (ex: "Art-thérapie")
2. Dans Page Attributes → Template, sélectionner "Page de service"
3. Publier / Mettre à jour
4. Répéter pour les 3 autres pages de services

## Tests et validation

### Tests automatisés
- [ ] PHPCS passe sur les fichiers PHP créés

### Tests manuels
- [ ] Le template "Page de service" apparaît dans le menu déroulant des templates dans l'admin
- [ ] Une page avec ce template affiche le titre, l'image à la une, et le contenu
- [ ] Les CTAs en bas de page sont visibles et fonctionnels
- [ ] Le contenu Gutenberg (paragraphes, titres, listes, images) est correctement stylé

### Tests manuels — Accessibilité
- [ ] Un seul `<h1>` (titre de la page)
- [ ] Hiérarchie des titres correcte dans le contenu
- [ ] CTAs accessibles au clavier, focus visible
- [ ] Image à la une avec `alt` approprié

### Tests manuels — Responsive
- [ ] Image hero responsive (pas de débordement)
- [ ] Contenu lisible à 320px
- [ ] CTAs empilés sur mobile, côte à côte en desktop

## Hors périmètre

- Le contenu rédactionnel de chaque service (géré dans WordPress par le client)
- Les cartes de service de la homepage (PRD 0008)
- Éléments décoratifs Kintsugi (PRD 0014)
