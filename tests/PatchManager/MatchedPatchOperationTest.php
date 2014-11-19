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
} 