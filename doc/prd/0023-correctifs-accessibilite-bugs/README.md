# PRD 0023 — Correctifs accessibilité et bugs

## Statut

**brouillon**

## Objectif

Corriger les bugs et violations d'accessibilité détectés par Lighthouse pour atteindre un score Accessibility de 100 et éliminer les erreurs console.

## Contexte

Le score Lighthouse Accessibility est de **93/100**. Trois catégories de problèmes ont été identifiées :

1. **Bugs PHP** : Variables non définies, attributs redondants, incohérences HTML
2. **Violations d'accessibilité** : Hiérarchie des headings, champs de formulaire sans labels
3. **Erreurs console** : Police manquante, double rendu reCAPTCHA

## Dépendances

Aucune.

## Spécifications

### 1. Bug : variable `$suffix` non définie (2 fonctions)

**Impact** : Notice PHP + chemins CSS incorrects en production (le `.min` n'est jamais ajouté).

#### 1a. `kiyose_enqueue_testimonials_styles()`

**Fichier** : `inc/enqueue.php`, fonction `kiyose_enqueue_testimonials_styles()` (lignes 325-358)

Les lignes 332 et 351 utilisent `$suffix` sans le définir.

**Correction** : Ajouter `$suffix = kiyose_get_asset_suffix();` au début de la fonction (après la ligne 326).

#### 1b. `kiyose_enqueue_signets_styles()`

**Fichier** : `inc/enqueue.php`, fonction `kiyose_enqueue_signets_styles()` (lignes 367-380)

La ligne 374 utilise `$suffix` sans le définir.

**Correction** : Ajouter `$suffix = kiyose_get_asset_suffix();` au début de la fonction (après la ligne 368).

### 2. Bug : attribut `defer` redondant sur script `type="module"`

**Fichier** : `inc/enqueue.php`, fonction `kiyose_defer_main_script()` (lignes 437-445)

**Problème** : Le code ajoute à la fois `defer` et `type="module"` sur le script principal. Les scripts `type="module"` sont automatiquement différés par le navigateur — l'attribut `defer` est ignoré et redondant.

**Avant** :
```php
if ( 'kiyose-main' === $handle ) {
	$tag = str_replace( ' src', ' defer src', $tag );
	$tag = str_replace( '<script', '<script type="module"', $tag );
	return $tag;
}
```

**Après** :
```php
if ( 'kiyose-main' === $handle ) {
	$tag = str_replace( '<script', '<script type="module"', $tag );
	return $tag;
}
```

### 3. Bug : double élément `<main>` (incohérence d'ID)

**Problème** : `header.php` (ligne 85) ouvre `<main id="main-content" role="main">`. Mais tous les templates (page-home.php, page.php, single.php, archive.php, search.php, 404.php, etc.) définissent leur propre `<main id="main" class="site-main">`. Cela crée une structure HTML invalide avec deux éléments `<main>` imbriqués.

**Solution** : Deux approches possibles.

**Approche retenue** : Supprimer le `<main>` de `header.php` et conserver celui des templates.

- `header.php` : Remplacer `<main id="main-content" role="main">` par un commentaire ou supprimer la ligne
- `footer.php` : Supprimer le `</main>` correspondant
- Vérifier que TOUS les templates ouvrent et ferment leur propre `<main id="main-content" class="site-main" role="main">`
- Uniformiser l'ID sur `main-content` dans tous les templates (c'est l'ID référencé par le skip link dans `header.php`)

**Fichiers à modifier** :

| Fichier | Modification |
|---------|-------------|
| `header.php` | Supprimer `<main id="main-content" role="main">` (ligne 85) |
| `footer.php` | Supprimer le `</main>` correspondant |
| `templates/page-home.php` | `id="main"` → `id="main-content"` |
| `templates/page-services.php` | `id="main"` → `id="main-content"` |
| `templates/page-contact.php` | `id="main"` → `id="main-content"` |
| `templates/page-about.php` | `id="main"` → `id="main-content"` |
| `templates/page-calendar.php` | `id="main"` → `id="main-content"` |
| `page.php` | `id="main"` → `id="main-content"` |
| `single.php` | `id="main"` → `id="main-content"` |
| `archive.php` | `id="main"` → `id="main-content"` |
| `search.php` | `id="main"` → `id="main-content"` |
| `404.php` | `id="main"` → `id="main-content"` |
| `index.php` | `id="main"` → `id="main-content"` |

**Vérifier** : Le skip link dans `header.php` pointe vers `#main-content` — il doit correspondre à l'ID du `<main>`.

### 4. Accessibilité : hiérarchie des headings

#### 4a. Heading `<h4>` sans parent `<h2>`/`<h3>` sur page services

**Problème** : Lighthouse détecte un `<h4 class="wp-block-heading">Art-thérapie</h4>` dans le contenu Gutenberg d'une page services, sans `<h2>` ou `<h3>` intermédiaire au-dessus.

**Cause** : Le contenu est saisi dans l'éditeur Gutenberg par l'utilisateur. Le thème ne peut pas contrôler directement les niveaux de heading choisis dans l'éditeur.

**Solutions** :
1. **Corriger le contenu** : Dans l'admin WordPress, modifier les blocs heading pour utiliser `<h2>` ou `<h3>` au lieu de `<h4>`
2. **Documentation** : Ajouter dans le guide éditorial une note sur l'importance de la hiérarchie des headings

#### 4b. Heading `<h3>` dans le footer

**Fichier** : `footer.php`, ligne 38

**Problème** : `<h3 class="site-footer__newsletter-title">Newsletter</h3>` apparaît dans le footer sans `<h2>` parent dans le contexte du footer.

**Solution** : Remplacer par `<h2>` car le footer est une section autonome. Le heading du footer n'a pas besoin d'être subordonné aux headings du contenu principal.

**Avant** : `<h3 class="site-footer__newsletter-title">`
**Après** : `<h2 class="site-footer__newsletter-title">`

**Note** : Vérifier et ajuster les styles CSS si le `h3` avait un style spécifique basé sur le sélecteur d'élément.

### 5. Accessibilité : champs de formulaire Brevo sans labels

**Problème** : Lighthouse détecte 6 champs de formulaire sans labels, tous générés par le plugin Brevo (newsletter). Le formulaire apparaît 2 fois (homepage + footer), avec 3 champs chacun (email, prénom, nom).

**Sélecteurs CSS détectés** :
- `p.sib-email-area > input.sib-email-area`
- `p.sib-PRENOM-area > input.sib-PRENOM-area`
- `p.sib-NOM-area > input.sib-NOM-area`

**Cause** : Le plugin Brevo génère des `<input>` sans `<label>` associé ni `aria-label`.

**Solution** : Ajouter les labels manquants via JavaScript côté thème, puisque le HTML est généré par le plugin.

**Fichier à créer** : `assets/js/modules/brevo-a11y.js`

```javascript
function addBrevoLabels() {
	const fields = [
		{ selector: 'input.sib-email-area', label: 'Adresse e-mail' },
		{ selector: 'input.sib-PRENOM-area', label: 'Prénom' },
		{ selector: 'input.sib-NOM-area', label: 'Nom' },
	];

	fields.forEach( ( { selector, label } ) => {
		document.querySelectorAll( selector ).forEach( ( input ) => {
			if ( ! input.getAttribute( 'aria-label' ) ) {
				input.setAttribute( 'aria-label', label );
			}
		} );
	} );
}

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', addBrevoLabels );
} else {
	addBrevoLabels();
}
```

**Alternative** : Vérifier dans la configuration Brevo (admin WordPress > Brevo > Forms) s'il existe une option pour activer les labels HTML natifs. Si oui, préférer cette solution au JavaScript.

### 6. Erreur console : double rendu reCAPTCHA

**Problème** : Lighthouse détecte l'erreur `reCAPTCHA has already been rendered in this element`. Probablement causé par le chargement de reCAPTCHA par deux plugins (CF7 + Brevo).

**Solution** : Vérifier la configuration des plugins :
1. Dans CF7 > Intégration > reCAPTCHA : vérifier la version (v2 vs v3)
2. Dans Brevo > Forms : vérifier si reCAPTCHA est activé
3. Si les deux utilisent reCAPTCHA, désactiver sur l'un des deux ou configurer pour partager la même instance

**Note** : Ce problème est lié à la configuration des plugins, pas au code du thème. Documenter la configuration recommandée.

### 7. Erreur console : fichier de police 404

**Problème** : `caveat-v21-latin-regular.woff2` retourne une 404.

**Ce correctif est traité dans PRD 0021** (section 5 — correction du fichier de police manquant). Inclus ici pour référence.

## Fichiers impactés

### Fichiers modifiés

| Fichier | Modification |
|---------|-------------|
| `inc/enqueue.php` | Fix `$suffix` (2 fonctions) + supprimer `defer` redondant |
| `header.php` | Supprimer `<main id="main-content">` |
| `footer.php` | Supprimer `</main>` + `h3` → `h2` pour newsletter |
| `templates/page-home.php` | `id="main"` → `id="main-content"` |
| `templates/page-services.php` | `id="main"` → `id="main-content"` |
| `templates/page-contact.php` | `id="main"` → `id="main-content"` |
| `templates/page-about.php` | `id="main"` → `id="main-content"` |
| `templates/page-calendar.php` | `id="main"` → `id="main-content"` |
| `page.php` | `id="main"` → `id="main-content"` |
| `single.php` | `id="main"` → `id="main-content"` |
| `archive.php` | `id="main"` → `id="main-content"` |
| `search.php` | `id="main"` → `id="main-content"` |
| `404.php` | `id="main"` → `id="main-content"` |
| `index.php` | `id="main"` → `id="main-content"` |
| `assets/css/components/footer.css` | Ajuster les styles si `h3` avait des styles spécifiques |

### Fichiers créés

| Fichier | Contenu |
|---------|---------|
| `assets/js/modules/brevo-a11y.js` | Ajout `aria-label` sur les champs Brevo |

## Accessibilité

Toutes les modifications de ce PRD améliorent directement l'accessibilité :

- Hiérarchie des headings correcte → navigation screen reader cohérente
- Labels de formulaire → champs annoncés correctement par les technologies d'assistance
- Un seul `<main>` → landmark unique et non ambigu
- Skip link fonctionnel → pointe vers le bon `id`

## Vérification

1. **PHPCS** : `make phpcs` — aucune violation
2. **HTML valide** : W3C Validator — un seul `<main>` par page, headings en ordre
3. **Skip link** : Cliquer Tab sur n'importe quelle page → le skip link doit mener au contenu principal
4. **Headings** : Chrome DevTools > Accessibility tree — vérifier la hiérarchie h1 > h2 > h3
5. **Labels Brevo** : axe DevTools → 0 violation "form elements must have labels"
6. **Console** : DevTools > Console → aucune erreur (police 404 + reCAPTCHA résolus)
7. **Lighthouse Accessibility** : Score ≥ 98
8. **Navigation clavier** : Tab à travers toutes les pages — aucun piège de focus
9. **$suffix** : En mode production (`WP_DEBUG=false`), vérifier que les fichiers `.min.css` sont bien chargés pour testimonials et signets
