<?php

namespace Cypress\PatchManagerBundle\Tests;

use Cypress\PatchManagerBundle\PatchManager\Handler\PatchOperationHandler;
use Cypress\PatchManagerBundle\PatchManager\MatchedPatchOperation;
use Mockery as m;

class PatchManagerTestCase extends \PHPUnit_Framework_TestCase
{
    protected function mockHandler($name = null)
    {
        $handler = m::mock('Cypress\PatchManagerBundle\PatchManager\Handler\PatchOperationHandler');
        if (! is_null($name)) {
            $handler->shouldReceive('getName')->andReturn($name)->byDefault();
        }
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
