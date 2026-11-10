<?php

declare(strict_types=1);

namespace Cypress\PatchManager\Bundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;

class PatchManagerExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array $configs An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     * @throws \Exception
     *
     * @api
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loaderHandlers = new Loader\PhpFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config/handlers')
        );
        $loader->load('services.php');
        $this->handleGenericConfig($config, $container, $loaderHandlers);
    }

    private function handleGenericConfig(
        array $config,
        ContainerBuilder $container,
        Loader\PhpFileLoader $loaderHandlers
    ): void {
        if ($config['dispatch_events']) {
            $patchManagerDefinition = $container->getDefinition('patch_manager');
            $patchManagerDefinition->addMethodCall(
                'setEventDispatcherInterface',
                [new Reference('event_dispatcher')]
            );
        }
        if (!is_null($config['alias'])) {
            $container->setAlias($config['alias'], 'patch_manager');
        }
        if (array_key_exists('state_machine', $config['handlers'])) {
            $this->handleStateMachine($loaderHandlers, $container);
        }
        $container->setParameter('patch_manager.strict_mode', $config['strict_mode']);
    }

    private function handleStateMachine(Loader\PhpFileLoader $loaderHandlers, ContainerBuilder $container): void
    {
        if (!class_exists('Finite\\StateMachine')) {
            $msg = 'If you want to use the patch manager with "op": "sm" you should install ';
            $msg .= 'the finite library. See https://github.com/yohang/Finite';

            throw new \RuntimeException($msg);
        }
        $loaderHandlers->load('state_machine.php');
        $smHandlerDefinition = $container->getDefinition('patch_manager.handler.state_machine');
        $smHandlerDefinition->addTag('patch_manager.handler');
    }
}
