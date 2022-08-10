<?php

declare(strict_types=1);

namespace Ibexa\LocationRelationListFieldType\FieldType\LocationRelationList;

use Ibexa\Contracts\Core\FieldType\Indexable as IndexableInterface;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Contracts\Core\Search;

final class Indexable implements IndexableInterface
{
    public function getIndexData(Field $field, FieldDefinition $fieldDefinition)
    {
        return [
            new Search\Field(
                'value',
                $field->value->data['destinationLocationIds'],
                new Search\FieldType\MultipleStringField()
            ),
            new Search\Field(
                'sort_value',
                implode('-', $field->value->data['destinationLocationIds']),
                new Search\FieldType\StringField()
            ),
        ];
    }

    public function getIndexDefinition()
    {
        return [
            'value' => new Search\FieldType\MultipleStringField(),
            'sort_value' => new Search\FieldType\StringField(),
        ];
    }

    /**
     * Get name of the default field to be used for matching.
     *
     * As field types can index multiple fields (see MapLocation field type's
     * implementation of this interface), this method is used to define default
     * field for matching. Default field is typically used by Field criterion.
     *
     * @return string
     */
    public function getDefaultMatchField()
    {
        return 'value';
    }

    /**
     * Get name of the default field to be used for sorting.
     *
     * As field types can index multiple fields (see MapLocation field type's
     * implementation of this interface), this method is used to define default
     * field for sorting. Default field is typically used by Field sort clause.
     *
     * @return string
     */
    public function getDefaultSortField()
    {
        return 'sort_value';
    }
}