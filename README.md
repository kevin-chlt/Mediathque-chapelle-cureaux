# Médiathèque La Chapelle-Cureaux

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