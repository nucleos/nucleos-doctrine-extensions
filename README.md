What is the Doctrine Extensions PHP library?
============================================
[![Latest Stable Version](https://poser.pugx.org/core23/doctrine-extensions/v/stable)](https://packagist.org/packages/core23/doctrine-extensions)
[![Latest Unstable Version](https://poser.pugx.org/core23/doctrine-extensions/v/unstable)](https://packagist.org/packages/core23/doctrine-extensions)
[![License](https://poser.pugx.org/core23/doctrine-extensions/license)](https://packagist.org/packages/core23/doctrine-extensions)

[![Build Status](https://travis-ci.org/core23/doctrine-extensions.svg)](http://travis-ci.org/core23/doctrine-extensions)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/core23/doctrine-extensions/badges/quality-score.png)](https://scrutinizer-ci.com/g/core23/doctrine-extensions/)
[![Coverage Status](https://coveralls.io/repos/core23/doctrine-extensions/badge.svg)](https://coveralls.io/r/core23/doctrine-extensions)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/83024b06-03e0-4b04-a011-8ad598d93af4/mini.png)](https://insight.sensiolabs.com/projects/51aa4b42-d229-4994-bb3a-156da22a1375)

[![Donate to this project using Flattr](https://img.shields.io/badge/flattr-donate-yellow.svg)](https://flattr.com/profile/core23)
[![Donate to this project using PayPal](https://img.shields.io/badge/paypal-donate-yellow.svg)](https://paypal.me/gripp)

This library provides adds some useful doctrine hooks and a bridge for symfony.

### Installation

```
composer require core23/doctrine-extensions
```

### Symfony usage

#### Enabling the bundle

```php
    // app/AppKernel.php

    public function registerBundles()
    {
        return array(
            // ...
            
            new Core23\DoctrineExtensions\Bridge\Symfony\Bundle\Core23DoctrineExtensionsBundle(),

            // ...
        );
    }
```

This lib / bundle is available under the [MIT license](LICENSE.md).
