# PRD 0055 — Homogénéisation des icônes sociales compactes

- **Statut** : terminé
- **Version** : 1.0
- **Date** : 2026-05-02

## Contexte

Le thème affiche des liens sociaux dans trois contextes visuels :

| Contexte | Source | Classes principales | État actuel |
|---|---|---|---|
| Footer | `kiyose_get_social_links_html( 'footer' )` dans `footer.php` | `.site-footer__social a` | Icônes rondes compactes, bordeaux sur fond clair, mouvement vertical au survol |
| Page Contact | `kiyose_get_social_links_html( 'contact' )` dans `templates/page-contact.php` | `.social-links a` | Grandes lignes avec icône + libellé, comportement horizontal au survol |
| Partage d'un contenu unique | `kiyose_get_post_share_links_html()` dans `single.php` | `.blog-single__share-link` | Icônes rondes compactes, couleurs réseau au survol, zoom au survol |

Le besoin exprimé porte sur l'homogénéisation des **petites icônes rondes** : footer et boutons de partage. Les grandes entrées de la page Contact ont une structure différente, avec texte visible et usage de menu de contact ; elles ne doivent pas être modifiées dans ce PRD.

Point technique important : les boutons de partage sont rendus par `single.php` et partagent les classes `.blog-single__share-link` pour les articles et les événements. Même si l'exemple observé est une page événement (`/events/atelier-rigologie-sud-vienne-17/`), le changement s'applique à tous les boutons de partage de contenu unique afin de garder un composant cohérent.

## Problème à résoudre

Les petites icônes sociales ne racontent pas le même système d'interaction selon l'endroit où elles apparaissent :

- le footer bouge vers le haut au survol ;
- les boutons de partage grossissent au survol ;
- les boutons de partage remplacent l'identité visuelle du site par les couleurs de chaque réseau ;
- la page Contact bouge horizontalement, mais ce comportement appartient à un composant plus grand et ne doit pas être pris comme référence pour les petites icônes.

Cette diversité donne une impression de finition inégale et rend le comportement moins prévisible, alors que les deux surfaces compactes remplissent une fonction proche : accéder à un réseau social ou partager un contenu.

## Objectif

Uniformiser le style et le comportement des petites icônes sociales rondes :

1. les icônes compactes du footer et du partage utilisent les mêmes couleurs de repos ;
2. les icônes compactes du footer et du partage utilisent les mêmes couleurs au survol et au focus clavier ;
3. aucun déplacement, zoom ou changement d'échelle n'est déclenché au survol ou au focus ;
4. les couleurs propres aux réseaux sociaux sont supprimées des états de survol des boutons de partage ;
5. les grands liens sociaux de la page Contact restent inchangés.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Scope | Cibler uniquement `.site-footer__social a` et `.blog-single__share-link` | Ce sont les deux familles d'icônes rondes compactes |
| Contact | Ne pas modifier `.social-links` | Le composant Contact est un menu avec libellés, pas une icône compacte |
| Couleurs au repos | Bordeaux sur rose poudré : `color: var(--kiyose-color-burgundy)` sur `background-color: var(--kiyose-color-primary)` | Répond à la cible "bordeaux sur rose" avec les tokens officiels du design system |
| Couleurs interactives | Blanc sur bordeaux : `color: #ffffff` sur `background-color: var(--kiyose-color-burgundy)` | Interaction claire, contrastée et cohérente avec la marque |
| Motion | Supprimer `translateY`, `scale` et toute transition de `transform` sur ces icônes | Le besoin demande explicitement aucune translation au survol ; supprimer aussi le zoom évite une deuxième variante de mouvement |
| Couleurs réseau | Retirer les états hover/focus spécifiques Facebook, LinkedIn et Instagram | Le composant doit privilégier l'identité Kiyose plutôt que les marques tierces |
| État copie Instagram | Conserver un état de succès distinct pour `.blog-single__share-link--instagram.copied` si le contraste reste valide | L'état "copié" est un feedback fonctionnel, pas un état de survol réseau |
| Markup | Ne pas modifier le HTML/PHP sauf nécessité découverte pendant l'implémentation | Les helpers sociaux ont déjà été factorisés par le PRD 0046 |

## Spécifications

### 1. Homogénéiser les icônes du footer

Modifier `latelierkiyose/assets/css/components/footer.css`.

Comportement attendu pour `.site-footer__social a` :

```css
.site-footer__social a {
	background-color: var(--kiyose-color-primary);
	color: var(--kiyose-color-burgundy);
}
```

Comportement attendu au survol et au focus visible :

```css
.site-footer__social a:hover,
.site-footer__social a:focus-visible {
	background-color: var(--kiyose-color-burgundy);
	color: #ffffff;
}
```

Règles attendues :

- supprimer le `transform: translateY(-2px)` existant ;
- retirer `transform` de la propriété `transition` ;
- conserver la taille tactile minimale actuelle (`44px`) ;
- conserver un focus visible avec `outline` et `outline-offset` ;
- ne pas modifier la structure HTML dans `footer.php`.

### 2. Homogénéiser les boutons de partage

Modifier `latelierkiyose/assets/css/components/blog-single.css`.

Comportement attendu pour `.blog-single__share-link` :

```css
.blog-single__share-link {
	background-color: var(--kiyose-color-primary);
	color: var(--kiyose-color-burgundy);
}
```

Comportement attendu au survol et au focus visible :

```css
.blog-single__share-link:hover,
.blog-single__share-link:focus-visible {
	background-color: var(--kiyose-color-burgundy);
	color: #ffffff;
}
```

Règles attendues :

- supprimer le `transform: scale(1.1)` existant ;
- retirer `transform` de la propriété `transition` ;
- supprimer les couleurs réseau sur :
  - `.blog-single__share-link--facebook:hover`
  - `.blog-single__share-link--facebook:focus`
  - `.blog-single__share-link--linkedin:hover`
  - `.blog-single__share-link--linkedin:focus`
  - `.blog-single__share-link--instagram:hover`
  - `.blog-single__share-link--instagram:focus`
- conserver le bouton Instagram de copie (`button.js-copy-link`) et ses attributs `data-url` / `aria-label` ;
- conserver un état `.blog-single__share-link--instagram.copied` lisible et distinct si l'implémentation actuelle est gardée.

### 3. Ne pas toucher les grands liens sociaux de contact

Ne pas modifier `latelierkiyose/assets/css/components/contact-page.css` pour ce PRD.

Comportement à préserver :

- `.social-links a` reste une ligne pleine largeur avec icône + libellé ;
- le fond blanc et les couleurs actuelles restent inchangés ;
- le déplacement horizontal au survol reste inchangé ;
- la page Contact continue d'afficher les libellés visibles des réseaux.

### 4. Régénérer les assets minifiés

Régénérer les fichiers générés par la chaîne de build existante :

- `latelierkiyose/assets/css/components/footer.min.css`
- `latelierkiyose/assets/css/components/footer.min.css.map`
- `latelierkiyose/assets/css/components/blog-single.min.css`
- `latelierkiyose/assets/css/components/blog-single.min.css.map`

Utiliser `npm run build:css` ou `make build-css`. Ne pas éditer les fichiers minifiés manuellement.

### 5. Ajouter un test de contrat CSS

Créer `latelierkiyose/tests/Test_Social_Icons_CSS.php`.

Cas minimum recommandé :

- `test_social_icons_whenCompactLinksAreStyled_useSharedBrandStates`
- `test_social_icons_whenCompactLinksAreInteractive_doNotMove`
- `test_social_icons_whenShareLinksAreStyled_doNotUseNetworkHoverColors`

Le test doit vérifier :

- `.site-footer__social a` et `.blog-single__share-link` utilisent les mêmes couleurs de repos ;
- leurs états `:hover` et `:focus-visible` utilisent blanc sur bordeaux ;
- les règles interactives ciblées ne contiennent pas `transform:` ;
- les transitions de ces deux composants ne contiennent pas `transform`;
- les couleurs réseau `#1877f2`, `#0a66c2` et le gradient Instagram ne pilotent plus le hover/focus des boutons de partage ;
- aucune assertion ne dépend du fichier minifié.

L'implémentation peut réutiliser l'approche de parsing CSS de `Test_Content_Headings_CSS`.

## Fichiers à modifier

| Fichier | Action |
|---|---|
| `latelierkiyose/assets/css/components/footer.css` | Harmoniser couleurs et supprimer le mouvement des petites icônes du footer |
| `latelierkiyose/assets/css/components/footer.min.css` | Régénérer |
| `latelierkiyose/assets/css/components/footer.min.css.map` | Régénérer |
| `latelierkiyose/assets/css/components/blog-single.css` | Harmoniser couleurs, supprimer zoom et couleurs réseau des boutons de partage |
| `latelierkiyose/assets/css/components/blog-single.min.css` | Régénérer |
| `latelierkiyose/assets/css/components/blog-single.min.css.map` | Régénérer |
| `latelierkiyose/tests/Test_Social_Icons_CSS.php` | Nouveau test de contrat CSS |

## Accessibilité

- Les zones cliquables existantes de `44px` minimum doivent être conservées.
- Le focus clavier doit rester visible sur footer et boutons de partage.
- Les états interactifs doivent respecter WCAG 2.2 AA :
  - bordeaux `#5D0505` sur rose poudré `#D7A4A4` est valide pour du texte et des icônes ;
  - blanc `#ffffff` sur bordeaux `#5D0505` est valide.
- Les SVG restent décoratifs avec `aria-hidden="true"` et `focusable="false"`.
- Les `aria-label` existants des liens externes et du bouton de copie ne doivent pas être dégradés.
- La suppression du mouvement réduit le risque d'inconfort vestibulaire ; aucun nouveau comportement animé n'est attendu.

## Documentation

Aucune documentation utilisateur n'est requise : le changement ne crée ni shortcode, ni classe CSS rédacteur, ni template de page, ni option admin.

Aucune documentation release n'est requise : le changement n'ajoute pas de plugin, de migration, de nouvelle option, de hook d'activation ou de prérequis de production.

## Hors périmètre

- Modifier les grands liens sociaux de la page Contact.
- Ajouter, supprimer ou renommer des réseaux sociaux.
- Modifier les URLs sociales configurées via `kiyose_get_social_profiles()`.
- Modifier les SVG dans `kiyose_social_icon_svg()`.
- Modifier le JavaScript de copie de lien.
- Refaire le footer ou le template `single.php`.
- Créer un style différent entre articles et événements pour les boutons de partage.
- Modifier les libellés visibles ou les `aria-label`.

## Risques et points d'attention

| Risque | Mitigation |
|---|---|
| Le changement des `.blog-single__share-link` touche aussi les articles de blog | L'assumer explicitement : le composant de partage est commun à `single.php` |
| Un ancien style réseau garde une priorité supérieure | Supprimer ou neutraliser les règles réseau hover/focus et couvrir avec un test CSS |
| Le focus clavier perd en visibilité sur fond bordeaux | Valider le focus visible manuellement et conserver `outline` / `outline-offset` |
| Les fichiers minifiés deviennent incohérents avec les sources | Régénérer via `npm run build:css` et exécuter `npm run build:css:check` ou `make test` |
| La page Contact est modifiée par un sélecteur trop large | Ne cibler que `.site-footer__social a` et `.blog-single__share-link`; ajouter une validation manuelle de `/contact/` |

## Tests et validation

### Validation automatisée

- [ ] Ajouter le test `Test_Social_Icons_CSS`.
- [ ] `./bin/phpunit.sh --testdox` passe.
- [ ] `npm run build:css` a régénéré les minifiés et source maps.
- [ ] `npm run lint:css` passe.
- [ ] `npm run build:css:check` passe.
- [ ] `make test` passe.

### Validation visuelle

- [ ] Footer : les icônes sont bordeaux sur rose au repos.
- [ ] Footer : au survol, les icônes deviennent blanches sur bordeaux sans déplacement vertical.
- [ ] Footer : au clavier, le focus est visible et l'état interactif reste blanc sur bordeaux.
- [ ] Page événement, exemple `http://localhost:8000/events/atelier-rigologie-sud-vienne-17/` : les boutons de partage sont bordeaux sur rose au repos.
- [ ] Page événement : au survol/focus, les boutons de partage deviennent blancs sur bordeaux, sans zoom ni déplacement.
- [ ] Article de blog : les boutons de partage suivent le même comportement que les événements.
- [ ] Page Contact `http://localhost:8000/contact/` : les grands liens sociaux sont inchangés.
- [ ] Mobile `320px+` : aucun chevauchement, pas de débordement horizontal, zones tactiles toujours utilisables.
- [ ] `prefers-reduced-motion: reduce` : aucun mouvement parasite sur les petites icônes sociales.
