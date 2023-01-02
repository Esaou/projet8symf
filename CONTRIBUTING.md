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
Open a new pull request and link it to the corresponding issue.

## Standards to apply
* [PSR](https://www.php-fig.org/psr/)
* [Symfony best pratices](https://symfony.com/doc/current/index.html) for current version
