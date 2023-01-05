ToDo & Co
========
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/37629424fce44d9986ca29d8610cdead)](https://www.codacy.com/gh/Esaou/projet8symf/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Esaou/projet8symf&amp;utm_campaign=Badge_Grade)

## Install project

### Requirements

- Install a web server (ex: WAMP).
- Install composer.
- Install PHP 8.
- Install symfony CLI.

### Installation

Clone the project.
```
git clone https://github.com/Esaou/projet8symf.git
```
install dependencies.
```
composer install
```
Create database.
```
php bin/console d:d:c
php bin/console d:d:c --env=test # pour la base de données de test
```
Create the database schema.
```
php bin/console doctrine:migrations:migrate
php bin/console doctrine:migrations:migrate --env=test
```
Generate the dataset .
```
php bin/console d:f:l
php bin/console d:f:l --env=test # pour la base de données de test
```
Start the server.
```
symfony server:start
```