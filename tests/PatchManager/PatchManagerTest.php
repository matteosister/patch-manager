<?php

namespace PatchManager\Tests;

use PatchManager\Handler\DataHandler;
use PatchManager\MatchedPatchOperation;
use PatchManager\OperationData;
use PatchManager\Patchable as IPatchable;
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
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function test_handle_without_required_keys()
    {
        $mpo = MatchedPatchOperation::create(array('op' => 'data', 'property' => 'a'), new DataHandler());
        $this->operationMatcher->shouldReceive('getMatchedOperations')
            ->andReturn(new Sequence(array($mpo)))->byDefault();
        $this->patchManager->handle(new SubjectA());
    }
}

class SubjectA implements IPatchable
{
    private $a = 1;
}