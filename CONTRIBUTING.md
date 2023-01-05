# CONTRIBUTING DOCUMENT

## Install project
Use README.md to install the project.

## Issue
Create a new issue on the repository to describe what will be done. Add a label to this issue for a better comprehension.

## New branch
You have to develop your feature on a new branch :

```
git checkout -b feature/[newBranch]
```

The branch name has to be short and understandable.

## Tests
At the end of each development, you must launch automated tests :

```
php bin/phpunit --coverage-html coverage
```
If error occurs, fix the bug before pushing.

Total coverage must be up to 70%.

## Commit and push
Add and commit your code with an understandable message. For exemple :

```
git add .
git commit -m "authentication - update login form design"
```

and push :
```
git push origin [branchName]
```

## Pull Request
Open a new pull request with an understandable title and add a description. 

Link it to the corresponding issue.

## Standards to apply
### Phpstan 

Use phpstan inspection to check your code :

```
vendor/bin/phpstan analyse src
```

If errors occcur, correct them.

### PHP Standards Recommandation

Respect the PSR-0, PSR-1, PSR-2, PSR-4.

See documentation for more [PSR](https://www.php-fig.org/psr/)

### Symfony best practices

To have a good use of Symfony, check the documentation.

[Symfony best pratices](https://symfony.com/doc/current/index.html) for current version.
