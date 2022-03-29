# Soir'EISTI

Soir'EISTI est un projet de site web pour une association factice.

Les utilisateurs peuvent :
 - consulter les événements prévus
 - s'y inscrire (avec des places sous forme de QR code)
 - voir les photos lorsque ceux-ci sont terminés

Il y aussi : 
 - un moteur de recherche
 - une boutique
 - des sondages
 - un thème sombre
 
 Et pour les admins :
 - un panneau de gestion des évents, des utilisateurs, des sondages et de la boutique
 - un aperçu de la trésorie avec des graphiques
 - un système de recrutement par candidatures.

## Lancer en local

Il est possible de lancer le projet en local.
Pour cela il faut faudra PHP et mysql.
 - cloner le projet
 - créer un fichier `credentials.php` dans le dossier api contenant identifiants de la base de données, sous cette forme :
```php
<?php
define('DATABASE_ADDRESS', 'localhost');
define('DATABASE_USERNAME', 'yourUsername');
define('DATABASE_PASSWORD', 'yourPassword');
define('DATABASE_NAME', 'soireisti');
?>
```
 - exécuter dans la base de données le script SQL [init.sql](init.sql)
 - lancer le serveur php

N'oubliez pas de changer la valeur de la colonne `admin` à 1 des comptes des administrateurs dans la table `utilisateurs` de la base de données.