<?php

namespace Cypress\PatchManager\Bundle;

use Cypress\PatchManager\Bundle\DependencyInjection\PatchManagerCompilerPass;
use Cypress\PatchManager\Tests\PatchManagerTestCase;
use Symfony\Component\DependencyInjection\ChildDefinition;

class PatchManagerBundleTest extends PatchManagerTestCase
{
    public function test_build()
    {
        $cb = $this->prophesize('Symfony\Component\DependencyInjection\ContainerBuilder');
        $cb->addCompilerPass(new PatchManagerCompilerPass())->shouldBeCalled();
        $cb->registerForAutoconfiguration('Cypress\PatchManager\PatchOperationHandler')->shouldBeCalled()->willReturn(new ChildDefinition(''));
        $bundle = new PatchManagerBundle();
        $bundle->build($cb->reveal());
    }
}
