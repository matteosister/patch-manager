<?php

declare(strict_types=1);

namespace Cypress\PatchManager\Tests;

use Cypress\PatchManager\Patchable as PatchableInterface;
use Cypress\PatchManager\PatchOperationHandler;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

abstract class PatchManagerTestCase extends TestCase
{
    use ProphecyTrait;

    protected function tearDown(): void
    {
        parent::tearDown();
        m::close();
    }

    /**
     * @return ObjectProphecy<PatchOperationHandler>
     */
    protected function mockHandler(?string $name, bool $canHandle = true): ObjectProphecy
    {
        $handler = $this->prophesize(PatchOperationHandler::class);
        if (!is_null($name)) {
            $handler->getName()->willReturn($name);
        }
        $handler->configureOptions(Argument::any());
        $handler->canHandle(Argument::type(PatchableInterface::class))->willReturn($canHandle);
        $handler->handle(Argument::type(PatchableInterface::class), Argument::any());

        return $handler;
    }
}
