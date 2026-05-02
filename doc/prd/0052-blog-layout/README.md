# PRD 0052 — Alignement du header des articles de blog

- **Statut** : terminé
- **Version** : 1.0
- **Date** : 2026-05-02

## Contexte

Sur une page d'article de blog rendue par `single.php`, le header éditorial n'est pas cohérent avec les autres templates de pages du thème.

Exemple observé en local :

- `http://localhost:8000/2025/02/10/vers-plus-de-joie/`

Comportement actuel :

- la date est rendue avant le titre dans le DOM ;
- le titre `.blog-single__title` hérite du soulignement doré global des `h1`, mais il reste aligné à gauche ;
- la date `.blog-single__date` est elle aussi alignée à gauche, ce qui peut la placer dans la zone visuelle du logo sur les petits viewports ;
- le template `À propos` et les autres templates internes utilisent plutôt un header centré, plus cohérent avec l'identité du site.

Point d'attention : `single.php` peut aussi servir de fallback à d'autres types de contenus si aucun template plus spécifique n'existe. Le changement doit donc rester limité au composant `.blog-single` et ne pas modifier le header global, le logo ou les templates de pages.

## Problème à résoudre

L'utilisateur doit percevoir immédiatement :

1. le titre de l'article comme information principale ;
2. la date comme métadonnée secondaire, placée sous le titre et sous son soulignement doré ;
3. un header centré qui ne donne pas l'impression que la date est masquée ou coincée sous le logo.

Le PRD ne doit pas prescrire un simple repositionnement visuel de la date en CSS si l'ordre HTML reste date → titre. Cela créerait un écart entre l'ordre lu par les technologies d'assistance et l'ordre visible.

## Objectif

Recomposer le header des articles uniques pour obtenir l'ordre et l'alignement suivants :

1. titre centré ;
2. soulignement doré décoratif du `h1`, conservé ;
3. date centrée sous le soulignement ;
4. image à la une, si présente, rendue sous la date ;
5. contenu de l'article inchangé.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Ordre du contenu | Rendre le `h1` avant le `time` dans `single.php` | L'ordre sémantique doit suivre l'ordre visuel : titre puis métadonnée |
| Alignement | Centrer `.blog-single__header`, `.blog-single__title` et `.blog-single__date` | Cohérent avec `about-page`, `service-page`, `calendar-page` et le template page par défaut |
| Soulignement doré | Réutiliser le `h1::after` global existant | Évite de dupliquer un SVG décoratif dans `blog-single.css` |
| Logo sticky | Ne pas modifier `.site-header` ni `.site-header__logo-bubble` | Le problème demandé concerne le header article ; changer le logo aurait un risque global |
| Scope CSS | Modifier uniquement `blog-single.css` | Évite les régressions sur les templates de pages, archives et cartes blog |
| Date | Garder une date textuelle simple, non interactive | Aucun besoin produit d'ajouter auteur, catégorie ou nouvelle métadonnée |
| Image à la une | Ne pas changer le comportement de l'image dans ce PRD | La taille desktop de l'image est couverte par le PRD 0051 |

## Spécifications

### 1. Réordonner le header dans `single.php`

Dans `latelierkiyose/single.php`, modifier le header de l'article pour rendre le titre avant la date.

Structure attendue :

```php
<header class="blog-single__header">
	<h1 class="blog-single__title"><?php the_title(); ?></h1>
	<time class="blog-single__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
		<?php echo esc_html( get_the_date() ); ?>
	</time>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="blog-single__featured-image">
			<?php
			the_post_thumbnail(
				'large',
				array(
					'alt' => get_the_title(),
				)
			);
			?>
		</div>
	<?php endif; ?>
</header>
```

Règles attendues :

- conserver le `datetime` ISO existant ;
- conserver l'échappement `esc_attr()` et `esc_html()` ;
- ne pas ajouter de date alternative, d'auteur ou de catégorie ;
- ne pas déplacer l'image à la une hors du header ;
- ne pas changer les liens de partage, catégories, tags ou navigation entre articles.

### 2. Centrer le header article

Modifier `latelierkiyose/assets/css/components/blog-single.css`.

Comportement attendu :

- `.blog-single__header` est centré ;
- `.blog-single__title` conserve la typographie et le soulignement doré global du `h1` ;
- `.blog-single__date` est centrée, visuellement secondaire, et placée sous le soulignement du titre ;
- l'espacement vertical reste équilibré entre titre, date, image et contenu ;
- le rendu mobile ne doit pas placer la date ou le titre sous la bulle du logo.

Exemple de direction CSS :

```css
.blog-single__header {
	margin-bottom: var(--kiyose-spacing-3xl);
	text-align: center;
}

.blog-single__title {
	color: var(--kiyose-color-burgundy);
	font-size: var(--kiyose-font-size-4xl);
	line-height: var(--kiyose-line-height-tight);
	margin-bottom: var(--kiyose-spacing-sm);
}

.blog-single__date {
	color: var(--kiyose-color-text-light);
	display: block;
	font-size: var(--kiyose-font-size-sm);
	margin-bottom: var(--kiyose-spacing-xl);
}
```

L'implémentation peut ajuster les espacements si la validation visuelle montre un déséquilibre, mais elle doit préserver l'intention : titre → soulignement → date → image.

### 3. Régénérer les assets minifiés

Régénérer les fichiers générés par la chaîne de build existante :

- `latelierkiyose/assets/css/components/blog-single.min.css`
- `latelierkiyose/assets/css/components/blog-single.min.css.map`

Ne pas éditer les fichiers minifiés manuellement.

### 4. Ajouter un test de contrat minimal

Ajouter un test PHPUnit ciblé dans `latelierkiyose/tests/Test_Blog.php` pour éviter une régression de l'ordre du header.

Cas attendu :

- `singleTemplate_ordersTitleBeforeDate`

Le test peut lire `single.php` et vérifier que `blog-single__title` apparaît avant `blog-single__date`. Il ne doit pas se limiter à vérifier que le fichier existe.

## Fichiers à modifier

| Fichier | Action |
|---|---|
| `latelierkiyose/single.php` | Placer le `h1.blog-single__title` avant `time.blog-single__date` |
| `latelierkiyose/assets/css/components/blog-single.css` | Centrer le header et ajuster l'espacement titre/date |
| `latelierkiyose/assets/css/components/blog-single.min.css` | Régénérer |
| `latelierkiyose/assets/css/components/blog-single.min.css.map` | Régénérer |
| `latelierkiyose/tests/Test_Blog.php` | Ajouter le test de contrat sur l'ordre titre/date |

## Accessibilité et performance

- L'ordre DOM doit correspondre à l'ordre visuel : titre puis date.
- Le soulignement doré reste décoratif via `h1::after` et n'ajoute aucun contenu lu par les technologies d'assistance.
- Aucun nouvel élément interactif n'est ajouté.
- Le focus clavier n'est pas impacté.
- Les contrastes existants doivent rester conformes WCAG 2.2 AA :
  - titre bordeaux sur fond clair ;
  - date `--kiyose-color-text-light` sur fond clair.
- Aucun nouveau téléchargement ni script n'est introduit.

## Documentation

Aucune documentation utilisateur n'est requise : le changement ne crée ni shortcode, ni classe CSS rédacteur, ni template de page, ni option admin.

Aucune documentation release n'est requise : le changement n'ajoute pas de plugin, de migration, de nouvelle option, de hook d'activation ou de prérequis de production.

## Hors périmètre

- Modifier le logo, sa taille, son `z-index` ou le comportement sticky du header.
- Refondre le template `single.php` au-delà du header titre/date/image.
- Ajouter une nouvelle métadonnée d'article : auteur, catégorie, temps de lecture, dernière mise à jour.
- Modifier les cartes blog, les archives blog ou le template de recherche.
- Modifier le design de la page `À propos`.
- Modifier la taille de l'image à la une desktop, déjà couverte par le PRD 0051.
- Créer un template Events Manager dédié.

## Risques et points d'attention

| Risque | Mitigation |
|---|---|
| La date est déplacée seulement en CSS, sans corriger le DOM | Exiger le réordonnancement dans `single.php` et ajouter le test `singleTemplate_ordersTitleBeforeDate` |
| Le centrage du `h1` casse l'affichage du soulignement global | Vérifier que le `h1` reste `inline-block` via les styles globaux et que `h1::after` reste centré |
| Le logo recouvre encore le header sur très petit viewport | Tester à `320px` et ajuster uniquement l'espacement du composant `.blog-single` si nécessaire |
| Un contenu non-blog utilisant `single.php` hérite du centrage | Valider au moins un contenu fallback connu ; créer un PRD dédié si le besoin diffère par type de contenu |
| Les minifiés deviennent incohérents | Régénérer les assets et inclure la source map |

## Tests et validation

### Validation automatisée

- [ ] `./bin/phpunit.sh --testdox` passe, incluant `singleTemplate_ordersTitleBeforeDate`.
- [ ] `npm run lint:css` passe.
- [ ] `make phpcs` passe.
- [ ] `make test` passe.
- [ ] Les fichiers minifiés sont à jour après le build.

### Validation visuelle

Tester l'URL d'exemple :

- [ ] Desktop `>= 1024px` : le titre est centré.
- [ ] Desktop `>= 1024px` : le soulignement doré du `h1` est centré sous le titre.
- [ ] Desktop `>= 1024px` : la date est centrée sous le soulignement et avant l'image à la une.
- [ ] Tablet `768px - 1023px` : le header reste centré et ne se superpose pas au logo.
- [ ] Mobile `320px - 767px` : le titre et la date restent lisibles, sans chevauchement avec la bulle du logo.
- [ ] Article sans image à la une : le header conserve un espacement cohérent avant le contenu.
- [ ] Article avec image à la une : l'image reste sous la date et conserve son ratio.
- [ ] Navigation clavier : le lien d'évitement, les liens de partage et la navigation entre articles restent accessibles.
