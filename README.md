# php8-simple-hr-api
Simple Human Resources API using PHP8, PDO, MySQL with variant using Swoole

# Requirements

* [PHP 8.0](https://www.php.net/)
* [Composer](https://getcomposer.org/)
* [MySQL Server 5.7](https://dev.mysql.com/downloads/mysql/5.7.html)

On Mac:

```sh
brew install php
brew install mysql
brew services start mysql
```

## PHP libraries

```sh
composer i
```


## Swoole

See Swoole website: https://www.swoole.co.uk/docs/get-started/installation

On Mac:

```sh
pecl install swoole
# enable mysqlnd and json
```

## DATABASE

Connect to your database system and create a database. Do **not** use `root`. Create a new user with permissions only on the HR database. If the MySQL server is on the same machine as the HTTP server, allow only **local** connections. Stick to UTF-8 at all tiers of your solution.

```sql
CREATE DATABASE `hrdb`;
ALTER SCHEMA `hrdb` DEFAULT CHARACTER SET utf8  DEFAULT COLLATE utf8_general_ci;
CREATE USER 'hradmin'@'localhost' IDENTIFIED WITH mysql_native_password BY 'ent3r.A-S3cure,Pa55w0rD';
GRANT ALL ON hrdb.* TO 'hradmin'@'localhost';
```

Check SQL files in `db/` and execute them on your database. For more information check [README.md](tree/main/db)

See [entity-relationship-diagram.png](blob/main/entity-relationship-diagram.png)

Load `entity-relationship-diagram.mwb` using [MySQL Workbench](https://www.mysql.com/products/workbench/).

## CONFIGURE

Take a copy of `.env.sample` as `.env` and review settings.

```sh
cp .env.sample .env
```

## RUN

**Option 1**:

PHP's built-in [web server](https://www.php.net/manual/en/features.commandline.webserver.php)

This for quick development only; **not** for production purposes!

```sh
cd ./public
php -S localhost:9090 ./index.php

# turn off console messages
php -S localhost:9090 ./index.php 2>/dev/null
```

Option 1 is tested.


**Option 2**:

[Swoole](https://www.swoole.co.uk)

```sh
php ./swoole.php
```

Option 2 is also tested but not thoroughly.


**Option 3**:

[Nginx](https://nginx.org/) and [PHP-FPM](https://www.php.net/manual/en/install.fpm.php)

Ref: https://www.nginx.com/resources/wiki/start/topics/examples/phpfcgi/

Ref: https://www.php.net/manual/en/install.unix.nginx.php


## USAGE

Use [Postman](https://www.postman.com/) and import collection for some samples `hr-api.postman_collection.json`

General logic:

* The `src/Api.php` is quite generic it can be used by HTTP (REST or GraphQL), CLI, WebSockets and can be included in any framework.

* All we need is to prepare `$input` for its `handle()` function, run it and get `$output`

* This is faster than matching URLs to controllers via expensive regular expressions.

* Do not use cookies, PHP sessions, etc.

* We can do formatting inputs/outputs outside API (decoding/encoding JSON etc.).

* It is like a proxy/router, instead of using a URL path and/or HTTP methods it relies on a basic object structure: see class in `src/ApiInput.php` e.g. string property `action` identifies the intend like `department_search`; that is it.

* Under the hood, it uses powerful native `PDO` library.

* There is no ORM, but raw power of SQL!

* So, you can simple do HTTP POST `http://localhost:9090/`

## FEATURES

* Basic Authentication (API Key)
* Supports MySQL
* Basic MySQL Query Builder
* 2 Basic Models with DTOs to perform CRUD + Search operations
* 2 Basic Reports

## TODO

* Add Authentication
* Add Authorization
* Add Validation (add advanced input validation)
* Improve Query Builder with more options like filtering, sorting, pagination, etc.
* Add Support for SQLite and PostgreSQL
* Add Dependency Injection
* Add Test Suite
* Containerize the solution (add Docker file)
