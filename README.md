# Gestion des Salariés

Une application web simple et efficace pour la gestion des informations des salariés, incluant leurs salaires, dates de naissance et d'embauche. Ce projet offre des fonctionnalités de consultation, d'ajout, de modification et de suppression, ainsi que des statistiques récapitulatives pour une vue d'ensemble rapide. L'application intègre un système d'authentification robuste et une gestion des droits d'accès pour sécuriser les données.

## Fonctionnalités

*   **Gestion Complète des Salariés (CRUD):** Ajoutez, consultez, modifiez et supprimez facilement les fiches de vos employés.
*   **Authentification Sécurisée:** Un système de connexion avec gestion des sessions assure que seules les personnes autorisées accèdent à l'application.
*   **Gestion des Droits d'Accès:** Différenciez les accès entre utilisateurs standards et administrateurs pour une meilleure sécurité et organisation.
*   **Statistiques Détaillées:** Obtenez des aperçus rapides grâce à des statistiques clés comme le nombre total de salariés, le salaire moyen, les salaires minimum/maximum, et la répartition par service.
*   **Recherche et Tri Avancés:** Trouvez rapidement les informations dont vous avez besoin grâce à des options de recherche et de tri flexibles sur la liste des salariés.
*   **Édition en Ligne Intuitive:** Modifiez les informations des salariés directement depuis la liste, pour une expérience utilisateur fluide et rapide.
*   **Sélecteur de Date Moderne:** Intégration de Flatpickr pour une saisie de date ergonomique et sans erreur.
*   **Validation de Formulaire Visuelle:** Des indicateurs clairs (vert/rouge) et des messages d'erreur guident l'utilisateur lors de la saisie des données, améliorant l'expérience et la qualité des données.
*   **Historique des Modifications:** Suivez les changements apportés aux données des salariés (via la page d'historique).

## Technologies Utilisées

*   **Backend:** PHP
*   **Base de données:** MySQL / MariaDB (avec PDO pour les interactions)
*   **Frontend:** HTML5, CSS3, JavaScript
*   **Framework CSS:** Bootstrap 5.3
*   **Bibliothèque JavaScript:** Flatpickr (pour les sélecteurs de date)

## Configuration

*   **`database.php`:** C'est le fichier clé pour la connexion à votre base de données. Assurez-vous que les identifiants sont corrects.
*   **`session.php`:** Ce fichier gère les sessions utilisateur. Aucune modification n'est généralement nécessaire, mais il est bon de savoir où il se trouve.

## Structure du Projet

*   `addSalaries.php`: Formulaire pour ajouter un nouveau salarié.
*   `administration.php`: Interface d'administration pour la gestion des utilisateurs ou d'autres paramètres (accès restreint).
*   `database.php`: Gère la connexion à la base de données.
*   `deleteSalaries.php`: Script pour la suppression des enregistrements de salariés.
*   `footer.html`: Contient le pied de page commun à l'application.
*   `functions.php`: Regroupe les fonctions PHP réutilisables (ex: récupération de données, calculs).
*   `header.php`: Contient l'en-tête de la page, la navigation et les inclusions CSS/JS globales.
*   `historique.php`: Affiche l'historique des actions ou modifications.
*   `listeSalaries.php`: La page principale affichant la liste des salariés avec les options de recherche, tri et édition.
*   `login.php`: Page de connexion des utilisateurs.
*   `logout.php`: Script pour déconnecter un utilisateur.
*   `main.js`: Fichier JavaScript pour les interactions côté client (recherche, édition en ligne, etc.).
*   `session.php`: Initialise et gère les sessions utilisateur.
*   `updateSalaryField.php`: Gère les requêtes AJAX pour la mise à jour des champs individuels des salariés.
*   `salaries.sql`, `users.sql`, `logs.sql`: Fichiers de schéma et de données pour la base de données.
*   `styles.css`: Fichier CSS pour les styles personnalisés de l'application.
