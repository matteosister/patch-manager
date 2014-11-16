<?php

namespace Cypress\PatchManagerBundle\Tests;

use Cypress\PatchManagerBundle\PatchManager\Handler\PatchOperationHandler;
use Mockery as m;

class PatchManagerTestCase extends \PHPUnit_Framework_TestCase
{
    protected function mockHandler($name = null)
    {
        $handler = m::mock(PatchOperationHandler::class);
        if (! is_null($name)) {
            $handler->shouldReceive('getName')->andReturn($name)->byDefault();
        }
        return $handler;
    }

    protected function tearDown()
    {
        m::close();
    }
}
