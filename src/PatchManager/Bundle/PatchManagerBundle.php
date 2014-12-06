<?php

namespace PatchManager\Bundle;

use PatchManager\Bundle\DependencyInjection\PatchManagerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PatchManagerBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new PatchManagerCompilerPass());
    }
}
