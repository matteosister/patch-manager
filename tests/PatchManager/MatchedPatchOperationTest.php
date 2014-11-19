<?php

namespace PatchManager\Tests;

use PatchManager\MatchedPatchOperation;
use PatchManager\OperationData;

class MatchedPatchOperationTest extends PatchManagerTestCase
{
    public function test_matchFor()
    {
        $mpo = MatchedPatchOperation::create(new OperationData(), $this->mockHandler('data'));
        $this->assertTrue($mpo->matchFor('data'));
        $this->assertFalse($mpo->matchFor('method'));
    }

    public function test_process()
    {
        $handler = $this->mockHandler('data');
        $handler->shouldReceive('handle')->once();
        $mpo = MatchedPatchOperation::create(new OperationData(), $handler);
        $mpo->process(new Patchable());
    }
}

class Patchable implements \PatchManager\Patchable
{
    private $a = 1;
}