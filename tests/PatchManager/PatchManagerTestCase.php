<?php

namespace Cypress\PatchManager\Tests;

use Cypress\PatchManager\MatchedPatchOperation;
use Mockery as m;
use Prophecy\PhpUnit\ProphecyTestCase;

abstract class PatchManagerTestCase extends ProphecyTestCase
{
    protected function mockHandler($name = null)
    {
        $handler = m::mock('PatchManager\PatchOperationHandler');
        if (! is_null($name)) {
            $handler->shouldReceive('getName')->andReturn($name)->byDefault();
        }
        $handler->shouldReceive('getRequiredKeys')->andReturn(array())->byDefault();
        $handler->shouldReceive('configureOptions')->andReturn(array())->byDefault();
        return $handler;
    }

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
