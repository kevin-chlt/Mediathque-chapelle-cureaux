# [Evaluation Studi] Plateforme en ligne de la médiathèque La Chapelle-Cureaux

## 1. Contexte de l'évaluation:
Lors du premier confinement, les bibliothèques et centres de documentation, au même titre que d'autres établissements recevant du public, ont dû fermer leurs portes.  
La médiathèque de La Chapelle-Curreaux souhaite en profiter pour développer en interne une solution d’emprunt en ligne.

Fonctionnalitées désirées:  
- Création de compte 
- Accès au site par authentification utilisateur
- Affichage du catalogue de livre avec possibilité de filtrer les livres et 
de les rechercher grace à une barre de recherche
- Formulaire d'enregistrement d'un nouveau livre
- Système de réservation/emprunt de livre

## 2. Environnement technique

- Front: HTLM 5 / CSS 3 & Framework MaterializeCSS  / JS Vanilla
- Back: PHP 7.4 à 8 / Symfony 5.3.9 / BDD relationnelle MySQL (SGBDR: Maria DB)
- Serveur: Local: Symfony Local Web Server / Prod: HEROKU


## Procédure de mise en place en local

- Cloner le fichier sur votre ordinateur avec  
  `git clone https://github.com/kevin-chlt/Mediathque-chapelle-cureaux.git`
- Installer les dépendances de l'application avec  
  `composer install`
- Entrer les paramètres de votre base de données dans un fichier situé à la racine du projet que vous nommerez
`.env.local.php` 
- Créer votre BDD avec `php bin/console doctrine:database:create`  
- Créer vos tables ensuite avec `php bin/console doctrine:migrations:migrate`
- Pour avoir un jeu de données factice, vous pouvez importer le fichier `import.sql` dans votdre BDD.

Vous pouvez désormais naviguer sur le site.