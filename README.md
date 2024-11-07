# WHImportMapBundle
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

## Usage

First, make one or more calls to `add_script_entry_point()` in your Twig template(s). Then, output the `wh_importmap()` function (instead of using the Symfony `importmap()` function). For example:

```twig
<head>
{# ... #}
{% do add_script_entry_point('app') %}
{% block scripts %}{# Extending templates can override this block to add their own entry point scripts. #}{% endblock %}
{{ wh_importmap() }}
</head>
```

If, however, you don't always have at least one entry point script, you can use the `have_script_entry_points()` function*:

```twig
{% if have_script_entry_points() %}
    {{~ wh_importmap() }}
{% endif %}
```

\* It's safe to output `wh_importmap()` even if there are no entry point scripts; the only reason not to is to reduce unnecessary HTML.
