# 07 · Formulaires et newsletter

Le site utilise deux plugins pour gérer les formulaires : **Contact Form 7** pour le formulaire de contact, **Brevo** pour la newsletter. Le thème les stylise pour qu'ils s'intègrent au design, mais leur fonctionnement reste celui des plugins.

## Contact Form 7 (formulaire de contact)

### Où se gère le formulaire

Menu **Contact** dans l'admin → tu vois la liste des formulaires. Le formulaire principal est déjà créé et placé dans la **Page de contact**.

### Modifier le formulaire de contact

1. Menu **Contact** → clique sur le formulaire existant.
2. Trois onglets :
   - **Formulaire** : les champs (nom, e-mail, message, etc.).
   - **E-mail** : à qui le message est envoyé, le contenu de l'e-mail.
   - **Messages** : les textes de confirmation ou d'erreur.
3. Enregistre après modification.

### Ajouter le formulaire sur une autre page

Copie le shortcode de la première colonne de la liste des formulaires. Il ressemble à :

```
[contact-form-7 id="123" title="Contact"]
```

Colle ce shortcode dans un bloc **Code court** de la page voulue.

### Documentation complète
📖 <https://contactform7.com/docs/>

## Brevo (newsletter)

### Où se gère la newsletter

Menu **Brevo** (ou **Sendinblue** selon la version) dans l'admin. La configuration (liste d'abonnés, paramètres SMTP, formulaires d'inscription) se fait dans ce menu.

### Le formulaire d'inscription sur le site

Le formulaire d'inscription à la newsletter est :
- **Intégré dans le pied de page** de toutes les pages.
- **Intégré sur la page d'accueil**.

Tu n'as **rien à faire** pour qu'il s'affiche — c'est déjà prêt.

### Ajouter le formulaire d'inscription sur une autre page

Colle ce shortcode dans un bloc **Code court** :

```
[sibwp_form id="1"]
```

(Remplace `1` par l'identifiant du formulaire que tu trouves dans le menu **Brevo** → **Formulaires**.)

### Consulter les abonnés

Les abonnés sont gérés dans l'interface Brevo en ligne (pas dans WordPress). Connecte-toi sur <https://app.brevo.com/> avec le compte du site.

### Documentation complète
📖 <https://help.brevo.com/>

## Pour aller plus loin

- [Choisir le template « Page de contact »](02-choisir-un-template.md)
- [Les shortcodes](04-shortcodes.md#shortcodes-fournis-par-les-plugins)
