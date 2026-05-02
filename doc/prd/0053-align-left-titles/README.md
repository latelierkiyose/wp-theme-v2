# PRD 0053 — Alignement à gauche des titres dans les contenus justifiés

- **Statut** : prêt à l'implémentation
- **Version** : 1.0
- **Date** : 2026-05-02

## Contexte

Les contenus éditoriaux longs du thème utilisent `text-align: justify` pour les paragraphes et les blocs de texte. Cette intention est conservée : elle donne un rendu plus posé et cohérent avec l'identité éditoriale du site.

Le problème est que les titres placés dans ces conteneurs héritent aussi de cet alignement justifié. Sur des titres courts ou moyennement longs, le navigateur peut étirer les espaces entre les mots, ce qui crée un rendu visuellement instable et moins qualitatif qu'un alignement naturel à gauche.

Exemple observé en local :

- `http://localhost:8000/sens-de-la-vie/`
- premier `h4` dans `.page__content`

Conteneurs concernés par le même risque :

| Conteneur | Fichier CSS | État actuel |
|---|---|---|
| `.page__content` | `assets/css/components/page.css` | contenu justifié, titres hérités |
| `.blog-single__content` | `assets/css/components/blog-single.css` | contenu justifié, titres hérités |
| `.about-page__content` | `assets/css/components/about-page.css` | contenu justifié, titres hérités |
| `.service-page__content` | `assets/css/components/service-page.css` | contenu justifié, titres hérités |

Point d'attention : le problème ne vient pas des `h1` de header de page. Ces titres principaux sont volontairement centrés dans leurs headers et décorés par le soulignement doré global du PRD 0038.

## Problème à résoudre

Les titres éditoriaux internes doivent rester faciles à scanner. Quand ils sont justifiés, ils perdent leur rôle de repère visuel : les espaces deviennent irréguliers, la lecture est moins fluide et l'interface donne une impression de finition incomplète.

Le correctif ne doit pas remettre en cause l'alignement justifié du contenu courant. Changer tout le contenu en `text-align: left` serait une réponse trop large par rapport au besoin exprimé.

## Objectif

Aligner à gauche les titres internes des contenus justifiés, tout en conservant l'alignement justifié du corps de texte.

Comportement attendu :

1. les paragraphes, listes et citations conservent l'alignement justifié existant ;
2. les titres éditoriaux `h2` à `h6` dans les conteneurs de contenu justifié sont alignés à gauche ;
3. les headers principaux de page (`h1`) restent inchangés ;
4. les titres de composants non éditoriaux, cartes, sections home et éléments Events Manager ne sont pas modifiés ;
5. le rendu reste cohérent de `320px` à desktop.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Scope | Cibler uniquement les titres `h2` à `h6` dans les conteneurs éditoriaux justifiés | Corrige le problème observé sans impacter les headers, cartes ou sections centrées |
| Conteneurs ciblés | `.page__content`, `.blog-single__content`, `.about-page__content`, `.service-page__content` | Ce sont les conteneurs du thème qui justifient explicitement le contenu long |
| Stratégie CSS | Ajouter `text-align: left` aux règles de titres déjà existantes dans chaque composant | S'intègre au code actuel et évite un sélecteur global trop large |
| `h1` | Ne pas modifier les `h1` | Les titres principaux sont gérés par les headers de templates et le PRD 0038 |
| Body text | Conserver `text-align: justify` sur les conteneurs | Le besoin demandé porte sur les titres, pas sur une refonte typographique du texte courant |
| Mobile | Ne pas créer de règle mobile spécifique dans ce PRD | Le comportement cible est identique : texte justifié, titres à gauche |
| Events Manager | Ne pas modifier `.calendar-page__content` | Ce conteneur n'est pas justifié et contient des structures plugin spécifiques |

## Spécifications

### 1. Aligner les titres du template page par défaut

Modifier `latelierkiyose/assets/css/components/page.css`.

Ajouter `text-align: left` aux règles existantes :

```css
.page__content h2 {
	margin-bottom: var(--kiyose-spacing-md);
	margin-top: var(--kiyose-spacing-2xl);
	text-align: left;
}

.page__content h3 {
	margin-bottom: var(--kiyose-spacing-md);
	margin-top: var(--kiyose-spacing-xl);
	text-align: left;
}

.page__content h4,
.page__content h5,
.page__content h6 {
	margin-bottom: var(--kiyose-spacing-sm);
	margin-top: var(--kiyose-spacing-lg);
	text-align: left;
}
```

Règles attendues :

- ne pas retirer `text-align: justify` de `.page__content` ;
- ne pas modifier `.page__header` ni `.page__title` ;
- ne pas modifier les alignements Gutenberg `.alignleft`, `.alignright`, `.aligncenter`, `.alignwide`, `.alignfull`.

### 2. Aligner les titres des articles uniques

Modifier `latelierkiyose/assets/css/components/blog-single.css`.

Ajouter `text-align: left` aux règles existantes :

- `.blog-single__content h2`
- `.blog-single__content h3`
- `.blog-single__content h4, .blog-single__content h5, .blog-single__content h6`

Règles attendues :

- ne pas modifier `.blog-single__header`, `.blog-single__title`, `.blog-single__date` ou `.blog-single__featured-image` ;
- conserver le comportement du PRD 0051 sur l'image à la une ;
- conserver `text-align: justify` sur `.blog-single__content`.

### 3. Aligner les titres des templates À propos et Services

Modifier :

- `latelierkiyose/assets/css/components/about-page.css`
- `latelierkiyose/assets/css/components/service-page.css`

Ajouter `text-align: left` aux règles existantes des titres internes :

- `.about-page__content h2`
- `.about-page__content h3`
- `.about-page__content h4`
- `.about-page__content h5, .about-page__content h6`
- `.service-page__content h2`
- `.service-page__content h3`
- `.service-page__content h4`
- `.service-page__content h5, .service-page__content h6`

Règles attendues :

- conserver les couleurs actuelles, notamment les `h4` en `--kiyose-color-text` sur À propos et Services ;
- ajouter des règles minimales pour `h5` et `h6` si elles n'existent pas encore, sans changer leur typographie globale ;
- ne pas modifier les headers `.about-page__header` et `.service-page__header` ;
- ne pas modifier les images, citations, listes ou CTA.

### 4. Régénérer les assets minifiés

Régénérer les fichiers générés par la chaîne de build existante :

- `latelierkiyose/assets/css/components/page.min.css`
- `latelierkiyose/assets/css/components/page.min.css.map`
- `latelierkiyose/assets/css/components/blog-single.min.css`
- `latelierkiyose/assets/css/components/blog-single.min.css.map`
- `latelierkiyose/assets/css/components/about-page.min.css`
- `latelierkiyose/assets/css/components/about-page.min.css.map`
- `latelierkiyose/assets/css/components/service-page.min.css`
- `latelierkiyose/assets/css/components/service-page.min.css.map`

Utiliser `npm run build:css` ou `make build-css`. Ne pas éditer les fichiers minifiés manuellement.

### 5. Ajouter un test de contrat CSS

Créer `latelierkiyose/tests/Test_Content_Headings_CSS.php`.

Cas minimum :

- `test_content_headings_whenInsideJustifiedContainers_areLeftAligned`

Le test doit vérifier :

- que chaque conteneur ciblé conserve `text-align: justify` ;
- que les règles de titres internes ciblées contiennent `text-align: left` ;
- que le test échoue si la règle est ajoutée seulement à `.page__content h4`.

Conteneurs à couvrir :

```php
array(
	'../assets/css/components/page.css'        => array( '.page__content' ),
	'../assets/css/components/blog-single.css' => array( '.blog-single__content' ),
	'../assets/css/components/about-page.css'  => array( '.about-page__content' ),
	'../assets/css/components/service-page.css' => array( '.service-page__content' ),
)
```

L'implémentation peut factoriser l'assertion dans une méthode privée du test, tant que le nommage des tests reste conforme au standard du projet.

## Fichiers à modifier

| Fichier | Action |
|---|---|
| `latelierkiyose/assets/css/components/page.css` | Aligner à gauche les `h2` à `h6` dans `.page__content` |
| `latelierkiyose/assets/css/components/page.min.css` | Régénérer |
| `latelierkiyose/assets/css/components/page.min.css.map` | Régénérer |
| `latelierkiyose/assets/css/components/blog-single.css` | Aligner à gauche les `h2` à `h6` dans `.blog-single__content` |
| `latelierkiyose/assets/css/components/blog-single.min.css` | Régénérer |
| `latelierkiyose/assets/css/components/blog-single.min.css.map` | Régénérer |
| `latelierkiyose/assets/css/components/about-page.css` | Aligner à gauche les `h2` à `h6` dans `.about-page__content` |
| `latelierkiyose/assets/css/components/about-page.min.css` | Régénérer |
| `latelierkiyose/assets/css/components/about-page.min.css.map` | Régénérer |
| `latelierkiyose/assets/css/components/service-page.css` | Aligner à gauche les `h2` à `h6` dans `.service-page__content` |
| `latelierkiyose/assets/css/components/service-page.min.css` | Régénérer |
| `latelierkiyose/assets/css/components/service-page.min.css.map` | Régénérer |
| `latelierkiyose/tests/Test_Content_Headings_CSS.php` | Nouveau test de contrat CSS |

## Accessibilité et lisibilité

- Le changement améliore la capacité de scan des contenus longs : les titres redeviennent des repères visuels stables.
- Aucun élément interactif n'est ajouté ou supprimé.
- Le focus clavier n'est pas impacté.
- Les contrastes existants restent inchangés.
- L'ordre sémantique des headings n'est pas modifié.
- À `320px`, vérifier que la justification du texte courant ne crée pas d'espaces excessifs. Si ce problème apparaît, il doit faire l'objet d'un PRD séparé sur la justification mobile plutôt que d'être glissé dans ce correctif.

## Documentation

Aucune documentation utilisateur n'est requise : le changement ne crée ni shortcode, ni classe CSS rédacteur, ni template de page, ni option admin.

Aucune documentation release n'est requise : le changement n'ajoute pas de plugin, de migration, de nouvelle option, de hook d'activation ou de prérequis de production.

## Hors périmètre

- Supprimer l'alignement justifié des paragraphes.
- Modifier les `h1` de header de page ou le soulignement doré global.
- Modifier la page d'accueil, les cartes blog, la page contact, la page calendrier ou les composants Events Manager.
- Revoir les tailles, couleurs ou polices des titres.
- Ajouter une option éditeur pour choisir l'alignement.
- Refondre la typographie mobile du contenu courant.

## Risques et points d'attention

| Risque | Mitigation |
|---|---|
| Correction trop locale sur `.page__content h4` uniquement | Couvrir `h2` à `h6` et ajouter un test qui échoue si seul le cas observé est traité |
| Régression sur les headers centrés | Ne cibler que les titres dans les conteneurs `__content`, jamais les `__header` ou `__title` |
| Perte de styles spécifiques des templates À propos/Services | Ajouter seulement `text-align: left`, sans toucher couleurs, tailles ou espacements existants |
| Minifiés incohérents avec les sources | Régénérer les CSS minifiés via la commande de build |
| Justification inconfortable sur mobile | Valider visuellement à `320px`; créer un PRD dédié si le body text doit changer sur petits écrans |

## Tests et validation

### Validation automatisée

- [ ] `./bin/phpunit.sh --testdox` passe, incluant `test_content_headings_whenInsideJustifiedContainers_areLeftAligned`.
- [ ] `npm run build:css` a régénéré les minifiés et source maps.
- [ ] `npm run lint:css` passe.
- [ ] `make test` passe.

### Validation visuelle

Tester l'URL d'exemple :

- [ ] `http://localhost:8000/sens-de-la-vie/` : le premier `h4` de `.page__content` est aligné à gauche.
- [ ] Les paragraphes de `.page__content` restent justifiés.
- [ ] Les `h2`, `h3`, `h4`, `h5` et `h6` insérés dans une page standard sont alignés à gauche.
- [ ] Un article de blog avec titres internes garde le contenu justifié et les titres internes à gauche.
- [ ] Les templates À propos et Services gardent leurs headers centrés, mais leurs titres internes sont à gauche.
- [ ] Desktop `>= 1024px` : les titres ne présentent plus d'espaces étirés.
- [ ] Tablet `768px - 1023px` : les titres restent lisibles et alignés à gauche.
- [ ] Mobile `320px - 767px` : pas de chevauchement, pas de débordement horizontal, scan des titres correct.
