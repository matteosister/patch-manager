<?php

namespace Cypress\PatchManager\Tests\Bundle\DependencyInjection;

use Cypress\PatchManager\Bundle\DependencyInjection\PatchManagerCompilerPass;
use Cypress\PatchManager\Tests\PatchManagerTestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\Reference;

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
        $this->compilerPass = new PatchManagerCompilerPass($this->cb);
    }

    public function testProcessWithoutDefinition()
    {
        $this->cb->hasDefinition("patch_manager.operation_matcher")->willReturn(false);
        $this->assertNull($this->compilerPass->process($this->cb->reveal()));
    }

    public function testProcessWithoutTaggedServices()
    {
        $this->cb->hasDefinition("patch_manager.operation_matcher")->willReturn(false);
        $this->assertNull($this->compilerPass->process($this->cb->reveal()));
    }

    public function testProcess()
    {
        $this->cb->findTaggedServiceIds()->willReturn(['test.service' => 'test', 'test.service2' => 'test2']);
        $definition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        //$definition->addMethodCall()->with('addHandler', array(new Reference('test.service')))->once()->andReturn();
        //$definition->addMethodCall('addHandler', array(new Reference('test.service')))->shouldBeCalled();
        //$definition->shouldReceive('addMethodCall')->with('addHandler', array(new Reference('test.service2')))->once()->andReturn();
        //$definition->addMethodCall('addHandler', array(new Reference('test.service2')))->shouldBeCalled();
        $this->cb->getDefinition()->willReturn($definition);
        $this->cb->hasDefinition("patch_manager.operation_matcher")->shouldBeCalled();

        $this->compilerPass->process($this->cb->reveal());
    }
}
