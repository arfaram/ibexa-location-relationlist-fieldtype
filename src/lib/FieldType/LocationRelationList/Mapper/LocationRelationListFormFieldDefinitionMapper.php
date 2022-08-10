<?php

namespace Ibexa\LocationRelationListFieldType\FieldType\LocationRelationList\Mapper;

use Ibexa\AdminUi\FieldType\Mapper\AbstractRelationFormMapper;
use Ibexa\AdminUi\Form\Data\FieldDefinitionData;
use Ibexa\ContentForms\Form\Type\LocationType;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationRelationListFormFieldDefinitionMapper extends AbstractRelationFormMapper
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;
        $fieldDefinitionForm
            ->add('selectionDefaultLocation', LocationType::class, [
                'required' => false,
                'property_path' => 'fieldSettings[selectionDefaultLocation]',
                'label' => /** @Desc("Default Location") */ 'field_definition.locationrelationlist.selection_default_location',
                'disabled' => $isTranslation,
            ])
            ->add('selectionContentTypes', ChoiceType::class, [
                'choices' => $this->getContentTypesHash(),
                'expanded' => false,
                'multiple' => true,
                'required' => false,
                'property_path' => 'fieldSettings[selectionContentTypes]',
                'label' => /** @Desc("Allowed Content Types") */ 'field_definition.locationrelationlist.selection_content_types',
                'disabled' => $isTranslation,
            ])
            ->add('selectionLimit', IntegerType::class, [
                'required' => false,
                'empty_data' => 0,
                'property_path' => 'validatorConfiguration[RelationListValueValidator][selectionLimit]',
                'label' => /** @Desc("Selection limit") */ 'field_definition.locationrelationlist.selection_limit',
                'disabled' => $isTranslation,
            ]);

    }

    /**
     * Fake method to set the translation domain for the extractor.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'content_type',
            ]);
    }
}
