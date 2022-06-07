# Project 8 : ToDo List (Application developer - PHP / Symfony - OpenClassrooms)

## Code quality

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/1d0af95c1dca45d09f73905d62f0fc33)](https://www.codacy.com/gh/ashk74/P8_Todolist/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=ashk74/P8_Todolist&amp;utm_campaign=Badge_Grade)
[![Maintainability](https://api.codeclimate.com/v1/badges/7c8e2e8fee86a10883ce/maintainability)](https://codeclimate.com/github/ashk74/P8_Todolist/maintainability)

## Tools used

[![Symfony 5.4](https://img.shields.io/badge/symfony_5.4-%23000000.svg?style=for-the-badge&logo=symfony&logoColor=white)](https://symfony.com/doc/5.4/index.html)
[![PHP 8.1.6](https://img.shields.io/badge/php_8.1.6-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL 5.7.34](https://img.shields.io/badge/mysql_5.7.34-%234479A1.svg?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Composer 2.2.6](https://img.shields.io/badge/composer_2.2.6-%23885630.svg?style=for-the-badge&logo=composer&logoColor=white)]([https://www.mysql.com/](https://getcomposer.org/download/))
[![Symfony CLI 5.4.8](https://img.shields.io/badge/cli_5.4.8-%23000000.svg?style=for-the-badge&logo=symfony&logoColor=white)](https://symfony.com/download#step-1-install-symfony-cli)
[![Bootstrap 3.3.7](https://img.shields.io/badge/bootstrap_3.3.7-%237952B3.svg?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com/docs/3.3/getting-started/)
[![PHPUnit 9.5.20](https://img.shields.io/badge/phpunit_9.5.20-%231890FF.svg?style=for-the-badge&logo=phpunit&logoColor=white)](https://phpunit.de/)

## Technical requirements
  - PHP 7.2.5 or higher
  - PHP extensions : Ctype, iconv, JSON, PCRE, Session, SimpleXML and Tokenizer

## Installation

### Download or clone the project
- Download zip files or clone the project repository with github - [GitHub documentation](https://docs.github.com/en/github/creating-cloning-and-archiving-repositories/cloning-a-repository)

### Edit .env file

```yaml
# SQL DBMS
DATABASE_URL="mysql://username:password@host:port/dbname"
```

### Set your PHP version

```sh
# List and select PHP version (minimum 7.2.5)
symfony local:php:list
echo 8.1.6 > .php-version
```

### Install required packages

```sh
composer install
```

### Create database and tables
```sh
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### Load fixtures
```sh
php bin/console doctrine:fixtures:load
```
### Tests
```sh
# Run tests
php bin/phpunit
```

Run the server and go to 127.0.0.1:8000/test-coverage to check the code coverage

## Contributing

Contributions, issues and feature requests are welcome.<br />
Feel free to check [issues page](https://github.com/ashk74/P8_Todolist/issues) if you want to contribute.<br />
[Contributing guide](./CONTRIBUTING.md).<br />
