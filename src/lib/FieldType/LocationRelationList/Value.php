<?php

declare(strict_types=1);

namespace Ibexa\LocationRelationListFieldType\FieldType\LocationRelationList;

use Ibexa\Core\FieldType\Value as BaseValue;

final class Value extends BaseValue
{
    /** @var array|null */
    public $destinationLocationIds;

    public function __construct(?array $destinationLocationIds = [])
    {
        $this->destinationLocationIds = $destinationLocationIds;
    }

    public function __toString()
    {
        return implode(',', $this->destinationLocationIds);
    }
}