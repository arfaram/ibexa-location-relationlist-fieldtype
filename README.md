# Ibexa location relation list fieldtype Bundle


## Requirement

Ibexa **4.x +**


## Features

## Limitations
 
## Installation

```bash
composer require arfaram/ibexa-location-relation-list-fieldtype
```

- Activate the Bundle in `bundles.php`

```php
    Ibexa\LocationRelationListFieldTypeBundle\IbexaLocationRelationListFieldTypeBundle::class => ['all' => true],
```

### Translation

#### fieldtype name
If the new fieldtype is shown as `location.name` then run
```
php bin/console translation:extract en --config=ibexa_location_relation_list_fieldtype
```

## Screenshots


