# PRD 0030 — Correction de la structure `<main>` imbriquée

## Statut

**prêt à l'implémentation**

## Objectif

Supprimer le `<main>` redondant injecté par `header.php`/`footer.php`, qui crée une imbrication HTML invalide avec le `<main>` propre à chaque template. Profiter du fix pour ajouter sur `.site-main` le contexte de positionnement nécessaire aux shapes décoratives (PRD 0032).

## Contexte

`header.php` ouvre `<main id="main-content" role="main">` et `footer.php` le ferme. Chaque template de page ouvre en plus son propre `<main id="main" class="site-main">`, créant une imbrication :

```html
<!-- header.php -->
<main id="main-content" role="main">

  <!-- page-services.php, page-about.php, etc. -->
  <main id="main" class="site-main">
    <!-- contenu de la page -->
  </main>

</main><!-- footer.php -->
```

**Problèmes** :
- Imbrication d'éléments `<main>` : HTML invalide (WHATWG spec, un seul `<main>` visible autorisé)
- IDs dupliqués au sens sémantique : `#main-content` et `#main` coexistent
- Le CSS cible `#main-content` pour le layout alors que le contenu est dans `.site-main`

**Note** : la homepage (`page-home.php`) a la même structure imbriquée mais ajoute la classe `.home-page` qui lui sert de contexte de positionnement pour les shapes. Ce contexte sera migré vers `.site-main` dans ce PRD.

## Dépendances

- [x] [PRD 0002 — Design tokens](../0002-design-tokens/) (variables CSS)

## Spécifications techniques

### 1. Supprimer le `<main>` de `header.php` et `footer.php`

**`header.php`** — supprimer la ligne :
```php
<main id="main-content" role="main">
```

**`footer.php`** — supprimer la ligne :
```php
	</main><!-- #main-content -->
```

Le `<main>` de chaque template devient le seul landmark `main` de la page.

### 2. Mettre à jour le skip link

Le skip link dans `inc/accessibility.php` cible actuellement `#main-content`. Il doit pointer vers `#main` (l'ID déjà présent sur tous les templates).

La cible d'un skip link doit être programmatiquement focusable. Chaque template doit ajouter `tabindex="-1"` sur son `<main id="main">`.

**`inc/accessibility.php`** :
```php
function kiyose_get_skip_link() {
	return sprintf(
		'<a href="#main" class="skip-link">%s</a>',
		esc_html__( 'Aller au contenu principal', 'kiyose' )
	);
}
```

**Tous les templates** — ajouter `tabindex="-1"` :

| Template | Avant | Après |
|----------|-------|-------|
| `templates/page-home.php` | `<main id="main" class="site-main home-page">` | `<main id="main" class="site-main home-page" tabindex="-1">` |
| `templates/page-services.php` | `<main id="main" class="site-main">` | `<main id="main" class="site-main" tabindex="-1">` |
| `templates/page-about.php` | idem | idem |
| `templates/page-contact.php` | idem | idem |
| `templates/page-calendar.php` | idem | idem |
| `page.php` | idem | idem |
| `single.php` | idem | idem |
| `archive.php` | idem | idem |
| `search.php` | idem | idem |
| `404.php` | idem | idem |

### 3. Migrer les styles de `#main-content` vers `.site-main`

**`assets/css/main.css`** — remplacer :
```css
/* stylelint-disable-next-line selector-max-id */
#main-content {
	min-height: 60vh;
	padding: var(--kiyose-spacing-2xl) 0;
}
```

Par :
```css
.site-main {
	min-height: 60vh;
	overflow-x: clip;
	padding: var(--kiyose-spacing-2xl) 0;
	position: relative;
}
```

Le `position: relative` et `overflow-x: clip` sont ajoutés ici (anciennement sur `.home-page` uniquement) pour servir de contexte de positionnement aux shapes décoratives sur toutes les pages (PRD 0032).

**`assets/css/components/home-sections.css`** — remplacer :
```css
/* stylelint-disable-next-line selector-max-id */
.page-template-page-home #main-content {
	padding-bottom: 0;
}
```

Par :
```css
.page-template-page-home .site-main {
	padding-bottom: 0;
}
```

**`assets/css/components/kintsugi.css`** — supprimer les propriétés migrées de `.home-page` (qui sont maintenant dans `.site-main`) :
```css
/* Avant */
.home-page {
	background-color: #ffffff;
	overflow-x: clip;
	position: relative;
}

/* Après — ne conserver que ce qui est homepage-specific */
.home-page {
	background-color: #ffffff;
}
```

### 4. Garantir le z-index du contenu au-dessus des shapes

Sur la homepage, `home-sections.css` définit déjà `.home-page section > :not(.deco-element, .sr-only) { position: relative; z-index: 1 }`. Pour que les shapes des autres pages (PRD 0032) restent derrière le contenu, ajouter dans `kintsugi.css` :

```css
/**
 * Guarantees page content stays above decorative shapes on all pages.
 */
.site-main > :not(.deco-page-shapes) {
	position: relative;
	z-index: 1;
}
```

## Accessibilité

| Critère | Spécification |
|---------|--------------|
| Structure HTML valide | Un seul `<main>` visible par page après le fix |
| Skip link | Cible `#main`, focusable via `tabindex="-1"` |
| Landmark `main` | `role="main"` déjà implicite sur `<main>` — pas besoin d'attribut explicite |
| Contraste | Aucun impact (changement structurel uniquement) |

## Hors périmètre

- Ajout de shapes décoratives sur les autres pages (PRD 0032, dépend de ce PRD)
- Modification du contenu ou du layout des templates
- Ajout d'autres landmarks ARIA

## Fichiers à modifier

```
latelierkiyose/
├── header.php                              # Supprimer <main id="main-content">
├── footer.php                              # Supprimer </main><!-- #main-content -->
├── inc/
│   └── accessibility.php                   # Mettre à jour href skip link
├── templates/
│   ├── page-home.php                       # tabindex="-1" sur <main>
│   ├── page-services.php                   # tabindex="-1" sur <main>
│   ├── page-about.php                      # tabindex="-1" sur <main>
│   ├── page-contact.php                    # tabindex="-1" sur <main>
│   └── page-calendar.php                   # tabindex="-1" sur <main>
├── page.php                                # tabindex="-1" sur <main>
├── single.php                              # tabindex="-1" sur <main>
├── archive.php                             # tabindex="-1" sur <main>
├── search.php                              # tabindex="-1" sur <main>
├── 404.php                                 # tabindex="-1" sur <main>
└── assets/css/
    ├── main.css                            # #main-content → .site-main + position/overflow
    ├── components/home-sections.css        # #main-content → .site-main
    └── components/kintsugi.css             # .home-page : supprimer position/overflow (migrés) + ajout règle z-index .site-main
```

## Tests et validation

### Fonctionnel

- [ ] HTML validé avec le W3C Validator — un seul `<main>` par page
- [ ] Skip link « Aller au contenu principal » fonctionne au clavier (Tab → Entrée) sur toutes les pages
- [ ] `make start` → naviguer sur toutes les pages, aucune régression visuelle
- [ ] La homepage conserve son aspect identique (fond blanc, overflow-x: clip)

### Accessibilité

- [ ] Skip link audité : focus visible sur la cible `#main` après activation
- [ ] VoiceOver / NVDA : landmark `main` annoncé une seule fois par page
- [ ] Aucune violation d'accessibilité dans Axe DevTools
- [ ] Navigation clavier inchangée sur tous les templates

### Responsive

- [ ] Rendu correct à 320px, 768px, 1280px — aucune régression

### Standards

- [ ] `make test` passe (PHPCS, ESLint, Stylelint)
- [ ] Aucune violation Stylelint (plus de `selector-max-id` nécessaire après migration CSS)
