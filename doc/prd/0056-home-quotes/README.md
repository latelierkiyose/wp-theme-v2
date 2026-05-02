# PRD 0056 — Citation structurée du bloc Contenu 2 de l'accueil

- **Statut** : terminé
- **Version** : 1.0
- **Date** : 2026-05-02

## Contexte

La page d'accueil affiche, dans le bloc `home-content2`, un texte libre suivi d'un élément actuellement nommé `content2_slogan`.

Cet élément est rendu par `template-parts/home/content-free.php` sous la forme :

```php
<p class="home-content2__slogan home-emphase">...</p>
```

La classe partagée `.home-emphase`, introduite par le PRD 0036, ajoute des guillemets français décoratifs via CSS (`::before` / `::after`). Ce comportement convient aux slogans courts ou aux phrases seules, mais il devient incorrect quand le contenu éditorial contient à la fois la citation et son auteurice dans le même champ.

Exemple de rendu actuel :

> « « Heureux soient les fêlés, car ils laissent passer la lumière » Michel Audiard »

Rendu attendu :

> « Heureux soient les fêlés, car ils laissent passer la lumière »
>
> Michel Audiard

Point d'attention : ce n'est pas seulement un problème de CSS. Le modèle de contenu confond deux informations différentes :

- le texte cité ;
- l'attribution de la citation.

## Problème à résoudre

Le champ `kiyose_content2_slogan` force les rédacteurices à saisir une citation complète, guillemets et auteurice inclus. Le thème ajoute ensuite ses propres guillemets CSS autour de l'ensemble, ce qui produit :

1. une double ponctuation ;
2. une attribution affichée comme si elle faisait partie de la citation ;
3. une consigne éditoriale ambiguë dans l'admin et la documentation ;
4. un HTML moins sémantique qu'un vrai bloc de citation avec attribution.

## Objectif

Transformer le bloc `home-content2__slogan` en composant de citation structuré :

1. le texte cité est saisi dans un champ `citation` ;
2. l'auteurice est saisi dans un champ séparé `auteurice` ;
3. seuls les mots de la citation sont entourés par les guillemets CSS ;
4. l'auteurice s'affiche sous la citation, sans guillemets ;
5. le rendu respecte la typographie française : `«` + espace insécable + citation + espace insécable + `»` ;
6. l'ancien contenu ne disparaît pas lors de la mise à jour du thème.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Nature du contenu | Traiter le bloc Contenu 2 comme une vraie citation, pas comme un slogan | Le besoin métier demande explicitement citation + auteurice |
| Stockage de la citation | Réutiliser la meta existante `kiyose_content2_slogan` pour le texte cité | Évite de perdre le contenu déjà saisi et limite la migration technique |
| Nouveau champ | Ajouter `kiyose_content2_quote_author` pour l'auteurice | Le champ manquant est l'attribution, pas la phrase elle-même |
| API interne | Exposer `content2_quote` et `content2_quote_author` dans `kiyose_get_home_page_data()` | Les noms reflètent le nouveau modèle produit |
| Compatibilité | Ne plus utiliser `content2_slogan` dans le template, mais garder une lecture de la meta existante | Le vocabulaire "slogan" doit sortir du rendu, sans imposer une migration SQL |
| Migration éditoriale | Ne pas tenter de parser automatiquement `« citation » auteurice` | Le format existant n'est pas fiable à parser sans faux positifs |
| HTML | Utiliser une structure sémantique `figure` / `blockquote` / `figcaption` / `cite` | Meilleure représentation d'une citation avec attribution |
| Guillemets | Conserver des guillemets générés en CSS autour du texte cité uniquement | Cohérent avec l'identité visuelle existante et évite de les stocker dans le contenu |
| Auteurice | Afficher l'auteurice sans tiret automatique, sans guillemets, avec la même police et couleur que la citation mais dans une taille plus petite | L'attribution doit appartenir visuellement à la citation sans entrer en concurrence avec elle |
| Champ vide | Afficher la citation même si l'auteurice est vide ; masquer seulement l'attribution | Permet une citation anonyme ou sans attribution connue |

## Spécifications

### 1. Renommer conceptuellement le champ de citation dans l'admin

Modifier `latelierkiyose/inc/meta-boxes.php`.

Dans la section `Bloc Contenu 2`, remplacer le libellé admin actuel :

- `Citation / Slogan (optionnel)`

par :

- `Citation (sans guillemets)`

Le champ continue d'écrire dans la meta existante :

- `kiyose_content2_slogan`

Description attendue :

> Saisis uniquement le texte cité, sans guillemets et sans nom d'auteurice.

Règles attendues :

- conserver un champ texte simple ;
- continuer à sauvegarder avec `sanitize_text_field()`;
- ne pas ajouter automatiquement les guillemets dans la valeur sauvegardée ;
- ne pas supprimer la meta `kiyose_content2_slogan`.

### 2. Ajouter un champ auteurice

Ajouter un champ texte dans `latelierkiyose/inc/meta-boxes.php`.

| Élément | Valeur attendue |
|---|---|
| Label admin | `Auteurice de la citation` |
| Meta key | `kiyose_content2_quote_author` |
| Type | Texte court |
| Obligatoire | Non |
| Sanitization | `sanitize_text_field()` |

Description attendue :

> Exemple : Michel Audiard. Laisse vide si la citation n'a pas d'attribution.

Le champ doit être affiché immédiatement sous le champ `Citation (sans guillemets)`.

### 3. Exposer les données structurées côté template

Modifier `latelierkiyose/inc/home.php`.

`kiyose_get_home_page_data()` doit retourner :

```php
'content2_quote'        => get_post_meta( $post_id, 'kiyose_content2_slogan', true ),
'content2_quote_author' => get_post_meta( $post_id, 'kiyose_content2_quote_author', true ),
```

Règles attendues :

- `content2_quote` lit volontairement l'ancienne meta `kiyose_content2_slogan` ;
- `content2_quote_author` lit la nouvelle meta `kiyose_content2_quote_author` ;
- les valeurs par défaut sont des chaînes vides si aucune meta n'existe ;
- le template ne doit plus dépendre de `content2_slogan`.

### 4. Rendre une citation sémantique

Modifier `latelierkiyose/template-parts/home/content-free.php`.

Le rendu attendu, quand une citation est présente :

```html
<figure class="home-content2__quote-block">
	<blockquote class="home-content2__quote home-emphase">
		<p>Heureux soient les fêlés, car ils laissent passer la lumière</p>
	</blockquote>
	<figcaption class="home-content2__quote-author">
		<cite>Michel Audiard</cite>
	</figcaption>
</figure>
```

Règles attendues :

- le bloc `figure` est rendu seulement si `content2_quote` n'est pas vide ;
- le `figcaption` est rendu seulement si `content2_quote_author` n'est pas vide ;
- la citation passe par `esc_html()` puis `kiyose_fr_nbsp()` ;
- l'auteurice passe par `esc_html()` ;
- aucun guillemet n'est ajouté dans le PHP ;
- aucun tiret ou préfixe n'est ajouté avant l'auteurice ;
- le CTA existant reste rendu après la citation ;
- la section `home-content2` continue de s'afficher si `content2_text` ou `content2_quote` est renseigné.

### 5. Adapter les styles CSS

Modifier `latelierkiyose/assets/css/components/home-content.css` et, si nécessaire, `latelierkiyose/assets/css/components/home-sections.css`.

Comportement attendu :

- les guillemets CSS existants s'appliquent au `blockquote.home-content2__quote`, pas à l'auteurice ;
- l'auteurice s'affiche sous la citation avec un espacement clair ;
- l'auteurice n'hérite pas des pseudo-éléments `::before` / `::after`;
- l'auteurice utilise la même couleur et le même poids typographique que la citation ;
- l'auteurice utilise une taille plus petite que la citation ;
- les marges du `blockquote` natif sont neutralisées pour conserver l'alignement existant ;
- la citation reste centrée, lisible et limitée à une largeur confortable ;
- le rendu reste correct à partir de `320px`.

Direction CSS recommandée :

```css
.home-content2__quote-block {
	margin: var(--kiyose-spacing-2xl) auto;
	max-width: 700px;
	text-align: center;
}

.home-content2__quote {
	margin-bottom: 0;
	margin-top: 0;
}

.home-content2__quote p {
	display: inline;
	margin: 0;
}

.home-content2__quote-author {
	color: var(--kiyose-color-burgundy);
	font-family: var(--kiyose-font-body);
	font-size: var(--kiyose-font-size-base);
	font-weight: 600;
	margin-top: var(--kiyose-spacing-sm);
}

.home-content2__quote-author cite {
	font-style: normal;
}
```

Les valeurs exactes peuvent être ajustées pendant la validation visuelle, mais l'auteurice ne doit jamais être inclus dans l'élément qui reçoit les guillemets CSS.

### 6. Régénérer les assets minifiés

Régénérer les fichiers générés par la chaîne de build existante :

- `latelierkiyose/assets/css/components/home-content.min.css`
- `latelierkiyose/assets/css/components/home-content.min.css.map`
- `latelierkiyose/assets/css/components/home-sections.min.css`
- `latelierkiyose/assets/css/components/home-sections.min.css.map`

Ne pas éditer les fichiers minifiés manuellement. Si `home-sections.css` n'est pas modifié, ne pas régénérer ses minifiés.

### 7. Mettre à jour la documentation rédacteurice

Mettre à jour `doc/user/recettes/modifier-la-page-d-accueil.md`.

La documentation doit expliquer :

- que le champ Contenu 2 contient maintenant `Texte libre`, `Citation` et `Auteurice de la citation` ;
- qu'il ne faut pas saisir les guillemets dans le champ `Citation` ;
- que l'auteurice doit être saisie dans le champ dédié ;
- que l'auteurice peut rester vide.

Inclure un exemple copiable :

| Champ | Exemple |
|---|---|
| Citation | `Heureux soient les fêlés, car ils laissent passer la lumière` |
| Auteurice de la citation | `Michel Audiard` |

### 8. Mettre à jour la documentation release

Mettre à jour `doc/release/content-migration.md`.

La documentation release doit indiquer :

- la nouvelle meta `kiyose_content2_quote_author` ;
- le fait que `kiyose_content2_slogan` reste la meta de stockage de la citation ;
- l'action manuelle attendue sur la page Accueil : retirer les guillemets et l'auteurice du champ citation, puis remplir le champ auteurice.

## Fichiers à modifier

| Fichier | Action |
|---|---|
| `latelierkiyose/inc/meta-boxes.php` | Renommer le libellé du champ citation et ajouter `kiyose_content2_quote_author` |
| `latelierkiyose/inc/home.php` | Exposer `content2_quote` et `content2_quote_author` |
| `latelierkiyose/template-parts/home/content-free.php` | Rendre `figure` / `blockquote` / `figcaption` / `cite` |
| `latelierkiyose/assets/css/components/home-content.css` | Ajouter les styles du bloc citation et de l'auteurice |
| `latelierkiyose/assets/css/components/home-content.min.css` | Régénérer |
| `latelierkiyose/assets/css/components/home-content.min.css.map` | Régénérer |
| `latelierkiyose/assets/css/components/home-sections.css` | Ajuster seulement si `.home-emphase` nécessite une adaptation pour `blockquote` |
| `latelierkiyose/assets/css/components/home-sections.min.css` | Régénérer seulement si la source change |
| `latelierkiyose/assets/css/components/home-sections.min.css.map` | Régénérer seulement si la source change |
| `latelierkiyose/tests/Test_Home.php` | Ajouter les tests de données structurées |
| `latelierkiyose/tests/Test_Meta_Boxes.php` | Ajouter les tests d'affichage/sauvegarde du champ auteurice si nécessaire |
| `latelierkiyose/tests/Test_Home_Content_CSS.php` | Nouveau test CSS recommandé pour le bloc citation |
| `doc/user/recettes/modifier-la-page-d-accueil.md` | Mettre à jour la recette rédacteurice |
| `doc/release/content-migration.md` | Ajouter la consigne de migration manuelle |

## Accessibilité et sécurité

- Utiliser `blockquote` pour le texte cité et `cite` pour l'auteurice.
- L'ordre DOM doit être : citation, puis auteurice.
- Les guillemets CSS sont décoratifs ; le contenu éditorial sauvegardé ne doit pas les contenir.
- Le contraste de la citation doit rester conforme WCAG 2.2 AA.
- Le contraste de l'auteurice bordeaux sur le fond rose de `home-content2` doit rester conforme WCAG 2.2 AA.
- Aucun nouvel élément interactif n'est ajouté.
- La navigation clavier n'est pas impactée.
- Les sorties doivent être échappées avec `esc_html()` et `kiyose_fr_nbsp()` quand nécessaire.
- Les entrées admin doivent être protégées par le nonce existant de la meta box et sauvegardées seulement pour le template `templates/page-home.php`.

## Documentation

Documentation utilisateur requise : oui, car un champ visible côté rédaction est ajouté et le mode de saisie change.

Documentation release requise : oui, car un nouveau meta field est attendu côté admin et une migration éditoriale manuelle est nécessaire.

## Hors périmètre

- Modifier les citations des témoignages.
- Modifier le composant `.home-content1__slogan`.
- Modifier le slogan du bloc bienvenue.
- Changer globalement le comportement de `.home-emphase` pour tous les usages si une règle ciblée suffit.
- Créer une migration SQL automatique.
- Parser automatiquement les anciennes chaînes `« citation » auteurice`.
- Ajouter plusieurs citations au bloc Contenu 2.
- Ajouter une option admin pour masquer le CTA.
- Modifier le texte ou l'URL du CTA `Réserver votre appel découverte`.

## Risques et points d'attention

| Risque | Mitigation |
|---|---|
| Le contenu existant garde temporairement les guillemets et l'auteurice dans le champ citation | Documenter une migration éditoriale manuelle claire dans `doc/release/content-migration.md` |
| Une tentative de parsing automatique casse une citation atypique | Ne pas parser automatiquement ; laisser la rédaction corriger le contenu |
| L'auteurice reçoit les guillemets CSS par héritage ou sélecteur trop large | Rendre l'auteurice hors du `blockquote.home-emphase` et couvrir par un test CSS |
| Le changement de clé interne casse le template | Ajouter des tests sur `kiyose_get_home_page_data()` et sur le template `content-free.php` |
| Les minifiés deviennent incohérents avec les sources | Régénérer via la commande de build et valider avec le check CSS |
| La documentation rédacteurice reste ambiguë | Donner un exemple copiable sans guillemets dans `doc/user/recettes/modifier-la-page-d-accueil.md` |

## Tests et validation

### Validation automatisée

- [ ] `Test_Home::test_kiyose_get_home_page_data_whenMetaFieldsAreEmpty_returnsDefaults` attend `content2_quote` et `content2_quote_author`.
- [ ] `Test_Home::test_kiyose_get_home_page_data_ofSavedMetaFields_returnsStructuredData` vérifie la citation et l'auteurice.
- [ ] Un test de template vérifie que `content-free.php` rend un `blockquote` quand `content2_quote` est renseigné.
- [ ] Un test de template vérifie que `figcaption` / `cite` est rendu quand `content2_quote_author` est renseigné.
- [ ] Un test de template vérifie que `figcaption` est absent quand l'auteurice est vide.
- [ ] Un test CSS vérifie que les pseudo-éléments de guillemets ciblent la citation et pas `.home-content2__quote-author`.
- [ ] Un test CSS vérifie que l'auteurice utilise la même couleur et le même poids que la citation, avec une taille plus petite.
- [ ] `./bin/phpunit.sh --testdox` passe.
- [ ] `npm run lint:css` passe.
- [ ] `npm run build:css:check` passe.
- [ ] `make test` passe.

### Validation manuelle

- [ ] Dans l'admin, le champ `Citation (sans guillemets)` est visible dans le bloc Contenu 2.
- [ ] Dans l'admin, le champ `Auteurice de la citation` est visible sous le champ citation.
- [ ] Avec citation + auteurice, le front affiche les guillemets autour de la citation uniquement.
- [ ] Avec citation + auteurice, l'auteurice apparaît sur une ligne séparée, sans guillemets, dans le même style que la citation mais plus petit.
- [ ] Avec citation seule, aucun espace vide ou `figcaption` vide n'est rendu.
- [ ] Avec auteurice seule, aucun bloc citation vide n'est rendu.
- [ ] Avec texte libre seul, la section continue de s'afficher comme avant.
- [ ] Avec texte libre + citation + CTA, l'espacement reste cohérent sur desktop.
- [ ] Mobile `320px+` : la citation et l'auteurice ne débordent pas et ne se chevauchent pas.
- [ ] Le rendu de `.welcome-block__slogan` et `.home-content1__slogan` reste inchangé.

## Critères d'acceptation

- [ ] Le rendu `« « citation » auteurice »` n'est plus possible quand les champs sont remplis selon la documentation.
- [ ] Le contenu éditorial ne stocke pas de guillemets typographiques autour de la citation.
- [ ] L'auteurice n'est jamais incluse dans l'élément décoré par les guillemets CSS.
- [ ] Le HTML du bloc citation est sémantique (`blockquote`, `figcaption`, `cite`).
- [ ] Les anciennes valeurs de `kiyose_content2_slogan` restent lisibles après déploiement.
- [ ] La documentation utilisateur et release décrit clairement la migration manuelle.
