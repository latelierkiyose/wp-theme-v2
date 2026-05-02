# 04 · Les shortcodes

Un *shortcode* est un **code court entre crochets** que tu colles dans l'éditeur et qui génère un contenu riche (témoignages, sommaire, encadré…).

Le thème fournit **quatre shortcodes** :

- [`[kiyose_testimonials]`](#kiyose_testimonials--témoignages-en-grille-ou-carrousel) — une grille ou un carrousel de témoignages
- [`[kiyose_signets]`](#kiyose_signets--sommaire-de-navigation-dans-la-page) — un sommaire de navigation dans la page
- [`[kiyose_callout]`](#kiyose_callout--encadré-de-mise-en-évidence) — un encadré pour mettre en avant une information
- [`[kiyose_cta]`](#kiyose_cta--bouton-dappel-à-laction) — un bouton doré centré avec texte et lien personnalisés

À la fin de cette page, une mention des [shortcodes fournis par les plugins](#shortcodes-fournis-par-les-plugins) (Contact Form 7, Events Manager, Brevo).

## Comment coller un shortcode

1. Dans l'éditeur Gutenberg, clique sur le bouton **+** pour ajouter un bloc.
2. Tape `shortcode` dans la recherche, choisis le bloc **Code court**.
3. Colle le shortcode (avec les crochets `[` et `]`) dans le bloc.
4. Enregistre ou publie la page pour voir le rendu.

> **Alternative :** tu peux aussi coller un shortcode directement dans un bloc **Paragraphe**, mais le bloc **Code court** est plus clair.

---

## `[kiyose_testimonials]` — témoignages en grille ou carrousel

### Exemple

```
[kiyose_testimonials display="carousel" limit="5"]
```

Affiche un carrousel de 5 témoignages tirés au hasard.

### À quoi ça sert

Récupère automatiquement les témoignages saisis dans le menu **Témoignages** de l'admin et les affiche soit en grille, soit en carrousel (défilement automatique). Voir [Gérer les témoignages](05-temoignages.md) pour saisir les témoignages.

### Paramètres

| Paramètre | Valeurs possibles | Par défaut | Rôle |
|---|---|---|---|
| `display` | `grid` ou `carousel` | `grid` | Mode d'affichage : grille fixe ou carrousel défilant. |
| `limit` | Un nombre (ou `-1` pour tous) | `-1` | Nombre maximum de témoignages à afficher. |
| `context` | `individual`, `enterprise`, `structure` ou vide | (vide) | Filtre par type de témoignage. Vide = tous types mélangés. |
| `columns` | `1`, `2`, `3` ou `4` | `2` | Nombre de colonnes (grille uniquement ; ignoré en carrousel). |

### Exemples courants

**Grille de 2 colonnes, tous les témoignages particuliers :**

```
[kiyose_testimonials display="grid" columns="2" context="individual"]
```

**Carrousel de 10 témoignages, tous contextes confondus :**

```
[kiyose_testimonials display="carousel" limit="10"]
```

**Grille de 3 colonnes, 6 témoignages entreprise maximum :**

```
[kiyose_testimonials display="grid" columns="3" limit="6" context="enterprise"]
```

**Aucun témoignage en base ?** Le shortcode affiche : « Aucun témoignage disponible pour le moment. »

---

## `[kiyose_signets]` — sommaire de navigation dans la page

### Exemple

```
[kiyose_signets][/kiyose_signets]
```

Génère automatiquement un sommaire à partir des titres **H2** de la page.

### À quoi ça sert

Affiche une barre de liens vers les différentes sections de la page (comme une table des matières). Pratique en haut d'une page longue (service, politique de confidentialité…) pour permettre au visiteur de sauter directement à la section qui l'intéresse.

### Paramètres

**Aucun paramètre.** Le comportement dépend du contenu entre les balises d'ouverture et de fermeture.

### Deux modes d'utilisation

**Mode 1 — Sommaire automatique** (le plus simple) : laisse le shortcode vide. Tous les titres **H2** de la page deviennent des entrées du sommaire.

```
[kiyose_signets][/kiyose_signets]
```

**Mode 2 — Sommaire manuel** : écris les entrées une par ligne, au format `Texte du lien | #ancre`.

```
[kiyose_signets]
Bienvenue | #intro
Matériel requis | #materiel
Étapes | #etapes
Questions fréquentes | #faq
[/kiyose_signets]
```

Pour que les liens fonctionnent en mode manuel, les sections cibles doivent avoir un **identifiant HTML** correspondant. Tu peux le définir sur un bloc **Titre** via **Avancé > Ancre HTML** (par exemple `intro`, sans le `#`).

### Exemples courants

**Placer un sommaire en haut d'une page de service :**

1. Commence ta page par quelques paragraphes d'introduction.
2. Insère `[kiyose_signets][/kiyose_signets]`.
3. Ensuite, rédige tes sections avec des titres de niveau **Titre 2** dans Gutenberg.

Voir la recette : [Ajouter un sommaire automatique](recettes/ajouter-un-sommaire-automatique.md).

---

## `[kiyose_callout]` — encadré de mise en évidence

### Exemple

```
[kiyose_callout titre="Bon à savoir"]
Les ateliers se déroulent à Brux, en Vienne. Prévois des vêtements confortables.
[/kiyose_callout]
```

Affiche un encadré crème doré avec une bordure gauche dorée, pour attirer l'œil sur une information importante.

### À quoi ça sert

Mettre en valeur une information cruciale : tarif, horaire, matériel requis, mise en garde, astuce… L'encadré se distingue visuellement du reste du contenu sans être agressif.

### Paramètres

| Paramètre | Valeurs possibles | Par défaut | Rôle |
|---|---|---|---|
| `titre` | Texte libre | (vide) | Titre optionnel affiché en gras au-dessus du contenu. |

### Contenu

Le texte entre `[kiyose_callout]` et `[/kiyose_callout]` est **obligatoire**. Tu peux y mettre plusieurs paragraphes, des liens, des listes, d'autres shortcodes.

### Mettre en forme le texte

Dans le contenu de l'encadré, utilise du HTML simple :

| Ce que tu veux faire | Exemple à copier |
|---|---|
| Mettre un passage en gras | `<strong>Information importante</strong>` |
| Mettre un passage en italique | `<em>à prévoir</em>` |
| Ajouter un lien | `<a href="/contact/">contacte-moi</a>` |
| Ajouter une liste à puces | `<ul><li>Premier point</li><li>Deuxième point</li></ul>` |
| Ajouter une liste numérotée | `<ol><li>Première étape</li><li>Deuxième étape</li></ol>` |

Le titre `titre="..."` reste du texte simple. Si tu veux du gras, un lien ou une liste, mets-les dans le contenu entre les deux balises du shortcode.

### Exemples courants

**Avec titre :**

```
[kiyose_callout titre="Tarif"]
80 € par séance individuelle. Règlement par chèque ou virement.
[/kiyose_callout]
```

**Sans titre :**

```
[kiyose_callout]
Les places sont limitées à 8 personnes par atelier. Pense à réserver.
[/kiyose_callout]
```

**Avec plusieurs paragraphes et un lien :**

```
[kiyose_callout titre="Pour aller plus loin"]
Tu trouveras toutes les dates à venir sur la <a href="/calendrier/">page du calendrier</a>.

Si tu préfères un échange en amont, <a href="/contact/">contacte-moi</a>.
[/kiyose_callout]
```

**Avec une liste :**

```
[kiyose_callout titre="À prévoir"]
<strong>Pour l'atelier, pense à apporter :</strong>

<ul>
<li>des vêtements confortables ;</li>
<li>une petite bouteille d'eau ;</li>
<li>de quoi prendre des notes si tu le souhaites.</li>
</ul>
[/kiyose_callout]
```

Voir la recette : [Mettre en avant une information](recettes/mettre-en-avant-une-information.md).

---

## `[kiyose_cta]` — bouton d'appel à l'action

### Exemple

```
[kiyose_cta texte="Réserver votre appel découverte" lien="/contact/"]
```

Affiche un bouton doré centré, avec le même rendu que les appels à l'action de la page d'accueil.

### À quoi ça sert

Ajouter un appel à l'action clair dans une page ou un article : réserver un appel découverte, aller vers la page contact, consulter le calendrier, etc.

Le bouton est **toujours centré**. Tu n'as pas besoin d'ajouter de classe CSS ou de régler l'alignement dans Gutenberg.

### Paramètres

| Paramètre | Valeur attendue | Obligatoire | Rôle |
|---|---|---:|---|
| `texte` | Texte court | oui | Libellé affiché dans le bouton. |
| `lien` | URL relative ou complète | oui | Destination du bouton. |

Si `texte` ou `lien` est vide, le bouton ne s'affiche pas.

### Exemples courants

**Vers la page contact :**

```
[kiyose_cta texte="Réserver votre appel découverte" lien="/contact/"]
```

**Vers le calendrier :**

```
[kiyose_cta texte="Voir les prochaines dates" lien="/calendrier-tarifs/"]
```

**Vers une page de service :**

```
[kiyose_cta texte="Découvrir l'art-thérapie" lien="/art-therapie/"]
```

Voir la recette : [Ajouter un appel à l'action](recettes/ajouter-un-appel-a-l-action.md).

---

## Shortcodes fournis par les plugins

Le site utilise trois plugins qui fournissent leurs propres shortcodes. Le thème ne les documente pas en détail — consulte la documentation officielle du plugin concerné.

### Contact Form 7 (formulaires)
- Shortcode typique : `[contact-form-7 id="123" title="Contact"]`
- Le formulaire de contact principal est déjà placé dans le template **Page de contact**. Tu n'as rien à faire sauf si tu veux ajouter un formulaire sur une autre page.
- Créer un formulaire : menu **Contact** dans l'admin.
- Documentation : <https://contactform7.com/docs/>

### Events Manager (événements)
- Shortcode typique : `[events_list limit="5"]`
- Le plugin affiche automatiquement les événements sur la page **Calendrier et tarifs**.
- Créer un événement : menu **Événements**.
- Documentation : <https://wp-events-plugin.com/documentation/>

### Brevo (newsletter)
- Shortcode typique : `[sibwp_form id="1"]`
- Le formulaire de newsletter est intégré dans le pied de page et sur la page d'accueil.
- Configurer : menu **Brevo** ou **Sendinblue** (anciennement).
- Documentation : <https://help.brevo.com/>

## Pour aller plus loin

- [Gérer les témoignages](05-temoignages.md)
- [Les recettes](recettes/) — cas d'usage concrets
- [Glossaire](glossaire.md)
