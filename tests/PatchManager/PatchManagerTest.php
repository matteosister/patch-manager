<?php

namespace PatchManager\Tests;

use PatchManager\Handler\DataHandler;
use PatchManager\MatchedPatchOperation;
use PatchManager\OperationData;
use PatchManager\Patchable;
use PatchManager\PatchManager;
use PhpCollection\Sequence;
use Mockery as m;

class PatchManagerTest extends PatchManagerTestCase
{
    /**
     * @var m\MockInterface
     */
    private $operationMatcher;

    /**
     * @var PatchManager
     */
    private $patchManager;

    public function setUp()
    {
        $this->operationMatcher = m::mock('PatchManager\OperationMatcher');
        $this->operationMatcher->shouldReceive('getMatchedOperations')
            ->andReturn(new Sequence())->byDefault();
        $this->patchManager = new PatchManager($this->operationMatcher);
    }

    /**
     * @expectedException \PatchManager\Exception\MissingKeysRequest
     */
    public function test_handle_without_required_keys()
    {
        $mpo = MatchedPatchOperation::create(new OperationData(array('op' => 'data', 'property' => 'a')), new DataHandler());
        $this->operationMatcher->shouldReceive('getMatchedOperations')
            ->andReturn(new Sequence(array($mpo)))->byDefault();
        $this->patchManager->handle(new SubjectA());
    }
}

class SubjectA implements Patchable
{
    private $a = 1;
}