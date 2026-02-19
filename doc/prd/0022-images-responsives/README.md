# PRD 0022 — Images responsives et optimisation

## Statut

**brouillon**

## Objectif

Corriger le problème "Serves images with low resolution" détecté par Lighthouse en s'assurant que les images sont servies à la bonne résolution selon le contexte d'affichage, et que toutes les images ont des dimensions explicites pour éviter le CLS.

## Contexte

Lighthouse signale que certaines images sont affichées à une résolution inférieure à leur taille d'affichage. Le score actuel de CLS est excellent (0), mais les images manquent de dimensions explicites dans certains cas.

### État actuel

**Bonne pratique déjà en place** :
- `the_post_thumbnail()` et `wp_get_attachment_image()` génèrent automatiquement les attributs `srcset` et `sizes` via WordPress
- `loading="lazy"` est utilisé sur les cartes service et blog
- Les images décoratives ont `alt=""` et `role="presentation"`
- 3 tailles custom définies : `kiyose-hero` (1920x800), `kiyose-service-card` (600x400), `kiyose-testimony-thumb` (150x150)

**Problèmes identifiés** :

| Fichier | Ligne | Problème |
|---------|-------|----------|
| `templates/page-home.php` | 42 | Hero utilise `medium_large` (768px) au lieu de `kiyose-hero` (1920px) |
| `single.php` | 27-33 | Image à la une sans `loading="lazy"` |
| `header.php` | 31 | Logo sans `srcset` (acceptable : image statique fixe) |
| `inc/meta-boxes.php` | 181 | `wp_get_attachment_image()` sans attribut `alt` (preview admin) |

## Dépendances

- **PRD 0001** : Squelette du thème (tailles d'images custom)
- **PRD 0008** : Page d'accueil (template hero)

## Spécifications

### 1. Corriger la taille de l'image hero

**Fichier** : `templates/page-home.php`, ligne 42

**Avant** :
```php
<?php echo wp_get_attachment_image( $kiyose_hero_image_id, 'medium_large', false, array( 'alt' => esc_attr( $kiyose_hero_title ) ) ); ?>
```

**Après** :
```php
<?php
echo wp_get_attachment_image(
	$kiyose_hero_image_id,
	'kiyose-hero',
	false,
	array(
		'alt'   => esc_attr( $kiyose_hero_title ),
		'sizes' => '100vw',
	)
);
?>
```

**Justification** : La taille `medium_large` (768px) est insuffisante pour un hero plein écran. La taille `kiyose-hero` (1920x800) est définie dans `inc/setup.php` exactement pour cet usage. L'attribut `sizes="100vw"` indique que l'image occupe toute la largeur du viewport.

**Impact** : WordPress générera automatiquement un `srcset` avec les tailles intermédiaires, permettant aux mobiles de charger une version plus petite.

### 2. Ajouter `loading="lazy"` sur l'image à la une des articles

**Fichier** : `single.php`, lignes 27-33

**Avant** :
```php
<?php
the_post_thumbnail(
	'large',
	array(
		'alt' => get_the_title(),
	)
);
?>
```

**Après** :
```php
<?php
the_post_thumbnail(
	'large',
	array(
		'alt'     => get_the_title(),
		'loading' => 'lazy',
	)
);
?>
```

**Note** : Sur les articles, l'image à la une est généralement sous le titre, donc sous la ligne de flottaison. Le lazy loading est approprié.

### 3. Ajouter `alt` sur le preview admin

**Fichier** : `inc/meta-boxes.php`, ligne 181

**Avant** :
```php
echo wp_get_attachment_image( $hero_image_id, 'medium_large' );
```

**Après** :
```php
echo wp_get_attachment_image(
	$hero_image_id,
	'medium_large',
	false,
	array(
		'alt' => esc_attr__( 'Aperçu de l\'image hero', 'kiyose' ),
	)
);
```

### 4. Vérifier la taille hero sur les pages services

**Fichier** : `templates/page-services.php`, ligne 24

Vérifier que `the_post_thumbnail( 'kiyose-hero', ... )` est bien utilisé (et non `medium_large`). D'après l'exploration, c'est déjà correct.

### 5. Responsive hero pour mobile

**Problème** : L'image hero à 1920px est surdimensionnée sur mobile. WordPress génère un `srcset` automatique, mais l'attribut `sizes` par défaut peut ne pas être optimal.

**Solution** : Ajouter un attribut `sizes` explicite sur les images hero :

```
sizes="100vw"
```

Cela permet au navigateur de choisir la bonne taille dans le `srcset` en fonction de la largeur du viewport. Un mobile 375px chargera l'image ~400px au lieu de 1920px.

## Fichiers impactés

### Fichiers modifiés

| Fichier | Modification |
|---------|-------------|
| `templates/page-home.php` | Ligne 42 — taille `medium_large` → `kiyose-hero` + `sizes` |
| `single.php` | Lignes 27-33 — ajout `loading="lazy"` |
| `inc/meta-boxes.php` | Ligne 181 — ajout attribut `alt` |

## Hors périmètre

- Conversion WebP — gérée par plugin (PRD 0024)
- Compression d'images — gérée par plugin (PRD 0024)
- Logo responsive — le logo est une image statique de taille fixe (250x250), pas besoin de `srcset`

## Accessibilité

- L'attribut `alt` ajouté sur le preview admin améliore l'accessibilité de l'interface d'administration
- `loading="lazy"` n'a aucun impact négatif sur l'accessibilité
- Les images hero restent identiques visuellement

## Performance attendue

| Métrique | Impact |
|----------|--------|
| LCP (mobile) | Amélioration significative — l'image hero mobile passe de ~1920px à ~400-800px |
| CLS | Aucun changement (déjà à 0) |
| Score "Serves images with low resolution" | Résolu — hero affiché avec la bonne résolution |

## Vérification

1. **Hero homepage** : Inspecter l'image hero dans DevTools > Elements — vérifier `srcset` et `sizes="100vw"`
2. **Résolution** : DevTools > Network > filtrer images — vérifier que l'image chargée correspond à la largeur du viewport
3. **Mobile** : Chrome DevTools > Device Mode (375px) — vérifier qu'une image < 800px est chargée
4. **Desktop** : Vérifier que l'image 1920px est chargée sur grands écrans
5. **Lighthouse** : "Serves images with low resolution" ne doit plus apparaître
6. **Lazy loading** : Vérifier dans Network que l'image `single.php` ne se charge qu'au scroll
7. **PHPCS** : `make phpcs` — aucune violation
