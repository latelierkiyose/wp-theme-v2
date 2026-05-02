# PRD 0054 — Navigation précédent/suivant des événements par date

- **Statut** : terminé
- **Version** : 1.0
- **Date** : 2026-05-02

## Contexte

Les pages de détail d'événements Events Manager, par exemple `/events/.../`, utilisent actuellement le fallback WordPress `single.php`, comme les articles de blog.

En bas de `single.php`, le bloc `.blog-single__navigation` appelle `the_post_navigation()`. Cette API WordPress affiche l'article précédent et l'article suivant selon la navigation native des posts, donc selon l'ordre WordPress, pas selon la date métier de l'événement.

Pour les articles de blog, ce comportement est acceptable. Pour les événements, il crée une navigation trompeuse : l'événement précédent/suivant peut être déterminé par la date de création ou publication du contenu, et non par la date de l'atelier ou du stage.

Events Manager expose des métadonnées de date sur les événements, notamment `_event_start_date`, `_event_start`, `_start_ts`. Le thème utilise déjà le post type `event` dans ses tests et conditions d'assets.

## Problème à résoudre

Sur une page événement, l'utilisateur s'attend à naviguer vers l'événement chronologiquement précédent ou suivant, pas vers le contenu créé avant ou après dans WordPress.

Le comportement actuel peut donner l'impression que le calendrier est désordonné ou que les événements sont mal reliés entre eux.

## Objectif

Remplacer la navigation native WordPress uniquement pour les événements :

1. les articles de blog conservent la navigation actuelle ;
2. les événements affichent précédent/suivant par date de début d'événement ;
3. si la date d'un événement est absente ou inexploitable, le bloc de navigation événement est masqué ;
4. aucun fallback vers l'ordre de création/publication ne doit être utilisé pour les événements.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Scope | Modifier le rendu dans `single.php` uniquement | C'est le template réellement utilisé par les articles et événements |
| Articles | Garder `the_post_navigation()` | Le comportement WordPress reste pertinent pour le blog |
| Événements | Calculer une navigation dédiée | La navigation doit suivre la date métier de l'événement |
| Date de référence | Utiliser la date de début | C'est le repère naturel pour comparer des événements |
| Repli | Masquer le bloc si la date est absente ou invalide | Mieux vaut ne rien afficher que montrer une navigation fausse |
| HTML/CSS | Réutiliser les classes existantes | Conserve le design et limite le risque visuel |
| Libellés | `Événement précédent` / `Événement suivant` | Évite d'appeler un événement "article" |

## Spécifications

### 1. Ajouter des helpers de navigation événement

Ajouter des fonctions préfixées `kiyose_` dans `latelierkiyose/inc/blog.php`.

Comportement attendu :

- identifier la date de début d'un événement depuis les metas Events Manager disponibles ;
- priorité recommandée :
  1. `_start_ts` si présent et numérique ;
  2. `_event_start` si parseable ;
  3. `_event_start_date` si parseable ;
- récupérer les événements publiés du post type `event` ;
- ignorer les événements sans date exploitable dans le calcul ;
- trier par date de début ascendante, puis par ID pour stabiliser les égalités ;
- trouver l'événement immédiatement précédent et l'événement immédiatement suivant autour de l'événement courant ;
- retourner une chaîne HTML vide si aucun lien n'est disponible.

Le HTML rendu doit rester compatible avec le CSS existant :

```html
<nav class="navigation post-navigation" aria-label="Navigation entre événements">
	<div class="nav-links">
		<div class="nav-previous">
			<a href="...">
				<span class="nav-subtitle">Événement précédent</span>
				<span class="nav-title">Titre</span>
			</a>
		</div>
		<div class="nav-next">
			<a href="...">
				<span class="nav-subtitle">Événement suivant</span>
				<span class="nav-title">Titre</span>
			</a>
		</div>
	</div>
</nav>
```

Tous les titres, URLs et attributs doivent être échappés.

### 2. Brancher le rendu dans `single.php`

Dans `latelierkiyose/single.php`, remplacer l'appel direct à `the_post_navigation()` par une branche :

- si `get_post_type() === 'event'`, rendre la navigation événement dédiée ;
- sinon, conserver `the_post_navigation()` avec les libellés actuels `Article précédent` / `Article suivant`.

Si la navigation événement dédiée retourne une chaîne vide, ne pas afficher de markup vide à l'intérieur du bloc.

### 3. Préserver l'apparence existante

Aucun changement CSS n'est attendu si le HTML conserve les classes actuelles :

- `.blog-single__navigation`
- `.post-navigation`
- `.nav-previous`
- `.nav-next`
- `.nav-subtitle`
- `.nav-title`

Ne pas modifier les assets minifiés sauf si une adaptation CSS devient nécessaire pendant l'implémentation.

## Fichiers à modifier

| Fichier | Action |
|---|---|
| `latelierkiyose/inc/blog.php` | Ajouter les helpers de navigation événement |
| `latelierkiyose/single.php` | Brancher navigation événement vs navigation article |
| `latelierkiyose/tests/Test_Blog.php` | Ajouter les tests de comportement |
| `latelierkiyose/tests/bootstrap.php` | Étendre les mocks WordPress si nécessaire |

## Accessibilité et sécurité

- Le `<nav>` doit avoir un `aria-label` spécifique aux événements.
- Les liens doivent conserver les styles de focus existants.
- Aucun nouvel élément interactif hors lien n'est ajouté.
- Les URLs doivent passer par `esc_url()`.
- Les titres et libellés doivent passer par `esc_html()` / `esc_html__()`.
- Aucun contenu sensible n'est manipulé ou loggé.

## Documentation

Aucune documentation utilisateur n'est requise : le changement ne crée ni shortcode, ni classe CSS rédacteur, ni template de page, ni option admin.

Aucune documentation release n'est requise : pas de plugin ajouté, pas de migration, pas de nouvelle option, pas de hook d'activation.

## Hors périmètre

- Créer un template Events Manager dédié.
- Modifier les listes `[events_list]` ou la page Calendrier.
- Modifier l'ordre des événements dans Events Manager.
- Ajouter une option admin pour activer/désactiver cette navigation.
- Changer le design visuel du bloc de navigation.
- Modifier la navigation des articles de blog.

## Risques et points d'attention

| Risque | Mitigation |
|---|---|
| Certaines installations Events Manager stockent la date dans une meta différente | Utiliser plusieurs metas connues dans un ordre de priorité |
| Deux événements ont la même date | Trier ensuite par ID pour obtenir un ordre stable |
| Un événement n'a pas de date exploitable | Masquer le bloc au lieu de revenir à l'ordre WordPress |
| Trop de requêtes si le volume d'événements devient élevé | Requête limitée aux IDs, metas nécessaires, posts publiés ; acceptable pour le volume attendu du site |
| Régression sur les articles | Garder `the_post_navigation()` pour les contenus non `event` et ajouter un test dédié |

## Tests et validation

### Validation automatisée

- [ ] Un événement au milieu d'une série datée affiche l'événement précédent et suivant chronologiques.
- [ ] Le premier événement daté affiche seulement l'événement suivant.
- [ ] Le dernier événement daté affiche seulement l'événement précédent.
- [ ] Un événement sans date exploitable masque la navigation événement.
- [ ] Un article de blog conserve les libellés `Article précédent` / `Article suivant`.
- [ ] `./bin/phpunit.sh --testdox` passe.
- [ ] `./bin/phpcs.sh` passe.
- [ ] `make test` passe.

### Validation manuelle

- [ ] Ouvrir un événement passé, un événement intermédiaire et un événement futur.
- [ ] Vérifier que les liens suivent l'ordre des dates d'événement.
- [ ] Vérifier qu'un article de blog garde la navigation précédente.
- [ ] Tester au clavier : Tab, Entrée, focus visible.
- [ ] Vérifier mobile `320px+` : aucun chevauchement ou bloc vide.

## Références

- WordPress Developer Reference — `the_post_navigation()` : https://developer.wordpress.org/reference/functions/the_post_navigation/
- Support WordPress Events Manager — metas connues dont `_event_start_date`, `_event_start`, `_start_ts` : https://wordpress.org/support/?p=16932232
