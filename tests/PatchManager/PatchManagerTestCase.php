<?php

namespace PatchManager\Tests;

use PatchManager\MatchedPatchOperation;
use Mockery as m;

abstract class PatchManagerTestCase extends \PHPUnit_Framework_TestCase
{
    protected function mockHandler($name = null)
    {
        $handler = m::mock('PatchManager\Handler\PatchOperationHandler');
        if (! is_null($name)) {
            $handler->shouldReceive('getName')->andReturn($name)->byDefault();
        }
        $handler->shouldReceive('getRequiredKeys')->andReturn(array())->byDefault();
        return $handler;
    }

    protected function getMatchedPatchOperation($handlerName = null)
    {
        return MatchedPatchOperation::create(array(), $this->mockHandler($handlerName));
    }

    protected function tearDown()
    {
        m::close();
    }
}
