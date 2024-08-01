# Symfony Import Map Bundle
 Some changes and additions to the importmap side of Symfony's AssetMapper component.

## Installation

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
$ composer require willherzog/symfony-import-map-bundle
```

### Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require willherzog/symfony-import-map-bundle
```

#### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    WHSymfony\WHImportMapBundle\WHImportMapBundle::class => ['all' => true],
];
```

## TODO

Figure out how to read original Symfony framework config for AssetMapper component.
