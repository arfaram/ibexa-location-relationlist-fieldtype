<?php

declare(strict_types=1);

namespace Ibexa\LocationRelationListFieldType\FieldType\LocationRelationList;

use Ibexa\Core\FieldType\Value as BaseValue;

final class Value extends BaseValue
{
    /** @var array|null */
    public $locationIds;

    public function __construct(?array $locationIds = [])
    {
        $this->locationIds = $locationIds;
    }

    public function __toString()
    {
        return implode(',', $this->locationIds);
    }
}