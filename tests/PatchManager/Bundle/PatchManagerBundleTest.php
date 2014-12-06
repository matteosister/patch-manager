<?php

namespace PatchManager\Bundle;

use PatchManager\Bundle\DependencyInjection\PatchManagerCompilerPass;
use PatchManager\Tests\PatchManagerTestCase;

class PatchManagerBundleTest extends PatchManagerTestCase
{
    public function test_build()
    {
        $cb = $this->prophesize('Symfony\Component\DependencyInjection\ContainerBuilder');
        $cb->addCompilerPass(new PatchManagerCompilerPass())->shouldBeCalled();
        $bundle = new PatchManagerBundle();
        $bundle->build($cb->reveal());
    }
} 