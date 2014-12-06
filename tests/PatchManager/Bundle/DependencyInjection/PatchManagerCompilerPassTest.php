<?php

namespace PatchManager\Bundle\DependencyInjection;

use PatchManager\Tests\PatchManagerTestCase;
use Mockery as m;
use Symfony\Component\DependencyInjection\Reference;

class PatchManagerCompilerPassTest extends PatchManagerTestCase
{
    private $cb;

    /**
     * @var PatchManagerCompilerPass
     */
    private $compilerPass;

    public function setUp()
    {
        parent::setUp();
        $this->cb = m::mock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $this->cb->shouldReceive('hasDefinition')->andReturn(true)->byDefault();
        $this->cb->shouldReceive('findTaggedServiceIds')->andReturn(array())->byDefault();
        $this->definition = m::mock('Symfony\Component\DependencyInjection\Definition');
        $this->cb->shouldReceive('getDefinition')->andReturn(null)->byDefault();
        $this->compilerPass = new PatchManagerCompilerPass($this->cb);
    }

    public function test_process_without_definition()
    {
        $this->cb->shouldReceive('hasDefinition')->andReturn(false)->byDefault();
        $this->assertNull($this->compilerPass->process($this->cb));
    }

    public function test_process_without_tagged_services()
    {
        $this->assertNull($this->compilerPass->process($this->cb));
    }

    public function test_process()
    {
        $this->cb->shouldReceive('findTaggedServiceIds')
            ->andReturn(array('test.service' => 'test', 'test.service2' => 'test2'))->byDefault();
        $definition = m::mock('Symfony\Component\DependencyInjection\Definition');
        $definition->shouldReceive('addMethodCall')
            ->with('addHandler', array(new Reference('test.service')))->once()->andReturn();
        $definition->shouldReceive('addMethodCall')
            ->with('addHandler', array(new Reference('test.service2')))->once()->andReturn();
        $this->cb->shouldReceive('getDefinition')->andReturn($definition)->byDefault();
        $this->compilerPass->process($this->cb);
    }
} 