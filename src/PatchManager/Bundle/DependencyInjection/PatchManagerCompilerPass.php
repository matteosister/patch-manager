<?php

namespace PatchManager\Bundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PatchManagerCompilerPass implements CompilerPassInterface
{
    const PATCH_MANAGER_HANDLER_TAG = 'patch_manager.handler';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $services = $container->findTaggedServiceIds(self::PATCH_MANAGER_HANDLER_TAG);
        var_dump($services);
    }
}