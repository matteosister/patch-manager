<?php

namespace Cypress\PatchManager\Tests\Bundle;

use Cypress\PatchManager\Bundle\DependencyInjection\PatchManagerCompilerPass;
use Cypress\PatchManager\Bundle\PatchManagerBundle;
use Cypress\PatchManager\PatchOperationHandler;
use Cypress\PatchManager\Tests\PatchManagerTestCase;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PatchManagerBundleTest extends PatchManagerTestCase
{
    public function testBuild(): void
    {
        $cb = $this->prophesize(ContainerBuilder::class);
        $cb->addCompilerPass(new PatchManagerCompilerPass())->shouldBeCalled();
        $cb->registerForAutoconfiguration(PatchOperationHandler::class)->shouldBeCalled()->willReturn(new ChildDefinition(''));
        $bundle = new PatchManagerBundle();
        $bundle->build($cb->reveal());
    }
}
