<?php

declare(strict_types=1);

namespace Ibexa\LocationRelationListFieldType\FieldType\LocationRelationList\Mapper;

use Ibexa\ContentForms\FieldType\Mapper\AbstractRelationFormMapper;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\LocationRelationListFieldType\Form\LocationRelationList\Type\LocationRelationListType;
use Symfony\Component\Form\FormInterface;


class LocationRelationListFormFieldValueMapper extends AbstractRelationFormMapper
{
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        LocationRelationListType::class,
                        [
                            'required' => $fieldDefinition->isRequired,
                            'label' => $fieldDefinition->getName(),
                            'default_location' => $this->loadDefaultLocationForSelection(
                                $fieldDefinition->getFieldSettings()['selectionDefaultLocation']
                            ),
                        ]
                    )
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }
}
