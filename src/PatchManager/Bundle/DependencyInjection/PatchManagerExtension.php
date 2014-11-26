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
        $loaderHandlers = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/handlers'));
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
            $this->handleData($config, $loaderHandlers, $container);
        }
        if (array_key_exists('state_machine', $config['handlers'])) {
            $this->handleStateMachine($config, $loaderHandlers, $container);
        }
        $container->setParameter('patch_manager.strict_mode', $config['strict_mode']);
    }

    /**
     * @param $config
     * @param Loader\XmlFileLoader $loaderHandlers
     * @param ContainerBuilder $container
     */
    private function handleData($config, Loader\XmlFileLoader $loaderHandlers, ContainerBuilder $container)
    {
        if ($config['handlers']['data']['doctrine']) {
            $loaderHandlers->load('data_doctrine.xml');
            $doctrineEMName = sprintf('doctrine.orm.%s_entity_manager', $config['handlers']['data']['entity_manager']);
            $dataDoctrineDefinition = $container->getDefinition('patch_manager.handler.data');
            $dataDoctrineDefinition->addArgument(
                new Reference($doctrineEMName)
            );
        } else {
            $loaderHandlers->load('data.xml');
        }
        $dataHandlerDefinition = $container->getDefinition('patch_manager.handler.data');
        $dataHandlerDefinition->addTag('patch_manager.handler');
    }

    /**
     * @param $config
     * @param Loader\XmlFileLoader $loaderHandlers
     * @param ContainerBuilder $container
     */
    private function handleStateMachine($config, Loader\XmlFileLoader $loaderHandlers, ContainerBuilder $container)
    {
        if (! interface_exists('Finite\Factory\FactoryInterface')) {
            throw new \RuntimeException('If you want to use the patch manager with "op": "sm" you should install the finite library. See https://github.com/yohang/Finite');
        }
        $loaderHandlers->load('state_machine.xml');
        $smHandlerDefinition = $container->getDefinition('patch_manager.handler.state_machine');
        $smHandlerDefinition->addTag('patch_manager.handler');
    }
}
