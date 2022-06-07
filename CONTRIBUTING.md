# Contributing guide

## Getting started

### Install the project

Follow these [instructions](./README.md) to install the project.

### Issues

#### Solve an issue

- Check if an [existing issues](https://github.com/ashk74/p8_todolist/issues) interests you. If you find an issue to work on, you are welcome to open a PR with a fix.

#### Create a new issue

- If you detect a problem, search if an [issue already exists](https://github.com/ashk74/p8_todolist/issues).
- If a related issue doesn't exist, you can open a new issue using a relevant [issue form](https://github.com/ashk74/p8_todolist/issues).

### Create a new branch

> Must respect the [GitFlow](https://www.google.com/search?q=gitflow+workflow) workflow

- Must be created from dev branch
    - feature : create a new feature
    - release : prepares a new version for production
- Must be created from main branch
    - hotfix : fix a bug in production

Example : feature/add-task-deadline

### Commits

Commits must respect the following format : [TYPE] concerned entity (#issue) - Description

```sh
# Examples
git commit -m "[FEATURE] Task (#1) - Add task deadline"
git commit -m "[HOTFIX] User (#1) - Change api url"
```

### Test

Write your own test with PHPUnit if needed.

```sh
# Run PHPUnit tests to make sure nothing is broken
php bin/phpunit
```

```sh
# Update code coverage
bin/phpunit --coverage-html public/test-coverage
```

### Pull Request

When you're finished with the changes, create a pull request

### PR is merged

Congratulations and thanks you for your contribution :sparkles:.
