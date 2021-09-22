<?php

namespace Cypress\PatchManager\Tests;

use Cypress\PatchManager\MatchedPatchOperation;
use Cypress\PatchManager\Tests\FakeObjects\SubjectA;
use Prophecy\Argument;

class MatchedPatchOperationTest extends PatchManagerTestCase
{
    public function testMatchFor(): void
    {
        $mpo = MatchedPatchOperation::create([], $this->mockHandler('data')->reveal());
        $this->assertTrue($mpo->matchFor('data'));
        $this->assertFalse($mpo->matchFor('method'));
    }

    public function testProcess(): void
    {
        $handler = $this->mockHandler('data');
        $handler->handle(Argument::any(), Argument::any())->shouldBeCalled();
        $mpo = MatchedPatchOperation::create(['op' => 'data'], $handler->reveal());
        $mpo->process(new SubjectA());
    }
}
