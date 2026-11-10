<?php

declare(strict_types=1);

namespace Cypress\PatchManager\Tests\Bundle\DependencyInjection;

use Cypress\PatchManager\Bundle\DependencyInjection\PatchManagerCompilerPass;
use Cypress\PatchManager\Tests\PatchManagerTestCase;
use PHPUnit\Framework\Attributes\Test;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class PatchManagerCompilerPassTest extends PatchManagerTestCase
{
    /** @var ObjectProphecy<ContainerBuilder> */
    private ObjectProphecy $containerBuilder;

    private PatchManagerCompilerPass $compilerPass;

    public function setUp(): void
    {
        parent::setUp();
        $this->containerBuilder = $this->prophesize(ContainerBuilder::class);
        $this->compilerPass = new PatchManagerCompilerPass();
    }

    #[Test]
    public function processWithoutDefinition(): void
    {
        try {
            $this->containerBuilder->hasDefinition(Argument::exact('patch_manager.operation_matcher'))->willReturn(false);
            $this->compilerPass->process($this->containerBuilder->reveal());
        } catch (\Throwable $e) {
            $this->fail();
        }

        $this->assertTrue(true);
    }

    #[Test]
    public function shouldProcessSucceed(): void
    {
        $this->containerBuilder->findTaggedServiceIds(
            Argument::exact('patch_manager.handler')
        )->willReturn(['test.service' => 'test', 'test.service2' => 'test2']);
        $definition = $this->prophesize(Definition::class);
        $this->containerBuilder->hasDefinition(Argument::exact('patch_manager.operation_matcher'))->shouldBeCalled();
        $this->containerBuilder->getDefinition(Argument::exact('patch_manager.operation_matcher'))->willReturn($definition);

        $this->compilerPass->process($this->containerBuilder->reveal());
    }
}
