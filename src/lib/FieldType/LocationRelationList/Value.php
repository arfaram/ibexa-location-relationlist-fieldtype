<?php

declare(strict_types=1);

namespace Ibexa\LocationRelationListFieldType\FieldType\LocationRelationList;

use Ibexa\Contracts\Core\FieldType\Value as ValueInterface;

final class Value implements ValueInterface
{
    /** @var array|null */
    private $locationIds;

    public function __construct(?array $locationIds = [])
    {
        $this->locationIds = $locationIds;
    }

    public function getLocationIds(): ?array
    {
        return $this->locationIds;
    }

    public function setLocationId(array $locationIds = []): void
    {
        $this->locationIds = $locationIds;
    }

    public function __toString()
    {
        return implode(',', $this->locationIds);
    }
}