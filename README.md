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

[![Donate to this project using Flattr](https://img.shields.io/badge/flattr-donate-yellow.svg)](https://flattr.com/profile/core23)
[![Donate to this project using PayPal](https://img.shields.io/badge/paypal-donate-yellow.svg)](https://paypal.me/gripp)

This library provides adds some useful doctrine hooks.

## Installation

Open a command console, enter your project directory and execute the following command to download the latest stable version of this library:

```
composer require core23/doctrine-extensions
```

## Usage

### Confirmable entities

If you need entities that needs to be confirmed, just implement the `Core23\DoctrineExtensions\Model\ConfirmableInterface`
in your entity class.

If you don't need the symfony framework, you need to register the `Core23\DoctrineExtensions\EventListener\ORM\ConfirmableListener`.

### Deleteable entities

If you need entities that should be soft deleted, just implement the `Core23\DoctrineExtensions\Model\DeletableInterface`
in your entity class.

If you don't need the symfony framework, you need to register the `Core23\DoctrineExtensions\EventListener\ORM\DeletableListener`.

### Lifecyle aware enties

If you need lifecyle information (creation / update date), just implement the `Core23\DoctrineExtensions\Model\LifecycleDateTimeInterface`
in your entity class.

If you don't need the symfony framework, you need to register the `Core23\DoctrineExtensions\EventListener\ORM\LifecycleDateListener`.

### Position aware entities

If you need sortable entities, just implement the `Core23\DoctrineExtensions\Model\PositionAwareInterface`
in your entity class.

If you don't need the symfony framework, you need to register the `Core23\DoctrineExtensions\EventListener\ORM\SortableListener`.

## Symfony usage

If you want to use this library inside symfony, you can use a bridge.

### Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in `bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Core23\DoctrineExtensions\Bridge\Symfony\Bundle\Core23DoctrineExtensionsBundle::class => ['all' => true],
];
```

## License

This library is under the [MIT license](LICENSE.md).
