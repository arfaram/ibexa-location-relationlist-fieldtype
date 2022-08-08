<?php

declare(strict_types=1);

namespace Ibexa\LocationFieldType\FieldType\Location;

use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\FieldType;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class Type extends FieldType implements TranslationContainerInterface
{
    /** @var string */
    private static $fieldTypeIdentifier = 'location';

    public function getFieldTypeIdentifier(): string
    {
        return self::$fieldTypeIdentifier;
    }

    protected function createValueFromInput($inputValue)
    {
        // TODO: Implement createValueFromInput() method.
    }

    public function getName(Value $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        // TODO: Implement getName() method.
    }

    public function getEmptyValue()
    {
        // TODO: Implement getEmptyValue() method.
    }

    public function fromHash($hash)
    {
        // TODO: Implement fromHash() method.
    }

    protected function checkValueStructure(Value $value)
    {
        // TODO: Implement checkValueStructure() method.
    }

    public function toHash(Value $value)
    {
        // TODO: Implement toHash() method.
    }

    /** @return array  */
    public static function getTranslationMessages()
    {
        return [
            (new Message(self::$fieldTypeIdentifier.'.name', 'fieldtypes'))->setDesc('Location Relation')
        ];
    }
}