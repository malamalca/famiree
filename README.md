#  Famiree is a PHP Family Tree

[![Build Status](https://img.shields.io/travis/malamalca/famiree/master.svg?style=flat-square)](https://travis-ci.org/malamalca/famiree)
[![Total Downloads](https://img.shields.io/packagist/dt/malamalca/famiree.svg?style=flat-square)](https://packagist.org/packages/malamaca/famiree)

Simple open source family tree web application [Famiree](https://famiree.org)

The source code can be found here: [cakephp/cakephp](https://github.com/malamalca/famiree).

## Installation

1. Download [Composer](https://getcomposer.org/doc/00-intro.md) or update `composer self-update`.
2. Run `php composer.phar create-project malamalca/famiree`.

If Composer is installed globally, run

```bash
composer create-project malamalca/famiree
```

You can now either use your machine's webserver to view the default home page, or start
up the built-in webserver with:

```bash
bin/cake server -p 8765
```

Then visit `http://localhost:8765` to see the welcome page.

## Configuration

Read and edit `config/app.php` and setup the `'Datasources'` and any other
configuration relevant for your application.

