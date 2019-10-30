<?php

namespace Cypress\PatchManager\Bundle\RequestAdapter;

use Cypress\PatchManager\Request\Operations;
use Cypress\PatchManager\Tests\PatchManagerTestCase;
use Mockery as m;

class RequestStackAdapterTest extends PatchManagerTestCase
{
    public function test_call()
    {
        $currentRequest = $this->prophesize('Symfony\Component\HttpFoundation\Request');
        $currentRequest->getContent()->willReturn('{"op":"data"}');
        $requestStack = $this->prophesize('Symfony\Component\HttpFoundation\RequestStack');
        $requestStack->getCurrentRequest()->willReturn($currentRequest->reveal());
        $adapter = new RequestStackAdapter($requestStack->reveal());

        $operations = new Operations($adapter);

        $this->assertCount(1, $operations->all());

        $first = $operations->all()->get(0);
        $this->assertEquals('data', $first['op']);
    }
}
