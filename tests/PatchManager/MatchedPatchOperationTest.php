<?php

namespace Cypress\PatchManager\Tests;

use Cypress\PatchManager\MatchedPatchOperation;
use Cypress\PatchManager\OperationData;

class MatchedPatchOperationTest extends PatchManagerTestCase
{
    public function test_matchFor()
    {
        $mpo = MatchedPatchOperation::create(array(), $this->mockHandler('data'));
        $this->assertTrue($mpo->matchFor('data'));
        $this->assertFalse($mpo->matchFor('method'));
    }

    public function test_process()
    {
        $handler = $this->mockHandler('data');
        $handler->shouldReceive('handle')->once();
        $mpo = MatchedPatchOperation::create(array('op' => 'data'), $handler);
        $mpo->process(new Patchable());
    }
}

class Patchable implements \Cypress\PatchManager\Patchable
{
    private $a = 1;
}