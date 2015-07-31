<?php

namespace Cypress\PatchManager\Tests;

use Cypress\PatchManager\MatchedPatchOperation;
use Cypress\PatchManager\PatchOperationHandler;
use Mockery as m;
use Prophecy\PhpUnit\ProphecyTestCase;

abstract class PatchManagerTestCase extends ProphecyTestCase
{
    /**
     * @param null $name
     * @param bool $canHandle
     *
     * @return PatchOperationHandler|m\MockInterface
     */
    protected function mockHandler($name = null, $canHandle = true)
    {
        $handler = m::mock('Cypress\PatchManager\PatchOperationHandler');
        if (! is_null($name)) {
            $handler->shouldReceive('getName')->andReturn($name)->byDefault();
        }
        $handler->shouldReceive('getRequiredKeys')->andReturn(array())->byDefault();
        $handler->shouldReceive('configureOptions')->andReturn(array())->byDefault();
        $handler->shouldReceive('canHandle')->andReturn($canHandle);
        return $handler;
    }

    /**
     * @param null $handlerName
     * @return MatchedPatchOperation
     */
    protected function getMatchedPatchOperation($handlerName = null)
    {
        return MatchedPatchOperation::create(array(), $this->mockHandler($handlerName));
    }

    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }
}
