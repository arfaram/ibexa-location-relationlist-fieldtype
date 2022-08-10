<?php

declare(strict_types=1);

namespace Ibexa\LocationRelationListFieldType\FieldType\LocationRelationList;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue as PersistenceValue;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Core\FieldType\Value as BaseValue;
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

    /** @return array  */
    public static function getTranslationMessages()
    {
        return [
            (new Message(self::$fieldTypeIdentifier.'.name', 'fieldtypes'))->setDesc('Location RelationList')
        ];
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
        //@todo ? ContentInfo
        if ($inputValue instanceof ContentInfo) {
            $inputValue = new Value([$inputValue->id]);
        } elseif (is_int($inputValue) || is_string($inputValue)) {
            // location id
            $inputValue = new Value([$inputValue]);
        } elseif (is_array($inputValue)) {
            // location id's
            $inputValue = new Value($inputValue);
        }

        return $inputValue;
    }


    protected function checkValueStructure(BaseValue $value)
    {
        if (!is_array($value->destinationLocationIds)) {
            throw new InvalidArgumentType(
                '$value->destinationLocationIds',
                'array',
                $value->destinationLocationIds
            );
        }

        foreach ($value->destinationLocationIds as $key => $locationId) {
            if (!is_int($locationId) && !is_string($locationId)) {
                throw new InvalidArgumentType(
                    "\$value->destinationLocationIds[$key]",
                    'string|int',
                    $locationId
                );
            }
        }
    }

    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        // TODO: Implement getName() method.
    }

    public function validate(FieldDefinition $fieldDefinition, SPIValue $fieldValue)
    {
        $validationErrors = [];

        if ($this->isEmptyValue($fieldValue)) {
            return $validationErrors;
        }

        $validatorConfiguration = $fieldDefinition->getValidatorConfiguration();
        $constraints = isset($validatorConfiguration['RelationListValueValidator']) ?
            $validatorConfiguration['RelationListValueValidator'] :
            [];

        $validationErrors = [];

        if (isset($constraints['selectionLimit']) &&
            $constraints['selectionLimit'] > 0 && count($fieldValue->destinationLocationIds) > $constraints['selectionLimit']) {
            $validationErrors[] = new ValidationError(
                'The selected content items number cannot be higher than %limit%.',
                null,
                [
                    '%limit%' => $constraints['selectionLimit'],
                ],
                'destinationContentIds'
            );
        }

        return $validationErrors;
    }

    public function getEmptyValue()
    {
        return new Value();
    }

    public function fromHash($hash)
    {
        return new Value($hash['destinationLocationIds']);
    }

    public function toHash(SPIValue $value)
    {
        return ['destinationLocationIds' => $value->destinationLocationIds];
    }

    protected function getSortInfo(BaseValue $value)
    {
        return implode(',', $value->destinationLocationIds);
    }

    /**
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