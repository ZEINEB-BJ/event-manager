# 🎉 Event Manager

Une application web simple de gestion d’événements, développée avec PHP, MySQL et Bootstrap 5.  
Elle permet d’ajouter, afficher, modifier, supprimer des événements, commenter, et bien plus !

---

## 🌐 Fonctionnalités

- ✅ Ajouter un événement (avec image)
- ✅ Modifier / Supprimer un événement (admin uniquement)
- ✅ Rechercher dynamiquement par **titre** ou **lieu**
- ✅ Pagination automatique (6 événements par page)
- ✅ Ajout de **commentaires** publics par événement
- ✅ Interface claire / sombre (toggle)
- ✅ Responsive design avec **Bootstrap 5**
- ✅ Connexion admin simple
- ✅ Logo + navigation dynamique
- ✅ Alertes automatiques (succès, suppression, modification)
- ✅ Carrousel des 3 derniers événements

---

## 🛠️ Technologies utilisées

- ✅ HTML5 / CSS3 / Bootstrap 5
- ✅ PHP 8+
- ✅ MySQL
- ✅ JavaScript (pour alertes et thème dark/light)

---

## 📸 Aperçu

### Page d'accueil (publique)

![Accueil](assets/capture-accueil.png)

### Ajout d'un événement (admin)

![Ajout](assets/capture-ajout.png)

### Commentaires sur un événement

![Commentaires](assets/capture-commentaires.png)

## 📁 Structure du projet

```bash
event-manager/
├── index.php            # Page d'accueil - liste et recherche des événements
├── ajouter.php          # Formulaire d'ajout
├── modifier.php         # Formulaire de modification
├── supprimer.php        # Suppression d'un événement
├── includes/
│   └── db.php           # Fichier de connexion à la base de données
├── assets/
│   ├── css/             # Fichiers CSS personnalisés
│   └── js/              # Scripts JS
└── README.md

👩‍💻 Auteur
Zeineb Ben Jeddou

GitHub : @ZEINEB-BJ


```
