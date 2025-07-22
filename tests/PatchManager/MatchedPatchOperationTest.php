<?php

declare(strict_types=1);

namespace Cypress\PatchManager\Tests;

use Cypress\PatchManager\MatchedPatchOperation;
use Cypress\PatchManager\Tests\FakeObjects\SubjectA;
use PHPUnit\Framework\Attributes\Test;
use Prophecy\Argument;

class MatchedPatchOperationTest extends PatchManagerTestCase
{
    #[Test]
    public function matchFor(): void
    {
        $mpo = MatchedPatchOperation::create([], $this->mockHandler('data')->reveal());
        $this->assertTrue($mpo->matchFor('data'));
        $this->assertFalse($mpo->matchFor('method'));
    }

    #[Test]
    public function process(): void
    {
        $handler = $this->mockHandler('data');
        $handler->handle(Argument::any(), Argument::any())->shouldBeCalled();
        $mpo = MatchedPatchOperation::create(['op' => 'data'], $handler->reveal());
        $mpo->process(new SubjectA());
    }
}
