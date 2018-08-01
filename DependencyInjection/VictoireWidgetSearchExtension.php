<?php

namespace Victoire\Widget\SearchBundle\DependencyInjection;

use FOS\ElasticaBundle\DependencyInjection\FOSElasticaExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration.
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
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function prepend(ContainerBuilder $container)
    {
        // Build fos_elastica config for each widget
        $elasticaConfig = [];
        $kernel = new \AppKernel('prod', false);
        /* @var Bundle $bundle */
        foreach ($kernel->registerBundles() as $bundle) {
            //If bundle is a widget
            if (0 === strpos($bundle->getNamespace(), 'Victoire\\Widget\\')) {
                //search for a config file
                $widgetConfig = Yaml::parse(file_get_contents(
                    sprintf('%s/Resources/config/config.yml', $bundle->getPath())
                ));

                if (is_array($widgetConfig)) {
                    foreach ($widgetConfig['victoire_core']['widgets'] as $_widgetConfig) {
                        if (!isset($widgetConfig['fos_elastica']['indexes']['widgets'])) {
                            continue;
                        }

                        $_config = [
                            'types' => [
                                $_widgetConfig['name'] => [
                                    'serializer' => [
                                        'groups' => ['search'],
                                    ],
                                    'mappings' => [],
                                    'persistence' => [
                                        'driver' => 'orm',
                                        'model' => $_widgetConfig['class'],
                                        'provider' => [],
                                        'listener' => [],
                                        'finder' => [],
                                    ],
                                ],
                            ],
                        ];

                        $_config = array_merge_recursive($widgetConfig['fos_elastica']['indexes']['widgets'], $_config);
                        $elasticaConfig = array_merge_recursive($elasticaConfig, $_config);
                    }
                }
            }
        }
        $container->setParameter('victoire_search_widgets_index', $elasticaConfig);
    }
}
