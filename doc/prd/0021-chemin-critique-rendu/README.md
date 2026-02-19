# PRD 0021 — Optimisation du chemin critique de rendu

## Statut

**prêt à l'implémentation**

## Objectif

Réduire drastiquement le First Contentful Paint (FCP) et le Largest Contentful Paint (LCP) en optimisant le chemin critique de rendu : rendre les CSS conditionnels, éliminer les ressources render-blocking inutiles, et inliner le CSS critique au-dessus de la ligne de flottaison.

## Contexte

Le score Lighthouse Performance est de **56/100** sur l'environnement local. Les métriques critiques :

| Métrique | Valeur actuelle | Objectif |
|----------|----------------|----------|
| FCP | 9.1s | < 1.8s |
| LCP | 16.6s | < 2.5s |
| Speed Index | 9.1s | < 3.4s |
| TBT | 0ms | < 200ms (OK) |
| CLS | 0 | < 0.1 (OK) |

**Cause racine** : le navigateur doit télécharger et parser **~18 fichiers CSS inconditionnels** (plus les CSS conditionnels déjà en place selon la page) et **plusieurs scripts render-blocking** avant de peindre quoi que ce soit.

### Diagnostic détaillé

**CSS chargés inconditionnellement sur CHAQUE page** (~18 fichiers, ~100 KB non minifiés) :

> **Note** : Plusieurs fichiers CSS sont déjà chargés conditionnellement (voir tableau des CSS déjà conditionnels plus bas). Le nombre total de CSS sur une page donnée dépend du template utilisé.

| Fichier | Taille | Nécessaire partout ? |
|---------|--------|---------------------|
| fonts.css | 1.6 KB | Oui |
| variables.css | 4.1 KB | Oui |
| plugins-common.css | 7.1 KB | Oui (plugins présents partout) |
| header.css | 4.6 KB | Oui |
| navigation.css | 7.8 KB | Oui |
| footer.css | 4.3 KB | Oui |
| main.css | 4.6 KB | Oui |
| animations.css | 2.5 KB | Oui |
| gutenberg-blocks.css | 5.7 KB | Oui (Gutenberg utilisé partout) |
| page.css | 2.8 KB | Non — pages génériques uniquement |
| search.css | 5.6 KB | Non — page de recherche uniquement |
| 404.css | 3.4 KB | Non — page 404 uniquement |
| blog-card.css | 4.1 KB | Non — pages avec articles uniquement |
| blog-archive.css | 4.1 KB | Non — archive blog uniquement |
| blog-single.css | 10.9 KB | Non — article seul uniquement |
| testimony.css | 2.2 KB | Non — pages avec témoignages uniquement |
| hero.css | 3.8 KB | Non — pages avec hero uniquement |
| service-card.css | 2.8 KB | Non — pages avec services uniquement |
| home-sections.css | 5.3 KB | Non — homepage uniquement |
| kintsugi.css | 6.1 KB | Oui — `.section-divider--header` utilisé dans `header.php` sur toutes les pages |
| brevo-override.css | 6.9 KB | Oui — le formulaire newsletter Brevo est dans `footer.php` sur toutes les pages |

**CSS déjà chargés conditionnellement** (pas de changement nécessaire) :

| Fichier | Condition actuelle |
|---------|-------------------|
| service-page.css | `is_page_template('templates/page-services.php')` |
| about-page.css | `is_page_template('templates/page-about.php')` |
| contact-page.css + cf7-override.css | `is_page_template('templates/page-contact.php')` |
| events-manager.css | Calendrier, homepage, ou shortcode Events Manager |
| carousel.css | Homepage |
| testimonials-grid.css | Shortcode `[kiyose_testimonials]` |
| signets.css | Shortcode `[kiyose_signets]` |
| home-animations.css | Homepage |

**Ressources render-blocking les plus coûteuses** (Lighthouse) :

| Ressource | Taille | Temps gaspillé |
|-----------|--------|---------------|
| events-manager.min.css | ~50 KB | 2 702 ms |
| events-manager.js | ~118 KB | 1 802 ms |
| jquery.min.js | ~31 KB | 1 652 ms |
| mailin-front.js (Brevo) | ~3.5 KB | 302 ms |

## Dépendances

- **PRD 0019** : Minification des assets (complémentaire — réduit la taille, ce PRD réduit le nombre de requêtes)
- **PRD 0002** : Design tokens (variables CSS)

## Spécifications

### 1. Chargement CSS conditionnel

**Principe** : Ne charger un fichier CSS que sur les pages qui l'utilisent.

**Modifications dans `inc/enqueue.php`**, fonction `kiyose_enqueue_assets()` :

| Fichier CSS | Condition de chargement |
|-------------|------------------------|
| search.css | `is_search()` |
| 404.css | `is_404()` |
| blog-card.css | `is_home() \|\| is_archive() \|\| is_page_template('templates/page-home.php')` |
| blog-archive.css | `is_home() \|\| is_archive()` |
| blog-single.css | `is_singular('post')` |
| testimony.css | `is_page_template('templates/page-home.php') \|\| has_shortcode(...)` |
| hero.css | `is_page_template('templates/page-home.php')` |
| service-card.css | `is_page_template('templates/page-home.php') \|\| is_page_template('templates/page-services.php')` |
| home-sections.css | `is_page_template('templates/page-home.php')` |
| page.css | `is_page() && !is_page_template()` (pages génériques sans template custom) |

> **Note hero.css** : `hero.css` ne contient que les styles `.hero-section` (hero de la homepage). La page services utilise `.service-page__hero-image` qui est dans `service-page.css`. Ne pas charger `hero.css` sur la page services.

> **Note kintsugi.css** : `kintsugi.css` contient les styles `.section-divider--header` utilisés dans `header.php` sur **toutes les pages**. Ce fichier doit rester chargé inconditionnellement. Une optimisation future pourrait déplacer `.section-divider--header` et `.section-divider` (base) vers `header.css`, ce qui permettrait de rendre le reste de `kintsugi.css` conditionnel à la homepage.

**CSS toujours chargés** (11 fichiers, ~56 KB — le noyau) :
- fonts.css, variables.css, plugins-common.css, header.css, navigation.css, footer.css, main.css, animations.css, gutenberg-blocks.css, kintsugi.css, brevo-override.css

> **Note brevo-override.css** : Bien que ce fichier soit un candidat à l'async loading (§4), il doit rester chargé sur toutes les pages car le formulaire newsletter Brevo est présent dans `footer.php` sur chaque page.

**Nettoyage du tableau de dépendances `kiyose-main`** : Le style `kiyose-main` (ligne 234 de `enqueue.php`) déclare tous les CSS composants comme dépendances. Quand un CSS devient conditionnel (ex: `kiyose-page` non enregistré hors pages génériques), WordPress le traite comme une dépendance manquante — `kiyose-main` se charge quand même, mais ça laisse des handles non résolus dans le tracking interne. **Il faut mettre à jour le tableau de dépendances de `kiyose-main`** pour ne lister que les CSS toujours chargés (le noyau) :

```php
array( 'kiyose-fonts', 'kiyose-variables', 'kiyose-header', 'kiyose-navigation', 'kiyose-footer', 'kiyose-kintsugi', 'kiyose-animations', 'kiyose-gutenberg-blocks' )
```

**Résultat attendu** : La homepage passe de ~18 requêtes CSS inconditionnelles à ~11 + les conditionnelles pertinentes. Les pages intérieures sans hero/services/blog passent à ~11 fichiers CSS.

### 2. Chargement conditionnel des assets Events Manager

**Problème** : `events-manager.css` (50 KB) et `events-manager.js` (118 KB) sont chargés sur la homepage et d'autres pages via `kiyose_override_events_manager_styles()`.

**Solution** : Restreindre le chargement au strict nécessaire.

La fonction `kiyose_override_events_manager_styles()` existante gère déjà correctement ce chargement conditionnel. **Ne pas modifier sa logique de détection.** L'objectif ici est de compléter par le dequeue des assets Events Manager natifs sur les pages hors périmètre.

**État actuel** (correct, à conserver) :

```php
// Dans kiyose_override_events_manager_styles() — existant
if ( is_page_template( 'templates/page-calendar.php' ) || is_page_template( 'templates/page-home.php' ) ) {
	$should_load = true;
}
// + détection shortcodes dans $post->post_content
```

> **Important** : La homepage charge les événements via `do_shortcode( '[events_list ...]' )` dans `page-home.php:183`. Ce shortcode est écrit directement dans le template PHP, pas dans `$post->post_content`. La condition `is_page_template('templates/page-home.php')` est donc **indispensable** car `has_shortcode()` ne détecterait pas ce cas.

**Note** : Events Manager enqueue ses propres scripts via ses hooks. Utiliser `wp_dequeue_script('events-manager')` et `wp_dequeue_style('events-manager')` aux priorités appropriées pour les pages hors périmètre.

### 3. Defer des scripts non-critiques

**jQuery** : Chargé par WordPress core et requis par des plugins (Events Manager, CF7, Brevo). Ne peut pas être supprimé, mais peut être déplacé en footer.

```php
function kiyose_move_jquery_to_footer() {
	if ( ! is_admin() ) {
		wp_scripts()->add_data( 'jquery', 'group', 1 );
		wp_scripts()->add_data( 'jquery-core', 'group', 1 );
		wp_scripts()->add_data( 'jquery-migrate', 'group', 1 );
	}
}
add_action( 'wp_enqueue_scripts', 'kiyose_move_jquery_to_footer', 1 );
```

**Attention** : Tester que les plugins fonctionnent toujours avec jQuery en footer. Si un plugin inline du jQuery dans le `<head>`, ça cassera. Rollback si nécessaire.

### 4. CSS critique inline (above-the-fold)

**Principe** : Inliner dans le `<head>` le CSS minimal nécessaire pour rendre le contenu visible sans scroll, puis charger le reste en asynchrone.

**Approche** : Extraction manuelle du CSS critique. Le CSS critique comprend :
- Variables CSS utilisées par le header/hero
- Styles du header (`.site-header`)
- Styles de navigation (`.main-nav`)
- Styles du hero (`.hero-section`) — homepage uniquement
- Reset/base minimal (box-sizing, body font)

**Implémentation** :

1. Créer `assets/css/critical.css` (~3-5 KB) contenant le CSS above-the-fold
2. Dans `inc/enqueue.php`, inliner ce fichier dans le `<head>` :

```php
function kiyose_inline_critical_css() {
	$critical_css_path = get_template_directory() . '/assets/css/critical.css';
	if ( file_exists( $critical_css_path ) ) {
		echo '<style id="kiyose-critical-css">';
		include $critical_css_path;
		echo '</style>';
	}
}
add_action( 'wp_head', 'kiyose_inline_critical_css', 1 );
```

3. Charger le reste des CSS en asynchrone via le pattern `media="print"` :

```php
function kiyose_async_stylesheet( $html, $handle ) {
	$async_handles = array(
		'kiyose-animations',
		'kiyose-gutenberg-blocks',
		'kiyose-brevo-override',
	);

	if ( in_array( $handle, $async_handles, true ) ) {
		// Gère les deux styles de guillemets (simples et doubles)
		// WordPress core utilise des guillemets simples, mais certains plugins/filtres
		// peuvent produire des guillemets doubles.
		$html = preg_replace(
			'/media=[\'"]all[\'"]/',
			"media='print' onload=\"this.media='all'\"",
			$html
		);
	}

	return $html;
}
add_filter( 'style_loader_tag', 'kiyose_async_stylesheet', 10, 2 );
```

> **Note** : `kiyose-kintsugi` a été retiré de la liste async car il est nécessaire au rendu du header (`.section-divider--header`) et doit se charger de manière synchrone.

**Maintenance de `critical.css`** : Le fichier `critical.css` est extrait manuellement de `variables.css`, `header.css`, `navigation.css`, et `hero.css`. Toute modification de ces fichiers source doit être répercutée dans `critical.css`.

Processus de synchronisation :
- Ajouter une entrée dans la checklist de vérification (§ Vérification) : vérifier la cohérence de `critical.css` avec ses sources
- Lors de la revue de code, tout changement à `variables.css`, `header.css`, `navigation.css`, ou `hero.css` doit vérifier si `critical.css` nécessite une mise à jour
- Un commentaire en en-tête de `critical.css` listera les fichiers sources et leur date de dernière synchronisation

### 5. Correction du fichier de police manquant

**Problème** : Lighthouse détecte une erreur 404 pour `caveat-v21-latin-regular.woff2`. Le fichier est référencé dans `inc/enqueue.php:449` via un `<link rel="preload">` dans la fonction `kiyose_preload_fonts()`, mais **absent du dossier `assets/fonts/`**. Le fichier `fonts.css` ne contient aucune référence à Caveat — il ne définit que les `@font-face` pour Nunito et Dancing Script.

**Solution** : Deux options possibles (choisir l'une ou l'autre) :

1. **Si Caveat n'est plus utilisée** : Supprimer le `<link rel="preload">` de `kiyose_preload_fonts()` dans `enqueue.php:449`.
2. **Si Caveat est nécessaire** : Télécharger le fichier WOFF2 depuis Google Fonts, le placer dans `assets/fonts/`, ET ajouter la déclaration `@font-face` correspondante dans `fonts.css`.

### 6. Preconnect pour ressources externes

Si des ressources externes sont chargées (Google reCAPTCHA pour CF7), ajouter des hints de preconnect **uniquement sur les pages concernées**. reCAPTCHA n'est chargé que sur les pages avec Contact Form 7 — ajouter un preconnect sur toutes les pages gaspillerait une connexion réseau.

```php
function kiyose_resource_hints() {
	// reCAPTCHA n'est nécessaire que sur les pages avec formulaire CF7.
	if ( is_page_template( 'templates/page-contact.php' ) ) {
		echo '<link rel="preconnect" href="https://www.gstatic.com" crossorigin>';
		echo '<link rel="dns-prefetch" href="https://www.gstatic.com">';
	}
}
add_action( 'wp_head', 'kiyose_resource_hints', 2 );
```

### 7. Correction de bugs pré-existants dans `enqueue.php`

Ces bugs ne sont pas causés par ce PRD mais doivent être corrigés lors de l'implémentation pour éviter des erreurs PHP :

**7a. Variable `$suffix` non définie dans deux fonctions** :
- `kiyose_enqueue_testimonials_styles()` (ligne 325) utilise `$suffix` sans le définir
- `kiyose_enqueue_signets_styles()` (ligne 367) utilise `$suffix` sans le définir

**Correction** : Ajouter `$suffix = kiyose_get_asset_suffix();` au début de chaque fonction.

**7b. Preload de police inexistante** : Corrigé dans §5 ci-dessus.

## Fichiers impactés

### Fichiers modifiés

| Fichier | Modification |
|---------|-------------|
| `inc/enqueue.php` | Conditions de chargement CSS, dépendances `kiyose-main`, jQuery footer, CSS critique inline, async stylesheets, preconnect conditionnel, correction `$suffix` manquant (§7), correction preload Caveat (§5) |

### Fichiers créés

| Fichier | Contenu |
|---------|---------|
| `assets/css/critical.css` | CSS critique above-the-fold (~3-5 KB) |

## Accessibilité

- Aucun impact sur l'accessibilité — les styles sont les mêmes, seul l'ordre de chargement change
- Le pattern `media="print" onload="this.media='all'"` nécessite un fallback `<noscript>` pour les utilisateurs sans JS :

```php
function kiyose_noscript_styles() {
	$async_handles = array( 'kiyose-animations', 'kiyose-gutenberg-blocks', 'kiyose-brevo-override' );
	echo '<noscript>';
	foreach ( $async_handles as $handle ) {
		$src = wp_styles()->registered[ $handle ]->src ?? '';
		if ( $src ) {
			echo '<link rel="stylesheet" href="' . esc_url( $src ) . '">';
		}
	}
	echo '</noscript>';
}
add_action( 'wp_head', 'kiyose_noscript_styles', 99 );
```

## Performance attendue

| Métrique | Avant | Après (estimé) |
|----------|-------|----------------|
| Requêtes CSS (homepage) | ~18 inconditionnels + conditionnels homepage | ~11 + conditionnels |
| Requêtes CSS (page intérieure) | ~18 inconditionnels | ~11 |
| FCP | 9.1s | ~3-4s |
| LCP | 16.6s | ~5-8s |
| Render-blocking resources | ~38 items | ~10 items |
| Score Performance | 56 | ~70-75 |

**Note** : Les améliorations supplémentaires (cache, compression images) sont couvertes par PRD 0024 (plugins) et devraient porter le score à 85+.

## Vérification

1. **Requêtes réseau** : DevTools > Network > filtrer "CSS" — vérifier que seuls les CSS pertinents sont chargés sur chaque page
2. **Render-blocking** : Lighthouse > Performance > "Eliminate render-blocking resources" — liste réduite
3. **FCP/LCP** : Lighthouse > Performance — FCP < 4s, LCP < 8s (local)
4. **Rendu visuel** : Aucune régression visuelle — le site doit être identique visuellement
5. **Navigation** : Tester toutes les pages (home, services, contact, calendrier, blog, 404, recherche)
6. **Noscript** : Désactiver JavaScript dans le navigateur — vérifier que les styles se chargent via `<noscript>`
7. **PHPCS** : `make phpcs` — aucune violation
8. **Plugins** : Vérifier que Events Manager, CF7, Brevo fonctionnent toujours correctement
9. **jQuery en footer** : Tester les fonctionnalités dépendantes de jQuery (formulaires, calendrier)
10. **critical.css** : Vérifier que `critical.css` est cohérent avec ses fichiers sources (`variables.css`, `header.css`, `navigation.css`, `hero.css`)
11. **Bugs corrigés** : Vérifier que `$suffix` est défini dans `kiyose_enqueue_testimonials_styles()` et `kiyose_enqueue_signets_styles()`

## Risques et rollback

| Risque | Mitigation |
|--------|-----------|
| jQuery en footer casse un plugin | Tester chaque plugin. Rollback : supprimer `kiyose_move_jquery_to_footer()` |
| CSS conditionnel oublie une page | Tester chaque type de page. Les conditions utilisent les fonctions WordPress standard |
| CSS critique incomplet (FOUC) | Le CSS critique est additionnel aux stylesheets normales — au pire un flash de contenu non stylé sur les éléments hors critical |
| Events Manager nécessite ses assets sur la homepage | Vérifier le rendu de la section événements. Rollback : remettre le chargement conditionnel précédent |
