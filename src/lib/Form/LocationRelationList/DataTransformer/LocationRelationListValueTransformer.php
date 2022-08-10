<?php

declare(strict_types=1);

namespace Ibexa\LocationRelationListFieldType\Form\LocationRelationList\DataTransformer;


use Ibexa\LocationRelationListFieldType\FieldType\LocationRelationList\Value;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * DataTransformer for RelationList\Value in single select mode.
 */
class LocationRelationListValueTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (!$value instanceof Value) {
            return null;
        }

        if ($value->destinationLocationIds === []) {
            return null;
        }

        return implode(',', $value->destinationLocationIds);
    }

    public function reverseTransform($value)
    {
        if ($value === null) {
            return null;
        }

        $destinationLocationIds = explode(',', $value);
        $destinationLocationIds = array_map('trim', $destinationLocationIds);
        $destinationLocationIds = array_map('intval', $destinationLocationIds);

        return new Value($destinationLocationIds);
    }
}

