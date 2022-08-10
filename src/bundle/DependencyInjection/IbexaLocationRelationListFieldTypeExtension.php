<?php

namespace Ibexa\LocationRelationListFieldTypeBundle\DependencyInjection;

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

class IbexaLocationRelationListFieldTypeExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('controllers.yaml');
        $loader->load('field_types.yaml');
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $this->prependJMSTranslation($container);
        $this->prependViews($container);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function prependJMSTranslation(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('jms_translation', [
            'configs' => [
                'ibexa_location_relation_list_fieldtype' => [
                    'dirs' => [
                        __DIR__ . '/../../',
                    ],
                    'output_dir' => __DIR__ . '/../Resources/translations/',
                    'output_format' => 'xliff',
                    'excluded_dirs' => ['Behat', 'Tests', 'node_modules'],
                    'extractors' => [],
                ],
            ],
        ]);
    }

    private function prependViews(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/views.yaml';
        $config = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig('ibexa', $config);
        $container->addResource(new FileResource($configFile));
    }
}
