# PRD 0007 — CPT Témoignages

## Statut

**terminé**

## Objectif

Créer le Custom Post Type (type de contenu personnalisé) `kiyose_testimony` pour gérer les témoignages de manière structurée dans l'admin WordPress, avec un template part pour l'affichage.

## Contexte

Les témoignages sont actuellement en dur dans une page WordPress. Ce PRD crée un type de contenu dédié permettant :
- D'ajouter/modifier/supprimer des témoignages individuellement dans l'admin
- De les afficher de manière uniforme via un template part
- De les requêter pour les afficher sur la homepage et la page témoignages

Les témoignages existants devront être ressaisis manuellement dans le nouveau CPT après activation du thème.

## Dépendances

- **PRD 0001** : Structure de fichiers et `functions.php`.

## Fichiers à créer/modifier

### Nouveaux fichiers

```
latelierkiyose/
├── inc/
│   └── custom-post-types.php          # Déclaration du CPT
├── template-parts/
│   └── content-testimony.php          # Affichage d'un témoignage
├── assets/
│   └── css/
│       └── components/
│           └── testimony.css          # Styles témoignage
└── tests/
    └── test-custom-post-types.php     # Tests PHPUnit
```

### Fichiers modifiés

- `functions.php` — Ajouter le `require_once` de `inc/custom-post-types.php`

## Spécifications détaillées

### Déclaration du CPT (`inc/custom-post-types.php`)

**Slug** : `kiyose_testimony`

**Paramètres** :
- `public` : `false` (pas d'archive publique, pas de page individuelle)
- `show_ui` : `true` (visible dans l'admin)
- `show_in_menu` : `true`
- `menu_icon` : `dashicons-format-quote`
- `supports` : `array( 'title', 'editor' )`
  - `title` : prénom du témoin
  - `editor` : la citation
- `has_archive` : `false`
- `publicly_queryable` : `false`

**Labels** :
```php
array(
    'name'               => 'Témoignages',
    'singular_name'      => 'Témoignage',
    'add_new'            => 'Ajouter un témoignage',
    'add_new_item'       => 'Ajouter un nouveau témoignage',
    'edit_item'          => 'Modifier le témoignage',
    'new_item'           => 'Nouveau témoignage',
    'view_item'          => 'Voir le témoignage',
    'search_items'       => 'Rechercher des témoignages',
    'not_found'          => 'Aucun témoignage trouvé',
    'not_found_in_trash' => 'Aucun témoignage dans la corbeille',
)
```

**Tous les labels doivent utiliser les fonctions i18n** (`__( '...', 'kiyose' )`).

### Meta box : Contexte du témoignage

Ajouter une meta box personnalisée pour le champ "Contexte" :

**Champ** : `kiyose_testimony_context`

**Type** : `select` avec les options :
- `individual` — Individu
- `enterprise` — Entreprise
- `structure` — Structure / Association

**Implémentation** :
- Hook `add_meta_boxes` pour ajouter la meta box
- Hook `save_post_kiyose_testimony` pour sauvegarder la valeur
- Nonce : `kiyose_testimony_context_nonce`
- Sanitization : `sanitize_key()` sur la valeur
- Vérification des capabilities : `edit_post`

### `template-parts/content-testimony.php`

Carte de témoignage réutilisable.

**Structure** :
```html
<blockquote class="testimony-card">
    <div class="testimony-card__quote">
        <?php the_content(); ?>
    </div>
    <footer class="testimony-card__attribution">
        <cite class="testimony-card__name">
            <?php the_title(); ?>
        </cite>
        <span class="testimony-card__context">
            <!-- Afficher le libellé du contexte -->
        </span>
    </footer>
</blockquote>
```

**Points clés** :
- Utiliser `<blockquote>` sémantiquement correct pour une citation
- `<cite>` pour l'attribution
- Le contexte est affiché en texte lisible (pas la clé technique)

### Fonction helper

Créer `kiyose_get_testimony_context_label( $context_key )` pour convertir la clé en libellé :
- `individual` → `''` (pas d'affichage, c'est le cas par défaut)
- `enterprise` → `'Entreprise'`
- `structure` → `'Association / Structure'`

### Styles CSS

- Carte avec fond clair, bordure gauche dorée (`--color-accent`)
- Guillemets décoratifs (CSS `::before` avec caractère `"`)
- Typographie italique pour la citation
- Nom en gras, contexte en texte léger

## Tests et validation

### Tests automatisés
- [ ] PHPCS passe sur tous les fichiers PHP créés/modifiés
- [ ] Test PHPUnit : le CPT `kiyose_testimony` est enregistré
- [ ] Test PHPUnit : la meta box sauvegarde et récupère correctement la valeur `kiyose_testimony_context`
- [ ] Test PHPUnit : la sanitization fonctionne (valeur invalide rejetée)

### Tests manuels
- [ ] Dans l'admin WordPress, le menu "Témoignages" apparaît avec l'icône appropriée
- [ ] On peut créer un témoignage avec titre (prénom), contenu (citation), et contexte (select)
- [ ] Le témoignage est sauvegardé et récupérable
- [ ] Le template part `content-testimony.php` affiche correctement un témoignage
- [ ] Le CPT n'a pas d'archive publique ni de page individuelle

### Tests manuels — Accessibilité
- [ ] La meta box dans l'admin est accessible au clavier
- [ ] Le select "Contexte" a un label associé
- [ ] La carte de témoignage utilise les éléments sémantiques corrects (`blockquote`, `cite`)

## Hors périmètre

- Affichage des témoignages sur la homepage (PRD 0008)
- Carousel de témoignages (PRD 0008)
- Page dédiée aux témoignages (peut utiliser `page.php` avec un shortcode ou être ajouté plus tard)
- Migration des témoignages existants (manuelle, hors PRDs)
