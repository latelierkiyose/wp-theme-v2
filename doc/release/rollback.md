# Rollback & diagnostic — en cas de pépin

Ce document couvre les procédures de repli si la mise en production pose problème. À lire **avant** le déploiement pour savoir vers quoi se tourner en urgence.

> 🔗 Revenir à la procédure principale → [`README.md`](README.md)

## ⚡ Règles d'or

1. **Ne pas paniquer**. La sauvegarde préalable (étape 1 de [`README.md`](README.md)) garantit qu'aucun dégât n'est permanent.
2. **Identifier la gravité avant d'agir** : site encore accessible ? admin accessible ? écran blanc ? → diagnostic rapide ci-dessous.
3. **Toujours commencer par le niveau de rollback le plus léger** qui résout le problème. Réserver la restauration complète à la dernière extrémité.

---

## Diagnostic rapide — symptômes fréquents

| Symptôme | Diagnostic probable | Action rapide |
|---|---|---|
| Écran blanc sur le site entier | Erreur PHP fatale | Voir logs PHP → rollback niveau 1 ou 2 |
| Admin inaccessible (écran blanc / 500) | Erreur PHP fatale côté thème ou plugin | Rollback niveau 2 via SFTP |
| Homepage affiche `[events_list limit="4"...]` en texte brut | Events Manager non installé ou inactif | Installer/activer Events Manager (voir [`plugins.md`](plugins.md#2-events-manager-calendrier)) — pas besoin de rollback |
| Footer sans newsletter | Brevo non installé ou formulaire d'ID 1 absent | Installer Brevo + créer le formulaire (voir [`plugins.md`](plugins.md#1-brevo-newsletter)) |
| Page Contact affiche `[contact-form-7 id="..."]` en texte brut | CF7 non installé ou ID du formulaire incorrect | Installer CF7 + vérifier l'ID |
| Menu invisible dans le header | Emplacement `primary` non assigné | Admin → Apparence → Menus → cocher « Menu Principal » |
| Carrousel témoignages vide | Aucun témoignage publié en CPT `kiyose_testimony` | Créer au moins 1 témoignage (voir [`content-migration.md`](content-migration.md#7-témoignages--migration-vers-le-cpt-kiyose_testimony)) |
| Images coupées ou floues sur les pages de service | Tailles `kiyose-hero` non générées sur les médias existants | Lancer Regenerate Thumbnails |
| Bouton « Voir le calendrier » absent sur une page de service | Page calendrier non configurée ou non publiée | Apparence → Personnaliser → Pages de service — boutons : choisir une page calendrier publiée |
| Le calendrier ne se filtre pas depuis une page de service | Page calendrier encore configurée avec un shortcode Events Manager brut | Remplacer le shortcode par `[kiyose_events_list]` sur la page Calendrier |

### Lire les logs PHP

Avant toute décision, vérifier les logs pour connaître la cause :

```bash
# Apache
tail -f /var/log/apache2/error.log

# nginx
tail -f /var/log/nginx/error.log

# WP_DEBUG (activer temporairement dans wp-config.php)
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
# Le log sera dans wp-content/debug.log
```

---

## Niveau 1 — Rollback via l'admin WordPress (< 1 min)

**Quand l'utiliser** : l'admin WordPress reste accessible (c'est le cas dans la majorité des situations, même si le front est cassé).

1. Se connecter sur `https://www.latelierkiyose.fr/wp-admin`
2. Apparence → Thèmes
3. Survoler l'ancien thème (celui qui était actif avant la migration)
4. Cliquer sur **Activer**
5. Rafraîchir la homepage et vérifier que le site s'affiche à nouveau

**Ce qui est préservé** : tous les contenus, pages, médias, témoignages créés. Rien n'est perdu. La nouvelle activation du thème Kiyose pourra être retentée plus tard une fois le problème corrigé.

---

## Niveau 2 — Rollback via SFTP (2 min)

**Quand l'utiliser** : l'admin WordPress est inaccessible (écran blanc ou erreur 500 partout).

1. Se connecter en SFTP ou SSH au serveur
2. Aller dans `wp-content/themes/`
3. Renommer le dossier du nouveau thème :
   ```bash
   mv latelierkiyose latelierkiyose-broken
   ```
4. WordPress bascule automatiquement sur le thème par défaut (Twenty Twenty-Four ou équivalent) au prochain chargement.
5. Se reconnecter à l'admin (qui doit redevenir accessible)
6. Apparence → Thèmes → activer l'ancien thème du client si toujours présent
7. Analyser `latelierkiyose-broken/` hors production pour identifier le problème (probablement une erreur PHP, un fichier manquant, ou un conflit avec un plugin)

**Supprimer le dossier `latelierkiyose-broken/`** une fois l'incident analysé, pour éviter la confusion lors d'une nouvelle tentative de déploiement.

---

## Niveau 3 — Restauration complète (30 min à 2 h selon taille)

**Quand l'utiliser** : corruption de la base de données, contenu perdu, ou situation non résolue par les niveaux 1-2.

### Via UpdraftPlus (si utilisé pour la sauvegarde)

1. Se connecter à l'admin WordPress
2. Réglages → Sauvegardes UpdraftPlus → onglet « Sauvegardes existantes »
3. Identifier la sauvegarde datée d'avant la migration
4. Cliquer **Restaurer**
5. Cocher **Base de données** et **Fichiers** (plugins, thèmes, uploads)
6. Cliquer **Restaurer** et attendre la fin (peut prendre 10+ min)
7. Se reconnecter et vérifier l'état du site

### Via phpMyAdmin + SFTP (restauration manuelle)

Si UpdraftPlus indisponible mais que l'étape 1.1 du README a produit un dump SQL et un ZIP de `wp-content/` :

1. **Base de données** :
   - phpMyAdmin → sélectionner la base WP → onglet « Importer »
   - Uploader le `.sql` de sauvegarde
   - ⚠️ Cela écrase la base actuelle
2. **Fichiers** :
   - Uploader et dézipper l'archive `wp-content/` de sauvegarde sur le serveur (en remplaçant le répertoire actuel)
   - Vérifier les permissions (`chown -R www-data:www-data wp-content/`, chmod selon la configuration du serveur)
3. Vider le cache (si plugin de cache actif) et rafraîchir le site

---

## Après un rollback — checklist

Une fois le site revenu à l'ancienne version :

- [ ] Site public accessible et affichant le contenu normal
- [ ] Admin accessible
- [ ] Formulaires (contact, newsletter) fonctionnels
- [ ] Aucune erreur dans les logs PHP
- [ ] Communiquer l'incident au commanditaire
- [ ] Analyser la cause racine **avant** de retenter la migration
- [ ] Documenter la cause + la correction dans le ticket / commit associé
- [ ] Ajouter un cas de test à la checklist de validation si pertinent

---

## Prévention — ce qui évite 90 % des rollbacks

- Tester la migration **d'abord sur un environnement de staging** (clone de la prod avec les mêmes plugins)
- Vérifier que les trois plugins requis sont installés et configurés **avant** l'activation du thème (voir [`plugins.md`](plugins.md))
- Ne pas activer le thème en pleine journée — prévoir une fenêtre de faible trafic
- Garder la sauvegarde de la veille accessible à portée de main pendant toute la fenêtre de migration
