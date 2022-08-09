<?php

declare(strict_types=1);

namespace Ibexa\LocationRelationListFieldType\FieldType\LocationRelationList\Converter;

use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;

class LocationRelationListConverter implements Converter
{

    /**
     * Converts data from $value to $storageFieldValue.
     *
     * @param FieldValue $value
     * @param StorageFieldValue $storageFieldValue
     */
    public function toStorageValue(FieldValue $value, StorageFieldValue $storageFieldValue)
    {
//        $storageFieldValue->dataText = $value->data;
//        $storageFieldValue->sortKeyString = $value->sortKey;
    }

    /**
     * Converts data from $value to $fieldValue.
     *
     * @param StorageFieldValue $value
     * @param FieldValue $fieldValue
     */
    public function toFieldValue(StorageFieldValue $value, FieldValue $fieldValue)
    {
        $fieldValue->data = ['locationIds' => []];
        if ($value->dataText === null) {
            return;
        }

        $priorityByContentId = [];

        $dom = new \DOMDocument('1.0', 'utf-8');
        if ($dom->loadXML($value->dataText) === true) {
            foreach ($dom->getElementsByTagName('relation-item') as $relationItem) {
                /* @var \DOMElement $relationItem */
                $priorityByContentId[$relationItem->getAttribute('location-id')] =
                    $relationItem->getAttribute('priority');
            }
        }

        asort($priorityByContentId, SORT_NUMERIC);

        $fieldValue->data['locationIds'] = array_keys($priorityByContentId);
        $fieldValue->sortKey = $value->sortKeyString;
    }

    /**
     * Converts field definition data in $fieldDef into $storageFieldDef.
     *
     * @param FieldDefinition $fieldDef
     * @param StorageFieldDefinition $storageDef
     */
    public function toStorageFieldDefinition(FieldDefinition $fieldDef, StorageFieldDefinition $storageDef)
    {
        $fieldSettings = $fieldDef->fieldTypeConstraints->fieldSettings;
        $validators = $fieldDef->fieldTypeConstraints->validators;
        $doc = new \DOMDocument('1.0', 'utf-8');
        $root = $doc->createElement('related-objects');
        $doc->appendChild($root);

        $constraints = $doc->createElement('constraints');
        if (!empty($fieldSettings['selectionContentTypes'])) {
            foreach ($fieldSettings['selectionContentTypes'] as $typeIdentifier) {
                $allowedClass = $doc->createElement('allowed-class');
                $allowedClass->setAttribute('contentclass-identifier', $typeIdentifier);
                $constraints->appendChild($allowedClass);
                unset($allowedClass);
            }
        }
        $root->appendChild($constraints);


        $selectionType = $doc->createElement('selection_type');
        if (isset($fieldSettings['selectionMethod'])) {
            $selectionType->setAttribute('value', (string)$fieldSettings['selectionMethod']);
        } else {
            $selectionType->setAttribute('value', '0');
        }
        $root->appendChild($selectionType);

        $defaultLocation = $doc->createElement('contentobject-placement');
        if (!empty($fieldSettings['selectionDefaultLocation'])) {
            $defaultLocation->setAttribute('node-id', (string)$fieldSettings['selectionDefaultLocation']);
        }
        $root->appendChild($defaultLocation);

        $selectionLimit = $doc->createElement('selection_limit');
        if (isset($validators['RelationListValueValidator']['selectionLimit'])) {
            $selectionLimit->setAttribute('value', (string)$validators['RelationListValueValidator']['selectionLimit']);
        } else {
            $selectionLimit->setAttribute('value', '0');
        }
        $root->appendChild($selectionLimit);

        $doc->appendChild($root);
        $storageDef->dataText5 = $doc->saveXML();
    }

    /**
     * Converts field definition data in $storageDef into $fieldDef.
     *
     * @param StorageFieldDefinition $storageDef
     * @param FieldDefinition $fieldDef
     */
    public function toFieldDefinition(StorageFieldDefinition $storageDef, FieldDefinition $fieldDef)
    {
        // default settings
        $fieldDef->fieldTypeConstraints->fieldSettings = [
            'selectionMethod' => 0,
            'selectionDefaultLocation' => null,
            'selectionContentTypes' => [],
        ];

        $fieldDef->fieldTypeConstraints->validators = [
            'RelationListValueValidator' => [
                'selectionLimit' => 0,
            ],
        ];

        // default value
        $fieldDef->defaultValue = new FieldValue();
        $fieldDef->defaultValue->data = ['locationIds' => []];

        if ($storageDef->dataText5 === null) {
            return;
        }

        $dom = new \DOMDocument('1.0', 'utf-8');
        if (empty($storageDef->dataText5) || $dom->loadXML($storageDef->dataText5) !== true) {
            return;
        }

        // read settings from storage
        $fieldSettings = &$fieldDef->fieldTypeConstraints->fieldSettings;
        if (
            ($selectionType = $dom->getElementsByTagName('selection_type')->item(0)) &&
            $selectionType->hasAttribute('value')
        ) {
            $fieldSettings['selectionMethod'] = (int)$selectionType->getAttribute('value');
        }

        if (
            ($defaultLocation = $dom->getElementsByTagName('contentobject-placement')->item(0)) &&
            $defaultLocation->hasAttribute('node-id')
        ) {
            $fieldSettings['selectionDefaultLocation'] = (int)$defaultLocation->getAttribute('node-id');
        }

        if (!($constraints = $dom->getElementsByTagName('constraints'))) {
            return;
        }

        foreach ($constraints->item(0)->getElementsByTagName('allowed-class') as $allowedClass) {
            $fieldSettings['selectionContentTypes'][] = $allowedClass->getAttribute('contentclass-identifier');
        }

        // read validators configuration from storage
        $validators = &$fieldDef->fieldTypeConstraints->validators;
        if (
            ($selectionLimit = $dom->getElementsByTagName('selection_limit')->item(0)) &&
            $selectionLimit->hasAttribute('value')
        ) {
            $validators['RelationListValueValidator']['selectionLimit'] = (int)$selectionLimit->getAttribute('value');
        }
    }

    /**
     * Returns the name of the index column in the attribute table.
     *
     * Returns the name of the index column the datatype uses, which is either
     * "sort_key_int" or "sort_key_string". This column is then used for
     * filtering and sorting for this type.
     *
     * @return string
     */
    public function getIndexColumn()
    {
        return 'sort_key_string';
    }
}
