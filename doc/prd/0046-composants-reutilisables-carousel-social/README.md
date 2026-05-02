# PRD 0046 — Composants réutilisables carousel et liens sociaux

- **Statut** : terminé
- **Version** : 1.0
- **Date** : 2026-05-02

## Contexte

Le thème possède déjà des template parts (`content-blog.php`, `content-testimony.php`) et des helpers SVG (`kiyose_social_icon_svg()`, `kiyose_carousel_icon_svg()`), mais certains composants sont encore dupliqués.

Duplications identifiées :

- le markup du carousel témoignages existe dans `inc/shortcodes.php` et dans `templates/page-home.php` ;
- les SVG sociaux centralisés dans `inc/accessibility.php` sont réécrits inline dans `templates/page-contact.php` et `single.php` ;
- les URLs sociales sont gérées différemment entre footer, contact et partage d'article.

## Objectif

Créer des composants PHP réutilisables pour le carousel de témoignages et les liens sociaux, puis les utiliser dans les templates et shortcodes existants sans modifier le rendu visuel attendu.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Emplacement | Créer `inc/components.php` | Les composants produisent du HTML réutilisable hors d'un template unique |
| Carousel | Créer `kiyose_get_testimonials_carousel_html( WP_Query $query, string $aria_label ): string` | Utilisable par shortcode et home avec buffering |
| Social links | Créer `kiyose_get_social_profiles(): array` et `kiyose_get_social_links_html( string $context ): string` | Source de vérité unique pour footer/contact |
| Partage article | Créer `kiyose_get_post_share_links_html( int $post_id ): string` | Les liens de partage ne sont pas les mêmes que les profils sociaux |
| Compatibilité | Garder les classes CSS actuelles | Les styles existants continuent de s'appliquer |

## Spécifications

### 1. Charger le module composants

Modifier `latelierkiyose/functions.php` pour charger `inc/components.php` après `inc/accessibility.php`.

### 2. Factoriser le carousel témoignages

Créer `kiyose_get_testimonials_carousel_html( WP_Query $query, string $aria_label ): string`.

Le HTML généré doit contenir :

- `<div class="carousel" role="region" aria-roledescription="carousel">`
- `aria-label` passé par argument et échappé avec `esc_attr()`
- `.carousel__controls` avec boutons précédent, pause/play, suivant
- `.carousel__track` avec `aria-live="polite"`
- slides avec :
  - `class="carousel__slide"` ;
  - `role="group"` ;
  - `aria-roledescription="slide"` ;
  - `aria-label="N sur TOTAL"` ;
  - première slide active et `aria-hidden="false"` ;
  - autres slides `aria-hidden="true"`.

Le contenu de chaque slide continue d'utiliser `get_template_part( 'template-parts/content', 'testimony' )`.

### 3. Remplacer les usages du carousel

- Dans `inc/shortcodes.php`, remplacer le bloc carousel par `kiyose_get_testimonials_carousel_html()`.
- Dans `templates/page-home.php` ou son futur partial `template-parts/home/testimonials.php`, utiliser le même helper.
- Conserver le rendu grille du shortcode hors carousel.

### 4. Factoriser les profils sociaux

Créer `kiyose_get_social_profiles(): array`.

Source de données :

- Facebook : `get_theme_mod( 'kiyose_social_facebook', 'https://www.facebook.com/latelierkiyose' )`
- Instagram : `get_theme_mod( 'kiyose_social_instagram', 'https://www.instagram.com/latelierkiyose' )`
- LinkedIn : `get_theme_mod( 'kiyose_social_linkedin', 'https://www.linkedin.com/company/latelierkiyose' )`

Chaque entrée retourne :

- `key`
- `label`
- `url`
- `icon`

Créer `kiyose_get_social_links_html( string $context ): string` avec deux contextes :

| Contexte | Classes attendues |
|---|---|
| `footer` | markup compatible `.site-footer__social` |
| `contact` | markup compatible `.social-links` |

### 5. Factoriser les liens de partage d'article

Créer `kiyose_get_post_share_links_html( int $post_id ): string`.

Le composant doit générer les liens Facebook, LinkedIn et le bouton copie Instagram avec les mêmes classes qu'aujourd'hui dans `single.php`.

## Fichiers à modifier

| Fichier | Action |
|---|---|
| `latelierkiyose/functions.php` | Charger `inc/components.php` |
| `latelierkiyose/inc/components.php` | Nouveau module composants |
| `latelierkiyose/inc/shortcodes.php` | Utiliser le helper carousel |
| `latelierkiyose/templates/page-home.php` ou `template-parts/home/testimonials.php` | Utiliser le helper carousel |
| `latelierkiyose/footer.php` | Utiliser le composant profils sociaux |
| `latelierkiyose/templates/page-contact.php` | Utiliser le composant profils sociaux |
| `latelierkiyose/single.php` | Utiliser le composant partage article |
| `latelierkiyose/tests/Test_Components.php` | Nouveaux tests de rendu |

## Accessibilité

- Les labels des boutons carousel doivent rester en français et explicites.
- Les SVG décoratifs restent `aria-hidden="true"` et `focusable="false"`.
- Les liens externes gardent `target="_blank"` et `rel="noopener noreferrer"`.
- Le bouton de copie conserve un `aria-label` incluant le titre de l'article.

## Hors périmètre

- Refaire le JavaScript du carousel.
- Modifier les styles `.carousel`, `.social-links`, `.site-footer__social` ou `.blog-single__share`.
- Ajouter de nouveaux réseaux sociaux.

## Tests et validation

### Tests automatisés

- [ ] Tester que le carousel retourne une région avec `aria-roledescription="carousel"`.
- [ ] Tester que le nombre de slides et les labels `N sur TOTAL` sont corrects.
- [ ] Tester que `kiyose_get_social_profiles()` ignore les URLs vides.
- [ ] Tester que le rendu contact contient une liste `.social-links`.
- [ ] Tester que le rendu footer ne sort rien si aucun profil social n'est configuré.
- [ ] `./bin/phpunit.sh --testdox` passe.

### Validation manuelle

- [ ] Le carousel home fonctionne au clavier et à la souris.
- [ ] Le shortcode `[kiyose_testimonials display="carousel"]` affiche le même composant.
- [ ] Footer et page contact affichent les mêmes URLs sociales.
- [ ] Les boutons de partage d'article gardent leur comportement, dont la copie du lien.
