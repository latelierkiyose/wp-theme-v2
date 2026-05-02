# PRD 0051 — Images à la une des articles et événements

- **Statut** : terminé
- **Version** : 1.0
- **Date** : 2026-05-02

## Contexte

Sur les pages de détail qui utilisent le template `single.php`, l'image à la une est rendue dans le header via `.blog-single__featured-image`.

Exemples observés en local :

- `http://localhost:8000/2025/02/10/vers-plus-de-joie/`
- `http://localhost:8000/events/atelier-rigologie-83/`

Le comportement actuel est correct sur viewport étroit : l'image occupe la largeur disponible et reste lisible. En revanche, sur desktop, elle occupe toute la largeur de la colonne éditoriale (`.blog-single`, max `800px`). Pour certains visuels, cela crée une image dominante, pousse le contenu textuel trop bas et donne un poids disproportionné au media par rapport au titre et au contenu.

Point d'attention : l'URL `/events/.../` semble utiliser le fallback WordPress `single.php` et non un template Events Manager dédié. Le correctif doit donc cibler le composant `.blog-single__featured-image`, pas les listes Events Manager de la page calendrier.

## Objectif

Réduire la largeur visuelle des images à la une sur desktop, tout en conservant le rendu actuel sur mobile et tablet.

Comportement attendu :

1. Sur mobile et tablet, l'image reste pleine largeur dans sa colonne.
2. Sur desktop, l'image est centrée et limitée à environ la moitié de la colonne.
3. Le ratio original de l'image est conservé.
4. Le style existant de l'image (`border-radius`, ombre, `overflow`) est conservé.
5. Les images insérées dans le contenu Gutenberg ne sont pas modifiées par ce PRD.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Scope CSS | Cibler uniquement `.blog-single__featured-image` | Le problème concerne l'image à la une du header, pas les images éditoriales du contenu |
| Breakpoint | Appliquer la réduction uniquement à partir de `1024px` | Cohérent avec les breakpoints desktop du thème et évite des images trop petites sur tablet |
| Largeur desktop | `width: 420px` avec `max-width: 50%` | Produit une image proche de la moitié du conteneur actuel de `800px`, sans dépasser si la colonne change |
| Alignement | Centrer l'image dans le header | Évite un flottement décoratif qui déséquilibrerait le bloc titre/date/image |
| Mobile/tablet | Ne pas changer le comportement existant | Le problème n'est pas présent sur viewport étroit |
| PHP | Ne pas modifier `single.php` | Le markup actuel est suffisant ; le problème est purement visuel |
| Taille WordPress | Conserver `the_post_thumbnail( 'large' )` | Le navigateur garde le `srcset` WordPress ; pas besoin d'une nouvelle taille d'image |

## Spécifications

### 1. Ajuster l'image à la une desktop

Modifier `latelierkiyose/assets/css/components/blog-single.css`.

Le style de base reste inchangé pour mobile et tablet :

```css
.blog-single__featured-image {
	border-radius: 8px;
	box-shadow: 0 4px 12px rgb(0 0 0 / 10%);
	margin-top: var(--kiyose-spacing-xl);
	overflow: hidden;
}

.blog-single__featured-image img {
	display: block;
	height: auto;
	width: 100%;
}
```

Ajouter une règle desktop :

```css
@media (width >= 1024px) {
	.blog-single__featured-image {
		margin-left: auto;
		margin-right: auto;
		max-width: 50%;
		width: 420px;
	}
}
```

Règles attendues :

- `max-width: 50%` garantit une image proche de la moitié du conteneur si `.blog-single` reste à `800px`.
- `width: 420px` donne une taille cible stable si la colonne éditoriale évolue.
- L'image interne reste à `width: 100%` pour remplir son wrapper.
- Aucun `float` ne doit être introduit.
- Aucun changement de marge verticale n'est requis dans ce PRD.

### 2. Régénérer les assets minifiés

Régénérer :

- `latelierkiyose/assets/css/components/blog-single.min.css`
- `latelierkiyose/assets/css/components/blog-single.min.css.map`

Utiliser la commande de build existante du dépôt. Ne pas éditer les fichiers minifiés manuellement.

### 3. Vérifier le scope

Le changement ne doit pas affecter :

- les images dans `.blog-single__content` ;
- les alignements Gutenberg `.alignleft`, `.alignright`, `.aligncenter` définis par le PRD 0040 ;
- les cartes de blog (`.blog-card`) ;
- les listes Events Manager (`.calendar-page__content .em-events-list`, `.em-item`) ;
- les images de la home ou des templates de pages.

## Fichiers à modifier

| Fichier | Action |
|---|---|
| `latelierkiyose/assets/css/components/blog-single.css` | Ajouter la contrainte desktop de `.blog-single__featured-image` |
| `latelierkiyose/assets/css/components/blog-single.min.css` | Régénérer |
| `latelierkiyose/assets/css/components/blog-single.min.css.map` | Régénérer |

## Accessibilité et performance

- L'image conserve son `alt` actuel, fourni par `single.php` via le titre de l'article.
- Aucun élément interactif n'est ajouté ou supprimé.
- Le focus clavier n'est pas impacté.
- La réduction desktop peut améliorer le confort de lecture en diminuant la hauteur visuelle avant le contenu.
- Aucun nouveau téléchargement d'image n'est introduit ; WordPress continue de fournir les sources responsives natives.

## Documentation

Aucune documentation utilisateur n'est requise : le changement ne crée ni shortcode, ni classe CSS rédacteur, ni template de page, ni option admin.

Aucune documentation release n'est requise : le changement n'ajoute pas de plugin, de migration, de nouvelle option, de hook d'activation ou de prérequis de production.

## Hors périmètre

- Créer une nouvelle taille d'image WordPress avec `add_image_size()`.
- Modifier le markup PHP de `single.php`.
- Modifier les templates Events Manager.
- Modifier les images dans le contenu éditorial Gutenberg.
- Ajouter une lightbox ou un comportement au clic.
- Changer le style des cartes de blog, de la page calendrier ou de la home.

## Risques et points d'attention

| Risque | Mitigation |
|---|---|
| L'événement `/events/.../` utilise un autre template en production | Valider le sélecteur réel dans le navigateur ; si `.blog-single__featured-image` n'est pas présent, créer un PRD dédié au template Events Manager |
| Image trop petite pour un visuel contenant du texte | Vérifier les deux URLs d'exemple sur desktop ; ajuster la largeur cible à `480px` si le contenu de l'image devient illisible |
| Régression sur les articles de blog classiques | Tester au moins un article avec image à la une et un article sans image à la une |
| Minifiés obsolètes | Régénérer les assets et inclure les `.map` dans le changement |

## Tests et validation

### Validation automatisée

- [ ] `npm run lint:css` passe.
- [ ] `make test` passe.
- [ ] Les fichiers minifiés sont à jour après le build.

Ce PRD ne demande pas de test unitaire PHP ou JavaScript : aucun comportement applicatif n'est modifié, seulement une règle de présentation CSS.

### Validation visuelle

Tester les deux URLs d'exemple :

- [ ] Desktop `>= 1024px` : l'image à la une est centrée et occupe environ la moitié de la colonne.
- [ ] Desktop `>= 1024px` : le titre, la date, l'image et le contenu restent dans un flux vertical cohérent.
- [ ] Tablet `768px - 1023px` : l'image conserve le comportement pleine largeur actuel.
- [ ] Mobile `320px - 767px` : l'image conserve le comportement pleine largeur actuel.
- [ ] L'image conserve son ratio original, sans crop ni déformation.
- [ ] Les pages sans image à la une ne changent pas visuellement.
- [ ] Les images insérées dans le contenu de l'article gardent les comportements du PRD 0040.
