# PRD 0057 — Style d'image ronde avec habillage rectangulaire

- **Statut** : terminé
- **Criticité** : moyenne
- **Version** : 1.0
- **Date** : 2026-05-02

## Contexte

Le PRD 0040 a défini le comportement global des images éditoriales insérées avec le bloc Image Gutenberg :

- images centrées limitées à `720px` ;
- images alignées à gauche ou à droite flottantes seulement sur desktop (`>= 1024px`) ;
- repli centré pleine colonne sur tablet et mobile ;
- captions harmonisées ;
- styles portés par `gutenberg-blocks.css`.

Ce comportement couvre le positionnement de base, mais il ne permet pas de créer une image ronde cohérente, proche d'un portrait ou d'un médaillon. Le besoin éditorial est de pouvoir sélectionner un style clair dans Gutenberg tout en gardant le comportement de flottement rectangulaire du PRD 0040, plus robuste entre navigateurs.

## Problème à résoudre

Aujourd'hui, le style natif WordPress `Arrondie` ne garantit pas un médaillon circulaire cohérent avec le thème. Le rendu visuel peut devenir incohérent :

- l'image semble ronde ;
- l'image peut devenir ovale si la source n'est pas carrée ;
- l'espace réservé dans le texte doit rester rectangulaire pour garantir un rendu stable ;
- les rédacteurices n'ont pas de style clair à sélectionner dans l'éditeur.

Le besoin n'est pas seulement esthétique. Il faut définir un contrat stable entre l'éditeur Gutenberg, le CSS front et la documentation utilisateur.

## Objectif

Ajouter un style d'image éditoriale `Ronde` qui :

1. rend l'image visuellement circulaire ;
2. force un cadrage carré avec `object-fit: cover` pour éviter les ovales ;
3. conserve, sur desktop, l'habillage rectangulaire standard des images flottantes ;
4. conserve le comportement responsive du PRD 0040 sur tablet et mobile ;
5. reste utilisable par les rédacteurices depuis l'interface native Gutenberg ;
6. reste stable sur Chrome, Firefox et Safari sans dépendre de CSS Shapes.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| UX rédacteurice | Exposer un style Gutenberg `Ronde` sur le bloc Image | Plus clair qu'une classe CSS à saisir manuellement |
| Nom technique | Utiliser le style `kiyose-round`, rendu en classe `is-style-kiyose-round` | Préfixe cohérent avec le thème et pas de conflit avec le style core `is-style-rounded` |
| Bloc ciblé | `core/image` uniquement | Le besoin concerne les images éditoriales simples, pas les galeries ni les médias custom |
| Alignements compatibles | `alignleft`, `alignright`, `aligncenter` | Réutilise le contrat du PRD 0040 |
| Habillage du texte | Garder l'habillage rectangulaire standard du PRD 0040 | Plus prévisible et compatible entre navigateurs que CSS Shapes |
| Mobile et tablet | Aucun flottement sous `1024px` | Le texte autour d'une image devient trop étroit et moins lisible |
| Dimensions | Limiter les images rondes à `320px` maximum | Cohérent avec la largeur flottante du PRD 0040 et évite les médaillons disproportionnés |
| Source image | Recadrer côté CSS avec `aspect-ratio: 1` et `object-fit: cover` | Garantit un cercle même avec une image source rectangulaire |
| Captions | Les conserver avec le comportement du PRD 0040 | Une caption fait partie du `figure` flottant et reste compatible avec l'habillage rectangulaire |
| Style core WordPress | Ne pas surcharger `is-style-rounded` | Évite de modifier par surprise des contenus qui utilisent déjà le style WordPress natif |
| JavaScript | Aucun JS | Le besoin est purement CSS + enregistrement de style Gutenberg |

## Dépendances

- **PRD 0040** : règles globales des images éditoriales et flottement desktop.
- **WordPress 6.7+** : API `register_block_style()` pour exposer le style dans Gutenberg.
- **CSS moderne** : `aspect-ratio`, `object-fit`.

## Spécifications

### 1. Enregistrer le style Gutenberg `Ronde`

Modifier `latelierkiyose/inc/setup.php` ou un module dédié déjà chargé par `functions.php`.

Créer une fonction préfixée, par exemple :

```php
function kiyose_register_block_styles() {
	if ( ! function_exists( 'register_block_style' ) ) {
		return;
	}

	register_block_style(
		'core/image',
		array(
			'name'  => 'kiyose-round',
			'label' => __( 'Ronde', 'kiyose' ),
		)
	);
}
add_action( 'init', 'kiyose_register_block_styles' );
```

Règles attendues :

- utiliser le text domain `kiyose` ;
- ne pas enregistrer de style inline via PHP ;
- garder le CSS dans `gutenberg-blocks.css` ;
- ne pas supprimer ni modifier le style WordPress natif `is-style-rounded` ;
- ne pas créer de bloc custom.

### 2. Ajouter les styles CSS front

Modifier `latelierkiyose/assets/css/components/gutenberg-blocks.css`, dans la section `IMAGES (wp-block-image)` ajoutée par le PRD 0040.

Comportement attendu :

```css
.wp-block-image.is-style-kiyose-round,
figure.is-style-kiyose-round {
	max-width: min(100%, 320px);
}

:where(.wp-block-image, figure).is-style-kiyose-round img {
	aspect-ratio: 1;
	border-radius: 50%;
	object-fit: cover;
	width: 100%;
}
```

Les valeurs exactes peuvent être ajustées pendant l'implémentation, mais le contrat produit ne doit pas changer :

- image ronde, jamais ovale ;
- largeur maximale `320px` ;
- texte habillé autour de la boîte rectangulaire sur desktop avec alignement gauche ou droit ;
- image centrée sans texte flottant avec alignement centre ;
- repli centré sans flottement sous `1024px`.

Ne pas utiliser `shape-outside` pour ce style : le rendu est trop fragile selon les navigateurs. Le comportement attendu est une image ronde visible avec texte autour de la boîte flottante rectangulaire.

### 3. Préserver le comportement des captions

Les captions existantes du bloc Image restent supportées.

Règles attendues :

- la caption reste lisible sous l'image ;
- la caption conserve le style défini par le PRD 0040 ;
- aucune caption ne doit chevaucher le texte adjacent.

### 4. Régénérer les assets minifiés

Régénérer les fichiers générés par la chaîne de build existante :

- `latelierkiyose/assets/css/components/gutenberg-blocks.min.css`
- `latelierkiyose/assets/css/components/gutenberg-blocks.min.css.map`

Ne pas éditer les fichiers minifiés manuellement.

### 5. Ajouter des tests automatisés

Ajouter ou compléter des tests PHPUnit.

Tests attendus :

- `kiyose_register_block_styles()` enregistre un style `kiyose-round` pour `core/image` quand `register_block_style()` existe ;
- la fonction ne produit pas d'erreur si `register_block_style()` n'existe pas ;
- `gutenberg-blocks.css` contient le sélecteur `.is-style-kiyose-round` ;
- le CSS du style rond contient `border-radius: 50%`, `aspect-ratio: 1`, `object-fit: cover` ;
- le CSS ne contient pas `shape-outside` ni `shape-margin` pour le style rond.

Fichiers de test possibles :

- créer `latelierkiyose/tests/Test_Setup.php` pour l'enregistrement du style ;
- créer `latelierkiyose/tests/Test_Gutenberg_Blocks_CSS.php` pour le contrat CSS.

Respecter les conventions de tests du projet :

- noms de tests au format `<method>_(when<condition>|of<input>)_<expected>` ;
- commentaires `// Given`, `// When`, `// Then` ;
- pas de `@Mock` ni `@InjectMocks`.

### 6. Mettre à jour la documentation utilisateur

Mettre à jour `doc/user/03-mise-en-page-avec-des-classes.md` ou créer une page dédiée si la section devient trop longue.

La documentation doit expliquer :

- comment sélectionner un bloc Image ;
- où choisir le style `Ronde` dans Gutenberg ;
- que l'image doit idéalement être un portrait ou une image déjà bien centrée ;
- que l'alignement gauche/droite active l'habillage rectangulaire du texte seulement sur ordinateur ;
- que l'alignement centre affiche une image ronde centrée sans habillage ;
- qu'il faut renseigner un texte alternatif pertinent si l'image porte une information.

Exemple à inclure :

| Réglage | Valeur |
|---|---|
| Bloc | Image |
| Style | `Ronde` |
| Alignement | `Gauche` ou `Droite` pour faire passer le texte autour |
| Texte alternatif | `Portrait de Sandrine Delmas` si l'image présente une personne |

## Fichiers à modifier

| Fichier | Action |
|---|---|
| `latelierkiyose/inc/setup.php` | Enregistrer le style Gutenberg `kiyose-round` pour `core/image` |
| `latelierkiyose/assets/css/components/gutenberg-blocks.css` | Ajouter les règles du style rond |
| `latelierkiyose/assets/css/components/gutenberg-blocks.min.css` | Régénérer |
| `latelierkiyose/assets/css/components/gutenberg-blocks.min.css.map` | Régénérer |
| `latelierkiyose/tests/Test_Setup.php` | Ajouter les tests d'enregistrement du style |
| `latelierkiyose/tests/Test_Gutenberg_Blocks_CSS.php` | Ajouter les tests de contrat CSS |
| `doc/user/03-mise-en-page-avec-des-classes.md` | Documenter l'usage rédacteurice |

## Accessibilité

| Critère | Exigence |
|---|---|
| Texte alternatif | Continuer à utiliser le champ natif WordPress du bloc Image |
| Contraste caption | Conserver les règles du PRD 0040 |
| Navigation clavier | Non impactée : le style ne crée aucun contrôle interactif |
| Zoom texte | Aucun chevauchement à 200% de zoom |
| Responsive 320px | L'image ronde doit rester dans la colonne, sans scroll horizontal |
| Compréhension du contenu | Ne pas utiliser l'image ronde pour porter une information absente du texte ou du `alt` |

## Hors périmètre

- Images des templates dédiés : hero, contact, home, services, témoignages.
- Galeries Gutenberg (`core/gallery`).
- Bloc média + texte (`core/media-text`).
- Réglage fin du point focal dans l'éditeur.
- Contrôles personnalisés de taille, marge ou forme.
- Habillage circulaire autour du contour visible de l'image.
- Habillage du texte sur mobile et tablet.
- Lightbox ou zoom au clic.

## Risques et points d'attention

| Risque | Mitigation |
|---|---|
| Les rédacteurices confondent `Ronde` avec le style natif `Arrondie` | Documentation explicite et label clair dans Gutenberg |
| Une image rectangulaire coupe un visage | Recommander des portraits centrés et tester le rendu avant publication |
| Les rendus CSS Shapes sont différents selon les navigateurs | Ne pas utiliser `shape-outside` ; garder l'habillage rectangulaire |
| Une caption longue augmente la hauteur du bloc flottant | Conserver l'habillage rectangulaire standard du PRD 0040 |
| Les styles `alignleft` / `alignright` existants entrent en conflit | Ajouter les règles dans la section PRD 0040 et couvrir par tests CSS |

## Tests et validation

### Validation automatisée

- [x] Test : `kiyose_register_block_styles()` enregistre `kiyose-round` sur `core/image`.
- [x] Test : absence de fatal error si `register_block_style()` n'existe pas.
- [x] Test CSS : `.is-style-kiyose-round` existe dans `gutenberg-blocks.css`.
- [x] Test CSS : l'image ronde utilise `aspect-ratio: 1`, `border-radius: 50%`, `object-fit: cover`.
- [x] Test CSS : le style rond n'utilise pas `shape-outside` ni `shape-margin`.
- [x] `./bin/phpunit.sh --testdox` passe.
- [x] `./bin/phpcs.sh` passe.
- [x] `make test` passe.

### Validation manuelle

Créer un article ou une page de test avec :

- une image `Ronde` alignée à gauche dans un paragraphe long ;
- une image `Ronde` alignée à droite dans un paragraphe long ;
- une image `Ronde` centrée ;
- une image `Ronde` avec caption courte.

Vérifier :

- [ ] Desktop `>= 1024px` : le texte suit une limite verticale rectangulaire stable.
- [ ] Desktop `>= 1024px` : `alignleft` et `alignright` restent limités à `320px`.
- [ ] Desktop `>= 1024px` : `aligncenter` reste centré, sans texte flottant.
- [ ] Tablet `768-1023px` : aucune image ne flotte.
- [ ] Mobile `320-767px` : l'image reste dans la colonne, sans scroll horizontal.
- [ ] La caption reste lisible et ne chevauche pas le texte adjacent.
- [ ] Les images sans style `Ronde` conservent le comportement du PRD 0040.
- [ ] Le style est disponible dans l'éditeur Gutenberg sur le bloc Image.
