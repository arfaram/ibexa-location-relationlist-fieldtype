# Ibexa location fieldtype Bundle


## Requirement

Ibexa **4.x +**


## Features

## Limitations
 
## Installation

```bash
composer require arfaram/ibexa-location-fieldtype
```

- Activate the Bundle in `bundles.php`

```php
    Ibexa\LocationFieldTypeBundle\IbexaLocationFieldTypeBundle::class => ['all' => true],
```

### Translation

#### fieldtype name
If the new fieldtype is shown as `location.name` then run
```
php bin/console translation:extract en --config=ibexa_location_fieldtype
```

## Screenshots


