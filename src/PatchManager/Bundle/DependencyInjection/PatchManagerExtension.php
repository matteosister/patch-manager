<?php

namespace PatchManager\Bundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class PatchManagerExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array $configs An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     *
     * @api
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loaderHanlders = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/handlers'));
        $loader->load('services.xml');
        if ($config['dispatch_events']) {
            $patchManagerDefinition = $container->getDefinition('patch_manager.patch_manager');
            $patchManagerDefinition->addMethodCall(
                'setEventDispatcherInterface',
                array(new Reference('event_dispatcher'))
            );
        }
        if (! is_null($config['alias'])) {
            $container->setAlias($config['alias'], 'patch_manager.patch_manager');
        }
        if (array_key_exists('data', $config['handlers'])) {
            if ($config['handlers']['data']['doctrine']) {
                $loaderHanlders->load('data_doctrine.xml');
                $dataDoctrineDefinition = $container->getDefinition('patch_manager.handler.data');
                $dataDoctrineDefinition->addArgument(
                    new Reference(
                        sprintf('doctrine.orm.%s_entity_manager', $config['handlers']['data']['entity_manager'])
                    )
                );
            } else {
                $loaderHanlders->load('data.xml');
            }
            $dataHandlerDefinition = $container->getDefinition('patch_manager.handler.data');
            $dataHandlerDefinition->addTag('patch_manager.handler');
        }
        $container->setParameter('patch_manager.strict_mode', $config['strict_mode']);
    }
}
