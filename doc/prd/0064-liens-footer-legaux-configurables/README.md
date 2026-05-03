# PRD 0064 — Liens légaux du footer configurables

- **Statut** : terminé
- **Criticité** : moyenne
- **Version** : 1.0
- **Date** : 2026-05-03

## Contexte

Le footer du thème affiche deux liens légaux :

1. « Mentions légales » ;
2. « Politique de confidentialité ».

Aujourd'hui, ces liens sont construits avec des slugs codés en dur dans `latelierkiyose/footer.php` :

- `/mentions-legales/` ;
- `/politique-de-confidentialite/`.

Ce choix est fragile. Si une page change de slug, si elle est recréée avec un autre slug, ou si l'installation cible utilise une page existante différente, le footer peut pointer vers une 404. Pour des liens légaux, ce risque est plus problématique qu'un simple lien de navigation cassé : les pages doivent rester accessibles depuis toutes les pages du site.

## Objectif

Rendre les deux liens légaux du footer configurables globalement dans le thème, sans URL codée en dur :

1. permettre de choisir une page WordPress existante pour « Mentions légales » ;
2. permettre de choisir une page WordPress existante pour « Politique de confidentialité » ;
3. rendre les liens depuis les permaliens des pages choisies ;
4. ne jamais générer de lien frontend vers un slug supposé ;
5. documenter l'action de migration à réaliser après activation ou mise à jour du thème.

## Décisions de conception

| Sujet | Décision | Justification |
|---|---|---|
| Source de vérité | Options globales du thème stockant des IDs de pages | Les IDs restent valides si le slug change |
| Interface admin | Customizer, section dédiée « Footer — liens légaux » | Cohérent avec les boutons configurables des pages de service |
| Type de contrôle | `dropdown-pages` | Force la sélection d'une page réelle au lieu d'une URL libre |
| Libellés frontend | Libellés fixes et traduisibles | Le besoin porte sur la cible des liens, pas sur leur texte |
| Page non configurée ou non publiée | Le lien concerné n'est pas rendu | Évite les liens morts ou les pages privées exposées |
| Validation production | Les deux pages doivent être configurées dans la migration contenu | Les liens peuvent être masqués défensivement, mais ils restent obligatoires pour le site livré |
| Création automatique de pages | Hors périmètre | Le thème ne doit pas créer du contenu légal à la place de l'administrateurice |

## Spécifications

### 1. Réglages Customizer

Ajouter une section « Footer — liens légaux » dans le Customizer.

Réglages :

| Réglage | Contrôle | Rôle |
|---|---|---|
| `kiyose_footer_legal_notice_page_id` | Liste de pages | Page cible du lien « Mentions légales » |
| `kiyose_footer_privacy_policy_page_id` | Liste de pages | Page cible du lien « Politique de confidentialité » |

Règles :

- les IDs sont sanitizés avec `absint()` ;
- le transport peut rester en `refresh` ;
- les contrôles peuvent permettre la création de page depuis le Customizer si le pattern existant `allow_addition` est conservé ;
- aucune URL libre ne doit être saisie ni stockée pour ces liens ;
- aucune dépendance à un slug précis ne doit rester pour ces deux liens.

### 2. Rendu frontend du footer

Modifier `latelierkiyose/footer.php` pour ne plus appeler `home_url()` avec des chemins légaux codés en dur.

Règles :

- récupérer chaque page configurée via son ID ;
- considérer une page valide uniquement si elle existe et que son statut est `publish` ;
- générer l'URL avec le permalien WordPress de la page valide ;
- échapper les URLs avec `esc_url()` ;
- échapper les libellés avec `esc_html_e()` ou équivalent ;
- conserver les classes CSS existantes de `.site-footer__legal` ;
- si une seule page est valide, rendre seulement son lien ;
- si aucune page n'est valide, ne pas rendre le conteneur `.site-footer__legal`.

### 3. Helper thème

Créer ou réutiliser un helper PHP pour isoler la résolution des pages légales du rendu HTML.

Comportement attendu :

- une fonction retourne les liens légaux valides sous forme de structure simple, par exemple label + URL ;
- le helper ignore les pages absentes, supprimées, non publiées ou configurées avec `0` ;
- le helper est testable sans devoir rendre tout le template footer ;
- les fonctions ajoutées respectent le préfixe `kiyose_`.

### 4. Documentation utilisateur

Mettre à jour la documentation utilisateur parce que l'implémentation expose un nouveau réglage global utilisable par l'administrateurice du site.

Fichiers attendus :

- `doc/user/02-choisir-un-template.md` pour rappeler que les pages légales restent des pages standard ;
- un fichier utilisateur plus pertinent si une page dédiée aux réglages globaux du thème existe au moment de l'implémentation.

À documenter :

- où trouver le réglage dans le Customizer ;
- quelles pages choisir ;
- le fait que le changement de slug d'une page ne nécessite pas de modification du thème.

### 5. Documentation de migration

Mettre à jour `doc/release/content-migration.md` dans le même changement d'implémentation.

Cette mise à jour doit ajouter une étape explicite de migration :

1. ouvrir **Apparence → Personnaliser → Footer — liens légaux** ;
2. sélectionner la page publiée des mentions légales ;
3. sélectionner la page publiée de politique de confidentialité ;
4. publier les réglages ;
5. vérifier sur le front que les deux liens du footer pointent vers les pages choisies.

La checklist finale de migration doit aussi inclure un item dédié : les deux liens légaux du footer sont configurés et ne retournent pas de 404.

### 6. Approches écartées

| Approche | Raison du rejet |
|---|---|
| Garder les slugs `/mentions-legales/` et `/politique-de-confidentialite/` | Ne respecte pas le besoin : les liens restent fragiles et dépendent d'une convention de contenu |
| Utiliser des champs URL libres | Trop permissif : permet les erreurs de saisie et ne garantit pas une page WordPress réelle |
| Utiliser uniquement le réglage WordPress natif de politique de confidentialité | Couvre seulement un des deux liens et laisse les mentions légales sans configuration équivalente |
| Créer automatiquement les pages légales | Risque de créer du contenu juridique incomplet ou faux |

## Accessibilité et sécurité

- Les liens restent de vrais éléments `<a>`, accessibles au clavier.
- Les styles de focus existants de `.site-footer__legal a` sont conservés.
- Les options admin sont sanitizées avec `absint()`.
- Les URLs frontend sont échappées avec `esc_url()`.
- Les libellés visibles sont traduisibles avec le text domain `kiyose`.
- Aucune donnée sensible n'est manipulée.

## Hors périmètre

- Écrire ou générer le contenu des pages légales.
- Créer automatiquement les pages « Mentions légales » ou « Politique de confidentialité ».
- Rendre les libellés des liens configurables.
- Ajouter un menu WordPress dédié au footer.
- Modifier le style visuel du footer.
- Ajouter une page de politique cookies séparée.

## Tests et validation

### Tests automatisés

- [ ] Les deux réglages Customizer existent avec un contrôle de type `dropdown-pages`.
- [ ] Les deux réglages sont sanitizés avec `absint()`.
- [ ] Le helper retourne le permalien d'une page publiée configurée.
- [ ] Le helper ignore une page non configurée.
- [ ] Le helper ignore une page supprimée ou inexistante.
- [ ] Le helper ignore une page non publiée.
- [ ] Le footer rend les deux liens quand les deux pages sont valides.
- [ ] Le footer rend un seul lien quand une seule page est valide.
- [ ] Le footer ne rend pas `.site-footer__legal` quand aucune page valide n'existe.
- [ ] `footer.php` ne contient plus les chemins `/mentions-legales/` ni `/politique-de-confidentialite/`.

### Validation manuelle

- [ ] Dans le Customizer, sélectionner deux pages publiées puis publier les réglages.
- [ ] Sur le front, vérifier que les deux liens apparaissent dans le footer.
- [ ] Modifier le slug d'une des pages choisies puis vérifier que le footer pointe vers le nouveau permalien.
- [ ] Dépublier temporairement une page choisie puis vérifier que son lien disparaît du footer.
- [ ] Tester la navigation clavier jusqu'aux liens du footer et vérifier le focus visible.
- [ ] Vérifier le rendu responsive à partir de 320 px.

### Validation finale

- [ ] `./bin/phpunit.sh --testdox` passe.
- [ ] `./bin/phpcs.sh` passe.
- [ ] `make test` passe.
- [ ] Documentation utilisateur mise à jour avec l'emplacement du réglage global.
- [ ] `doc/release/content-migration.md` mis à jour avec l'étape de configuration des liens légaux du footer.
