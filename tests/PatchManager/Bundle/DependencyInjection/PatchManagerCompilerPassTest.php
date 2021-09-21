<?php

namespace Cypress\PatchManager\Tests\Bundle\DependencyInjection;

use Cypress\PatchManager\Bundle\DependencyInjection\PatchManagerCompilerPass;
use Cypress\PatchManager\Tests\PatchManagerTestCase;
use Prophecy\Prophecy\ObjectProphecy;

class PatchManagerCompilerPassTest extends PatchManagerTestCase
{
    /** @var ObjectProphecy */
    private $cb;

    /**
     * @var PatchManagerCompilerPass
     */
    private $compilerPass;

    public function setUp(): void
    {
        parent::setUp();
        $this->cb = $this->prophesize('Symfony\Component\DependencyInjection\ContainerBuilder');
        $this->cb->hasDefinition()->willReturn(true);
        $this->cb->findTaggedServiceIds()->willReturn([]);
        $this->cb->getDefinition()->willReturn(null);
        $this->compilerPass = new PatchManagerCompilerPass();
    }

    public function testProcessWithoutDefinition()
    {
        try {
            $this->cb->hasDefinition("patch_manager.operation_matcher")->willReturn(false);
            $this->compilerPass->process($this->cb->reveal());
        } catch (\Throwable $e) {
            $this->fail();
        }

        $this->assertTrue(true);
    }

    public function testProcess()
    {
        $this->cb->findTaggedServiceIds()->willReturn(['test.service' => 'test', 'test.service2' => 'test2']);
        $definition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $this->cb->getDefinition()->willReturn($definition);
        $this->cb->hasDefinition("patch_manager.operation_matcher")->shouldBeCalled();

        $this->compilerPass->process($this->cb->reveal());
    }
}
