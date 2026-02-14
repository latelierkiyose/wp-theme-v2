# Guide de test - Services dynamiques sur page d'accueil

**Feature** : Rendre les 4 piliers (services) affichés sur la page d'accueil dynamiques via meta box

**Version** : 0.1.17

## Pré-requis

- Environnement local démarré (`make start`)
- Thème "L'Atelier Kiyose" activé
- 4 pages de services créées avec template "Page de service"

## Tests à effectuer

### Test 1 : Vérifier que la meta box s'affiche uniquement sur les pages de service

1. Se connecter à l'admin WordPress : http://127.0.0.1:8000/wp-admin
2. Aller dans **Pages > Toutes les pages**
3. Créer ou éditer une page avec le template **"Page de service"**
4. **Vérifier** : Dans la sidebar droite, la meta box **"Affichage page d'accueil"** doit apparaître
5. Créer ou éditer une page avec un template **différent** (ex: "Page par défaut")
6. **Vérifier** : La meta box doit afficher un message d'information indiquant qu'elle n'est disponible que pour les pages de service

**Résultat attendu** : ✅ La meta box s'affiche uniquement sur les pages avec template "Page de service"

---

### Test 2 : Marquer des pages pour affichage sur la page d'accueil

1. Éditer chacune des 4 pages de services :
   - Art-thérapie
   - Rigologie / Yoga du rire
   - Bols tibétains
   - Ateliers philosophie

2. Pour chaque page :
   - Vérifier que le template "Page de service" est sélectionné
   - Cocher la case **"Afficher dans la section 'Nos activités'"**
   - Dans "Attributs de page", définir un **Ordre** (1, 2, 3, 4)
   - Cliquer sur **"Mettre à jour"**

3. **Vérifier** : Les modifications sont bien enregistrées (la case reste cochée après rechargement)

**Résultat attendu** : ✅ Les 4 pages sont marquées et ont un ordre défini

---

### Test 3 : Vérifier l'affichage sur la page d'accueil

1. Aller sur la page d'accueil : http://127.0.0.1:8000/
2. Scroller jusqu'à la section **"Nos activités"**
3. **Vérifier** :
   - Les 4 cartes de services s'affichent
   - Elles sont dans l'ordre défini (1 → 2 → 3 → 4)
   - Chaque carte affiche : titre, excerpt, image à la une (si définie)
   - Le lien "En savoir plus" fonctionne

**Résultat attendu** : ✅ Les 4 services s'affichent dans le bon ordre

---

### Test 4 : Modifier l'ordre d'affichage

1. Éditer la page "Art-thérapie"
2. Changer l'**Ordre** de 1 à 4
3. Éditer la page "Ateliers philosophie"
4. Changer l'**Ordre** de 4 à 1
5. Mettre à jour les deux pages
6. Recharger la page d'accueil
7. **Vérifier** : L'ordre des cartes a changé (Ateliers philosophie en premier, Art-thérapie en dernier)

**Résultat attendu** : ✅ L'ordre s'adapte dynamiquement selon le champ `menu_order`

---

### Test 5 : Décocher une page

1. Éditer la page "Rigologie"
2. **Décocher** la case "Afficher dans la section 'Nos activités'"
3. Mettre à jour
4. Recharger la page d'accueil
5. **Vérifier** : Seules 3 cartes s'affichent (plus de Rigologie)

**Résultat attendu** : ✅ Les services décochés ne s'affichent plus sur la page d'accueil

---

### Test 6 : Ajouter un nouveau service

1. Créer une nouvelle page : "Sophrologie"
2. Assigner le template **"Page de service"**
3. Cocher **"Afficher dans la section 'Nos activités'"**
4. Définir un **Ordre** : 5
5. Publier
6. Recharger la page d'accueil
7. **Vérifier** : La nouvelle carte "Sophrologie" apparaît en 5ème position

**Résultat attendu** : ✅ Les nouveaux services peuvent être ajoutés sans modifier le code

---

### Test 7 : Page sans service marqué

1. Décocher **toutes** les pages de services
2. Mettre à jour
3. Recharger la page d'accueil
4. **Vérifier** : La section "Nos activités" affiche un message : "Aucun service n'est actuellement configuré pour l'affichage sur la page d'accueil."

**Résultat attendu** : ✅ Un message informatif s'affiche si aucun service n'est marqué

---

### Test 8 : Script de migration (optionnel)

**Note** : Ce test est optionnel si les pages sont déjà marquées manuellement.

1. Se connecter en SSH au container WordPress :
   ```bash
   docker compose exec wordpress bash
   ```

2. Exécuter le script de migration :
   ```bash
   wp eval-file wp-content/themes/latelierkiyose/bin/migrate-homepage-services.php
   ```

3. **Vérifier** dans la sortie :
   - 4 pages mises à jour
   - Aucune erreur
   - Message de succès

4. Aller dans l'admin WordPress
5. Éditer chaque page de service
6. **Vérifier** : La case "Afficher dans la section 'Nos activités'" est cochée
7. **Vérifier** : L'ordre est défini (1, 2, 3, 4)

**Résultat attendu** : ✅ Le script marque automatiquement les 4 pages existantes

---

## Validation PHPCS

```bash
make phpcs
```

**Résultat attendu** : ✅ 0 erreurs, 0 warnings

---

## Checklist finale

- [ ] Meta box visible uniquement sur pages de service
- [ ] Checkbox fonctionne (enregistrement + affichage)
- [ ] Ordre d'affichage respecté (menu_order)
- [ ] Ajout/retrait de services dynamique
- [ ] Message si aucun service marqué
- [ ] Script de migration fonctionne (si testé)
- [ ] PHPCS passe sans erreur
- [ ] Documentation mise à jour (deployment-guide.md)

---

## Notes techniques

**Fichiers modifiés** :
- `inc/meta-boxes.php` : Ajout de la meta box `kiyose_show_on_homepage`
- `templates/page-home.php` : Remplacement de la query hardcodée par meta query
- `bin/migrate-homepage-services.php` : Script de migration one-time
- `doc/deployment-guide.md` : Ajout de l'étape 3.5 (configuration services)
- `phpcs.xml` : Exclusion du répertoire `bin/`

**Comportement** :
- La meta box s'affiche uniquement sur les pages avec template `templates/page-services.php`
- Les pages sont triées par `menu_order` (Attributs de page > Ordre)
- Si une page change de template, la meta `kiyose_show_on_homepage` est automatiquement supprimée
