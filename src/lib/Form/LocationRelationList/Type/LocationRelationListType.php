<?php

declare(strict_types=1);

namespace Ibexa\LocationRelationListFieldType\Form\LocationRelationList\Type;

use Ibexa\Contracts\Core\Repository;
use Ibexa\LocationRelationListFieldType\FieldType\LocationRelationList\Value;
use Ibexa\LocationRelationListFieldType\Form\LocationRelationList\DataTransformer\LocationRelationListValueTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form Type representing locationrelationlist field type.
 */
class LocationRelationListType extends AbstractType
{
    /** @var Repository\LocationService  */
    private $locationService;

    /**
     * @param Repository\LocationService $locationService
     */
    public function __construct(
        Repository\LocationService $locationService
    )
    {
        $this->locationService = $locationService;
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'locationrelationlist';
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new LocationRelationListValueTransformer());
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['relations'] = [];
        $view->vars['default_location'] = $options['default_location'];

        /** @var \Ibexa\LocationRelationListFieldType\FieldType\LocationRelationList\Value $data */
        $data = $form->getData();

        if (!$data instanceof Value) {
            return;
        }

        foreach ($data->destinationLocationIds as $locationId) {

            $contentInfo = null;
            $contentType = null;
            $unauthorized = false;

            try {
                $location = $this->locationService->loadLocation($locationId);
                $contentInfo = $location->getContent()->getVersionInfo()->getContentInfo();
                $contentType = $location->getContent()->getContentType();
            } catch (Repository\Exceptions\UnauthorizedException $e) {
                $unauthorized = true;
            }

            $view->vars['relations'][$locationId] = [
                'contentInfo' => $contentInfo,
                'contentType' => $contentType,
                'unauthorized' => $unauthorized,
                'contentId' => $location->getContent()->id,
                'locationId' => $location->id,
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'default_location' => null,
        ]);

        $resolver->setAllowedTypes('default_location', ['null', Repository\Values\Content\Location::class]);
    }
}
