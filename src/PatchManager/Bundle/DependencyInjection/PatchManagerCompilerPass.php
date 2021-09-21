<?php

namespace Cypress\PatchManager\Bundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PatchManagerCompilerPass implements CompilerPassInterface
{
    public const PATCH_MANAGER_HANDLER_TAG = 'patch_manager.handler';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('patch_manager.operation_matcher')) {
            return;
        }

        $definition = $container->getDefinition(
            'patch_manager.operation_matcher'
        );
        $taggedServices = $container->findTaggedServiceIds(self::PATCH_MANAGER_HANDLER_TAG);
        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addHandler',
                [new Reference($id)]
            );
        }
    }
}
