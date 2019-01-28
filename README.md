#  Famiree is a PHP Family Tree

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Build Status](https://img.shields.io/travis/malamalca/famiree/master.svg?style=flat-square)](https://travis-ci.org/malamalca/famiree)
[![Total Downloads](https://img.shields.io/packagist/dt/malamalca/famiree.svg?style=flat-square)](https://packagist.org/packages/malamaca/famiree)

Simple open source PHP family tree web application built with latest technologies.

The source code can be found here: [malamalca/famiree](https://github.com/malamalca/famiree).

![alt text](https://github.com/malamalca/famiree/raw/master/example.png)

## Installing Famiree via Composer

1. Download [Composer](https://getcomposer.org/doc/00-intro.md) or update `composer self-update`.
2. Run `php composer.phar create-project malamalca/famiree --no-dev`.

If Composer is installed globally, run

```bash
composer create-project malamalca/famiree --no-dev
```

## Installing Famiree from archive
1. Download [Latest Famiree release](https://github.com/malamalca/famiree/releases/latest) and extract it to your webroot folder.
2. Copy  config/app.default.php to config/app.php and open the file with your preferred text editor
3. Find and replace following settings in your config/app.php file:
    * `__SALT__` with a random string (eg `3498klfsjo093ljk42389s`)
    * `__DBHOST__` with mysql host (eg `localhost`)
    * `__DBUSER__` with mysql user (eg `famiree_www`)
    * `__DBPASS__` with mysql password (eg `mysecretpassword`)
    * `__DATABASE__` with mysql database name (eg `famiree`)
4. Import schema/famiree.sql file into your mysql database (you should create it first and set up db permissions) via phpMyAdmin or mysql command line interface (eg `mysql -u username -p database_name < famiree.sql`).
5. Set up write permission for following folders and their subfolders `logs`, `tmp`, `uploads`, `webroot/img/thumbs`.

## Running

You can now either use your machine's webserver to view the default home page, or start
up the built-in webserver with:

```bash
bin/cake server -p 8765
```

Then visit `http://localhost:8765` to see the welcome page.

## Configuration

Read and edit `config/app.php` and setup the `'Datasources'` and any other
configuration relevant for your application.

