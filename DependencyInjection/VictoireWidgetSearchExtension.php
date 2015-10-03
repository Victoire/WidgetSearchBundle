<?php

namespace Victoire\Widget\SearchBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class VictoireWidgetSearchExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * @param  ContainerBuilder $container
     *
     * @return void
     */
    public function prepend(ContainerBuilder $container)
    {
        // Build fos_elastica config for each widget
        $elasticaConfig = [];
        $kernel = new \AppKernel('prod', false);
        $yamlParser = new Yaml();
        foreach ($kernel->registerBundles() as $bundle) {
            /** @var Bundle $bundle */
            $path = $bundle->getPath();
            //If bundle is a widget
            if (0 === strpos($bundle->getNamespace(), 'Victoire\\Widget\\')) {
                //find for a fos_elastica.yml config file
                $widgetConfig = $yamlParser->parse($path.'/Resources/config/config.yml');

                if (is_array($widgetConfig)) {
                    foreach ($widgetConfig['victoire_core']['widgets'] as $_widgetConfig) {
                        if (array_key_exists('fos_elastica', $widgetConfig)) {
                            $_config = [
                                'indexes' => [
                                    'widgets' => [
                                        'types' => [
                                            $_widgetConfig['name'] => [
                                                'serializer'  => [
                                                    'groups' => ['search']
                                                ],
                                                'mappings'    => [],
                                                'persistence' => [
                                                    'driver'   => 'orm',
                                                    'model'    => $_widgetConfig['class'],
                                                    'provider' => [],
                                                    'listener' => [],
                                                    'finder'   => [],
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ];
                            $_config = array_merge_recursive($widgetConfig['fos_elastica'], $_config);
                            $elasticaConfig = array_merge_recursive($elasticaConfig, $_config);
                        }
                    }
                }
            }
        }

        foreach ($container->getExtensions() as $name => $extension) {
            switch ($name) {
                case 'fos_elastica':
                    $container->prependExtensionConfig($name, $elasticaConfig);
                    break;
            }
        }
    }
}
