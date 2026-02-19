🍳 ECF Vite & Gourmand - Plateforme de Commande de Repas
Bienvenue sur le dépôt officiel du projet Vite & Gourmand, développé pour Julie et José dans le cadre du titre professionnel Développeur Web et Web Mobile.


📋 Présentation du Projet
Cette application permet aux clients de consulter une carte dynamique, de passer commande avec calcul automatique des frais de livraison et de gérer leur profil. Un espace administration complet permet aux employés et à l'administrateur de gérer les menus, les commandes et la modération des avis.


🚀 Guide de Déploiement Local
1. Prérequis
Serveur local (WAMP, XAMPP ou MAMP).

PHP 8.x et MySQL/MariaDB.


2. Installation
Clonage du dépôt :

Bash
git clone [LIEN_DE_TON_REPO]
Base de données :

Créer une base de données nommée vite_et_gourmand.

Importer le fichier database/structure_et_donnees.sql présent à la racine pour installer les tables et les jeux de données de test.

Configuration :

Modifier le fichier db.php pour y insérer vos identifiants de connexion locale (Host, User, Password).

Lancement :

Placer le projet dans votre dossier www ou htdocs et accéder via localhost.

🛡️ Sécurité & Choix Techniques

Backend : PHP avec l'interface PDO pour une protection native contre les injections SQL.



RGPD : Hachage des mots de passe via l'algorithme BCRYPT et système de suppression de compte conforme au droit à l'oubli.



XSS : Échappement systématique des données affichées via htmlspecialchars().


NoSQL : Export JSON pour l'analyse des statistiques de vente demandée par la direction.


🛠️ Organisation du Dépôt (GitFlow)
Le projet respecte les bonnes pratiques Git exigées:

main : Branche de production.

develop : Branche d'intégration des fonctionnalités testées.

feature/ : Branches dédiées au développement de modules spécifiques (ex: feature/panier, feature/admin).

👤 Identifiants de Test

utilisateur : Vous pouvez vous creé un compte

Employez : e-mail : julie@vite-gourmand.fr / mdp : vitegourmand

Administrateur : jose@vite-gourmand.fr / vitegourmand2


Client : client@test.fr / Client7624!.
