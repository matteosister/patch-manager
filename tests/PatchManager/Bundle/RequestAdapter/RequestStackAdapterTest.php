<?php

namespace PatchManager\Bundle\RequestAdapter;

use PatchManager\Tests\PatchManagerTestCase;
use Mockery as m;

class RequestStackAdapterTest extends PatchManagerTestCase
{
    /**
     * @var m\MockInterface
     */
    private $currentRequest;

    /**
     * @var RequestStackAdapter
     */
    private $adapter;

    public function setUp()
    {
        $requestStack = m::mock('Symfony\Component\HttpFoundation\RequestStack');
        $this->currentRequest = m::mock('Symfony\Component\HttpFoundation\Request');
        $this->currentRequest->shouldReceive('isMethod')->with('PATCH')->andReturn(true)->byDefault();
        $requestStack->shouldReceive('getCurrentRequest')->andReturn($this->currentRequest);

        $this->adapter = new RequestStackAdapter($requestStack);
    }

    public function test_call()
    {

    }
} 