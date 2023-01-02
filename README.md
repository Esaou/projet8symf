ToDo & Co
========
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/37629424fce44d9986ca29d8610cdead)](https://www.codacy.com/gh/Esaou/projet8symf/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Esaou/projet8symf&amp;utm_campaign=Badge_Grade)

## Installation du projet

### Prérequis

- Installer un serveur web (ex: WAMP).
- Installer composer.
- Installer PHP 8.
- Installer symfony CLI.

### Installation

Cloner le projet.
```
git clone https://github.com/Esaou/projet8symf.git
```
Installer les dépendances.
```
composer install
```
Créer la base de données.
```
php bin/console d:d:c
php bin/console d:d:c --env=test # pour la base de données de test
```
Construire le schéma de la base de données.
```
php bin/console doctrine:migrations:migrate
```
Générer le jeu de données.
```
php bin/console d:f:l
php bin/console d:f:l --env=test # pour la base de données de test
```
Lancer le serveur.
```
symfony server:start
```