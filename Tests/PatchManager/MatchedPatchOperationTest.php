<?php


namespace PatchManager;


use Cypress\PatchManagerBundle\PatchManager\MatchedPatchOperation;
use Cypress\PatchManagerBundle\Tests\PatchManagerTestCase;

class MatchedPatchOperationTest extends PatchManagerTestCase
{
    public function test_matchFor()
    {
        $mpo = MatchedPatchOperation::create(array(), $this->mockHandler('data'));
        $this->assertTrue($mpo->matchFor('data'));
        $this->assertFalse($mpo->matchFor('method'));
    }
} 