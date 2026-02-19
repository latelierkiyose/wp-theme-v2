# PRD 0021 — Optimisation du chemin critique de rendu

## Statut

**brouillon**

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

**Cause racine** : le navigateur doit télécharger et parser **21 à 23 fichiers CSS** et **plusieurs scripts render-blocking** avant de peindre quoi que ce soit.

### Diagnostic détaillé

**CSS chargés sur CHAQUE page** (21 fichiers, ~130 KB non minifiés) :

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
| kintsugi.css | 6.1 KB | Non — pages avec décorations Kintsugi |
| brevo-override.css | 6.9 KB | Non — pages avec formulaire newsletter |

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
| hero.css | `is_page_template('templates/page-home.php') \|\| is_page_template('templates/page-services.php')` |
| service-card.css | `is_page_template('templates/page-home.php') \|\| is_page_template('templates/page-services.php')` |
| home-sections.css | `is_page_template('templates/page-home.php')` |
| kintsugi.css | `is_page_template('templates/page-home.php')` |
| page.css | `is_page() && !is_page_template()` (pages génériques sans template custom) |

**CSS toujours chargés** (9 fichiers, ~43 KB — le noyau) :
- fonts.css, variables.css, plugins-common.css, header.css, navigation.css, footer.css, main.css, animations.css, gutenberg-blocks.css

**Résultat attendu** : La homepage passe de 23 requêtes CSS à ~15. Les pages intérieures sans hero/services/blog passent de 21 à ~9.

### 2. Chargement conditionnel des assets Events Manager

**Problème** : `events-manager.css` (50 KB) et `events-manager.js` (118 KB) sont chargés sur la homepage et d'autres pages via `kiyose_override_events_manager_styles()`.

**Solution** : Restreindre le chargement au strict nécessaire.

Dans `inc/enqueue.php`, modifier `kiyose_should_load_events_manager()` (ou créer cette fonction helper) :

```php
function kiyose_should_load_events_manager() {
	if ( is_page_template( 'templates/page-calendar.php' ) ) {
		return true;
	}

	global $post;
	if ( $post && has_shortcode( $post->post_content, 'events_list' ) ) {
		return true;
	}

	return false;
}
```

**Homepage** : Les prochains événements sur la homepage sont affichés via un appel PHP direct (`EM_Events::get()`), pas via un shortcode JS. Vérifier si les assets Events Manager sont réellement nécessaires pour le rendu de cette section. Si seul le CSS est nécessaire (pas le JS), charger uniquement le CSS.

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
		'kiyose-kintsugi',
		'kiyose-brevo-override',
	);

	if ( in_array( $handle, $async_handles, true ) ) {
		$html = str_replace(
			"media='all'",
			"media='print' onload=\"this.media='all'\"",
			$html
		);
	}

	return $html;
}
add_filter( 'style_loader_tag', 'kiyose_async_stylesheet', 10, 2 );
```

### 5. Correction du fichier de police manquant

**Problème** : Lighthouse détecte une erreur 404 pour `caveat-v21-latin-regular.woff2`. Le fichier est référencé dans `fonts.css` mais absent du dossier `assets/fonts/`.

**Solution** : Vérifier si le fichier existe dans le repo. Si absent, le télécharger depuis Google Fonts (police Caveat, format WOFF2) et le placer dans `assets/fonts/`.

### 6. Preconnect pour ressources externes

Si des ressources externes sont chargées (Google reCAPTCHA pour CF7/Brevo), ajouter des hints de preconnect :

```php
function kiyose_resource_hints() {
	echo '<link rel="preconnect" href="https://www.gstatic.com" crossorigin>';
	echo '<link rel="dns-prefetch" href="https://www.gstatic.com">';
}
add_action( 'wp_head', 'kiyose_resource_hints', 2 );
```

## Fichiers impactés

### Fichiers modifiés

| Fichier | Modification |
|---------|-------------|
| `inc/enqueue.php` | Conditions de chargement CSS, jQuery footer, CSS critique inline, async stylesheets, preconnect |
| `assets/css/fonts.css` | Vérifier/corriger le chemin du fichier Caveat |

### Fichiers créés

| Fichier | Contenu |
|---------|---------|
| `assets/css/critical.css` | CSS critique above-the-fold (~3-5 KB) |

## Accessibilité

- Aucun impact sur l'accessibilité — les styles sont les mêmes, seul l'ordre de chargement change
- Le pattern `media="print" onload="this.media='all'"` nécessite un fallback `<noscript>` pour les utilisateurs sans JS :

```php
function kiyose_noscript_styles() {
	$async_handles = array( 'kiyose-animations', 'kiyose-gutenberg-blocks', 'kiyose-kintsugi', 'kiyose-brevo-override' );
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
| Requêtes CSS (homepage) | 23 | ~12 |
| Requêtes CSS (page intérieure) | 21 | ~9 |
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

## Risques et rollback

| Risque | Mitigation |
|--------|-----------|
| jQuery en footer casse un plugin | Tester chaque plugin. Rollback : supprimer `kiyose_move_jquery_to_footer()` |
| CSS conditionnel oublie une page | Tester chaque type de page. Les conditions utilisent les fonctions WordPress standard |
| CSS critique incomplet (FOUC) | Le CSS critique est additionnel aux stylesheets normales — au pire un flash de contenu non stylé sur les éléments hors critical |
| Events Manager nécessite ses assets sur la homepage | Vérifier le rendu de la section événements. Rollback : remettre le chargement conditionnel précédent |
