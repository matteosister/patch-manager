<?php

namespace Cypress\PatchManager\Tests\Bundle\RequestAdapter;

use Cypress\PatchManager\Bundle\RequestAdapter\RequestStackAdapter;
use Cypress\PatchManager\Request\Operations;
use Cypress\PatchManager\Tests\PatchManagerTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestStackAdapterTest extends PatchManagerTestCase
{
    public function testCall(): void
    {
        $currentRequest = $this->prophesize(Request::class);
        $currentRequest->getContent()->willReturn('{"op":"data"}');
        $requestStack = $this->prophesize(RequestStack::class);
        $requestStack->getCurrentRequest()->willReturn($currentRequest->reveal());
        $adapter = new RequestStackAdapter($requestStack->reveal());

        $operations = new Operations($adapter);

        $this->assertCount(1, $operations->all());

        /** @var array $first */
        $first = $operations->all()->get(0);
        $this->assertEquals('data', $first['op']);
    }
}
