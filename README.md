Doctrine Extensions
===================
[![Latest Stable Version](https://poser.pugx.org/core23/doctrine-extensions/v/stable)](https://packagist.org/packages/core23/doctrine-extensions)
[![Latest Unstable Version](https://poser.pugx.org/core23/doctrine-extensions/v/unstable)](https://packagist.org/packages/core23/doctrine-extensions)
[![License](https://poser.pugx.org/core23/doctrine-extensions/license)](LICENSE.md)

[![Total Downloads](https://poser.pugx.org/core23/doctrine-extensions/downloads)](https://packagist.org/packages/core23/doctrine-extensions)
[![Monthly Downloads](https://poser.pugx.org/core23/doctrine-extensions/d/monthly)](https://packagist.org/packages/core23/doctrine-extensions)
[![Daily Downloads](https://poser.pugx.org/core23/doctrine-extensions/d/daily)](https://packagist.org/packages/core23/doctrine-extensions)

[![Build Status](https://travis-ci.org/core23/doctrine-extensions.svg)](http://travis-ci.org/core23/doctrine-extensions)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/core23/doctrine-extensions/badges/quality-score.png)](https://scrutinizer-ci.com/g/core23/doctrine-extensions/)
[![Code Climate](https://codeclimate.com/github/core23/doctrine-extensions/badges/gpa.svg)](https://codeclimate.com/github/core23/doctrine-extensions)
[![Coverage Status](https://coveralls.io/repos/core23/doctrine-extensions/badge.svg)](https://coveralls.io/r/core23/doctrine-extensions)

This library provides adds some useful doctrine hooks.

## Installation

Open a command console, enter your project directory and execute the following command to download the latest stable version of this library:

```
composer require core23/doctrine-extensions
```

## Usage

### Confirmable entities

If you need entities that needs to be confirmed, just implement the `Core23\Doctrine\Model\ConfirmableInterface`
in your entity class.

If you don't need the symfony framework, you need to register the `Core23\Doctrine\EventListener\ORM\ConfirmableListener`.

### Deleteable entities

If you need entities that should be soft deleted, just implement the `Core23\Doctrine\Model\DeletableInterface`
in your entity class.

If you don't need the symfony framework, you need to register the `Core23\Doctrine\EventListener\ORM\DeletableListener`.

### Lifecyle aware enties

If you need lifecyle information (creation / update date), just implement the `Core23\Doctrine\Model\LifecycleDateTimeInterface`
in your entity class.

If you don't need the symfony framework, you need to register the `Core23\Doctrine\EventListener\ORM\LifecycleDateListener`.

### Position aware entities

If you need sortable entities, just implement the `Core23\Doctrine\Model\PositionAwareInterface`
in your entity class.

If you don't need the symfony framework, you need to register the `Core23\Doctrine\EventListener\ORM\SortableListener`.

### Unique active entities

If you need entities that should only have one active state, just implement the `Core23\Doctrine\Model\UniqueActiveInterface`
in your entity class.

If you don't need the symfony framework, you need to register the `Core23\Doctrine\EventListener\ORM\UniqueActiveListener`.

### Table prefix

If you need a prefix for all of you application tables and sequences, you could use the  `TablePrefixEventListener`. 
If the table name does already start with the defined prefix, it will be ignored.

If you don't need the symfony framework, you need to register the `Core23\Doctrine\EventListener\ORM\TablePrefixEventListener`.

## Symfony usage

If you want to use this library inside symfony, you can use a bridge.

### Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Core23\Doctrine\Bridge\Symfony\Bundle\Core23DoctrineBundle::class => ['all' => true],
];
```


### Configure the Bundle

Create a configuration file called `core23_doctrine.yaml`:

```yaml
# config/packages/core23_doctrine.yaml

core23_doctrine:
    table:
        prefix: 'acme_'
```


## License

This library is under the [MIT license](LICENSE.md).
