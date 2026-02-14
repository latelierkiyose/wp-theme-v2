# PRD 0011 — Page à propos

## Statut

**terminé**

## Objectif

Créer le template de la page "À propos" présentant Virginie Le Mignon, sa philosophie Kintsugi, son parcours et ses qualifications.

## Dépendances

- **PRD 0005** : Templates de base.

## Fichiers à créer/modifier

### Nouveaux fichiers

```
latelierkiyose/
├── templates/
│   └── page-about.php                 # Template page à propos
└── assets/
    └── css/
        └── components/
            └── about-page.css         # Styles page à propos
```

## Spécifications détaillées

### `templates/page-about.php`

**Header du fichier** :
```php
<?php
/*
Template Name: À propos
*/
```

**Structure** :
```
get_header()

<article class="about-page">
    <header class="about-page__header">
        <h1 class="about-page__title"><?php the_title(); ?></h1>
    </header>

    <div class="about-page__content">
        <?php the_content(); ?>
    </div>
</article>

get_footer()
```

**Points clés** :
- Ce template est volontairement simple : le contenu est entièrement géré dans l'éditeur WordPress (Gutenberg)
- La page utilisera les blocs Gutenberg pour structurer le contenu : colonnes (photo + texte), paragraphes, titres, listes
- Les styles de base du contenu Gutenberg sont hérités de `main.css` et `page.css` (PRD 0005)

### Contenu attendu (géré dans WordPress)

Le contenu sera saisi dans l'éditeur WordPress, mais le template doit bien styler ces éléments :

1. **Introduction** — Photo de Virginie + texte de présentation côte à côte (bloc Colonnes Gutenberg)
2. **Philosophie Kintsugi** — Section avec texte décrivant la métaphore du Kintsugi
3. **Parcours** — 20 ans d'expérience, transitions professionnelles
4. **Qualifications** — Liste des diplômes et certifications (bloc Liste Gutenberg)

### Styles CSS

**Spécifiques à la page** :
- Mise en page aérée avec beaucoup d'espace blanc
- Image de Virginie : coins arrondis, ombre légère
- Section qualifications : mise en forme soignée de la liste

**Styles Gutenberg partagés** :
Si ce n'est pas déjà fait dans PRD 0005 ou 0009, s'assurer que les blocs Gutenberg suivants sont correctement stylés :
- Colonnes (`wp-block-columns`)
- Images (`wp-block-image`)
- Listes (`wp-block-list`)
- Séparateurs (`wp-block-separator`)
- Citations (`wp-block-quote`)

## Tests et validation

### Tests automatisés
- [ ] PHPCS passe sur les fichiers PHP créés

### Tests manuels
- [ ] Le template "À propos" apparaît dans le menu des templates
- [ ] Le contenu de la page s'affiche correctement
- [ ] Les blocs Gutenberg sont correctement stylés (colonnes, images, listes)

### Tests manuels — Accessibilité
- [ ] Un seul `<h1>`
- [ ] Hiérarchie des titres correcte
- [ ] Images avec `alt` approprié
- [ ] Contenu lisible avec bon contraste

### Tests manuels — Responsive
- [ ] Colonnes (photo + texte) empilées sur mobile
- [ ] Images responsives
- [ ] Contenu lisible à 320px

## Hors périmètre

- Le contenu rédactionnel (biographie, philosophie, parcours) — géré dans WordPress
- Les photos de Virginie — fournies séparément
- Éléments décoratifs Kintsugi (PRD 0014)
