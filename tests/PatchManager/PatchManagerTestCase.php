<?php

namespace Cypress\PatchManager\Tests;

use Cypress\PatchManager\MatchedPatchOperation;
use Cypress\PatchManager\Patchable as PatchableInterface;
use Cypress\PatchManager\PatchOperationHandler;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

abstract class PatchManagerTestCase extends TestCase
{
    use ProphecyTrait;

    protected function tearDown(): void
    {
        parent::tearDown();
        m::close();
    }

    /**
     * @param null|string $name
     * @param bool $canHandle
     *
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    protected function mockHandler(?string $name, bool $canHandle = true)
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

    /**
     * @param null $handlerName
     * @return MatchedPatchOperation
     */
    protected function getMatchedPatchOperation($handlerName = null): MatchedPatchOperation
    {
        return MatchedPatchOperation::create([], $this->mockHandler($handlerName)->reveal());
    }
}
