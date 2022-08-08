<?php

declare(strict_types=1);

namespace Ibexa\LocationFieldType\FieldType\Location;

use Ibexa\Contracts\Core\FieldType\Value as ValueInterface;

final class Value implements ValueInterface
{
    /** @var integer|null */
    private $locationId;

    public function __construct(?int $locationId = null)
    {
        $this->locationId = $locationId;
    }

    public function getLocationId(): ?float
    {
        return $this->locationId;
    }

    public function setLocationId(?int $locationId): void
    {
        $this->locationId = $locationId;
    }

    public function __toString()
    {
        return (string) $this->locationId;
    }
}