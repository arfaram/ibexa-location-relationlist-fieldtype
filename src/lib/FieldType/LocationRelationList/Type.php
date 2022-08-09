<?php

declare(strict_types=1);

namespace Ibexa\LocationRelationListFieldType\FieldType\LocationRelationList;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue as PersistenceValue;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\ValidationError;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class Type extends FieldType implements TranslationContainerInterface
{
    /** @var string */
    private static $fieldTypeIdentifier = 'locationrelationlist';

    public const SELECTION_BROWSE = 0;

    protected $settingsSchema = [
        'selectionMethod' => [
            'type' => 'int',
            'default' => self::SELECTION_BROWSE,
        ],
        'selectionDefaultLocation' => [
            'type' => 'string',
            'default' => null,
        ],
        'selectionContentTypes' => [
            'type' => 'array',
            'default' => [],
        ],
    ];

    protected $validatorConfigurationSchema = [
        'RelationListValueValidator' => [
            'selectionLimit' => [
                'type' => 'int',
                'default' => 0,
            ],
        ],
    ];

    public function getFieldTypeIdentifier(): string
    {
        return self::$fieldTypeIdentifier;
    }

    public function toPersistenceValue(?SPIValue $value)
    {
        if ($value === null) {
            return new PersistenceValue(
                [
                    'data' => [],
                    'externalData' => null,
                    'sortKey' => null,
                ]
            );
        }

        return new PersistenceValue(
            [
                'data' => [
                    'locationId' => $value->locationId,
                ],
                'externalData' => null,
                'sortKey' => $this->getSortInfo($value->locationId),
            ]
        );
    }

    public function validateFieldSettings($fieldSettings)
    {
        $validationErrors = [];

        foreach ($fieldSettings as $name => $value) {
            if (!isset($this->settingsSchema[$name])) {
                $validationErrors[] = new ValidationError(
                    "Setting '%setting%' is unknown",
                    null,
                    [
                        '%setting%' => $name,
                    ],
                    "[$name]"
                );
                continue;
            }

            switch ($name) {
                case 'selectionMethod':
                    if (!$this->isValidSelectionMethod($value)) {
                        $validationErrors[] = new ValidationError(
                            "The following options are available for setting '%setting%': %selection_browse%",
                            null,
                            [
                                '%setting%' => $name,
                                '%selection_browse%' => self::SELECTION_BROWSE
                            ],
                            "[$name]"
                        );
                    }
                    break;
                case 'selectionContentTypes':
                    if (!is_array($value)) {
                        $validationErrors[] = new ValidationError(
                            "Setting '%setting%' value must be of array type",
                            null,
                            [
                                '%setting%' => $name,
                            ],
                            "[$name]"
                        );
                    }
                    break;
                case 'selectionDefaultLocation':
                    if (!is_int($value) && !is_string($value) && $value !== null) {
                        $validationErrors[] = new ValidationError(
                            "Setting '%setting%' value must be of either null, string or integer",
                            null,
                            [
                                '%setting%' => $name,
                            ],
                            "[$name]"
                        );
                    }
                    break;
                case 'selectionLimit':
                    if (!is_int($value)) {
                        $validationErrors[] = new ValidationError(
                            "Setting '%setting%' value must be of integer type",
                            null,
                            [
                                '%setting%' => $name,
                            ],
                            "[$name]"
                        );
                    }
                    if ($value < 0) {
                        $validationErrors[] = new ValidationError(
                            "Setting '%setting%' value cannot be lower than 0",
                            null,
                            [
                                '%setting%' => $name,
                            ],
                            "[$name]"
                        );
                    }
                    break;
            }
        }

        return $validationErrors;
    }

    public function validateValidatorConfiguration($validatorConfiguration)
    {
        $validationErrors = [];

        foreach ($validatorConfiguration as $validatorIdentifier => $constraints) {
            if ($validatorIdentifier !== 'RelationListValueValidator') {
                $validationErrors[] = new ValidationError(
                    "Validator '%validator%' is unknown",
                    null,
                    [
                        '%validator%' => $validatorIdentifier,
                    ],
                    "[$validatorIdentifier]"
                );

                continue;
            }

            foreach ($constraints as $name => $value) {
                if ($name === 'selectionLimit') {
                    if (!is_int($value) && !ctype_digit($value)) {
                        $validationErrors[] = new ValidationError(
                            "Validator parameter '%parameter%' value must be an integer",
                            null,
                            [
                                '%parameter%' => $name,
                            ],
                            "[$validatorIdentifier][$name]"
                        );
                    }
                    if ($value < 0) {
                        $validationErrors[] = new ValidationError(
                            "Validator parameter '%parameter%' value must be equal to/greater than 0",
                            null,
                            [
                                '%parameter%' => $name,
                            ],
                            "[$validatorIdentifier][$name]"
                        );
                    }
                } else {
                    $validationErrors[] = new ValidationError(
                        "Validator parameter '%parameter%' is unknown",
                        null,
                        [
                            '%parameter%' => $name,
                        ],
                        "[$validatorIdentifier][$name]"
                    );
                }
            }
        }

        return $validationErrors;
    }

    protected function createValueFromInput($inputValue)
    {
        // TODO: Implement createValueFromInput() method.
    }

    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
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

    protected function checkValueStructure(SPIValue $value)
    {
        // TODO: Implement checkValueStructure() method.
    }

    public function toHash(SPIValue $value)
    {
        // TODO: Implement toHash() method.
    }

    /** @return array  */
    public static function getTranslationMessages()
    {
        return [
            (new Message(self::$fieldTypeIdentifier.'.name', 'fieldtypes'))->setDesc('Location RelationList')
        ];
    }

    /**
     * Returns whether the field type is searchable.
     *
     * @return bool
     */
    public function isSearchable()
    {
        return true;
    }

    /**
     * Checks whether given selectionMethod is valid.
     *
     * @param int $selectionMethod
     *
     * @return bool
     */
    private function isValidSelectionMethod($selectionMethod)
    {
        return $selectionMethod === self::SELECTION_BROWSE;
    }
}