<?php

namespace Cypress\PatchManagerBundle\Tests\PatchManager;

use Cypress\PatchManagerBundle\PatchManager\Handler\DataHandler;
use Cypress\PatchManagerBundle\PatchManager\MatchedPatchOperation;
use Cypress\PatchManagerBundle\PatchManager\Patchable;
use Cypress\PatchManagerBundle\PatchManager\PatchManager;
use Cypress\PatchManagerBundle\Tests\PatchManagerTestCase;
use Mockery as m;
use PhpCollection\Sequence;

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
        $this->operationMatcher = m::mock('Cypress\PatchManagerBundle\PatchManager\OperationMatcher');
        $this->operationMatcher->shouldReceive('getMatchedOperations')
            ->andReturn(new Sequence())->byDefault();
        $this->patchManager = new PatchManager($this->operationMatcher);
    }

    /**
     * @expectedException \Cypress\PatchManagerBundle\Exception\MissingKeysRequest
     */
    public function test_handle_without_required_keys()
    {
        $mpo = MatchedPatchOperation::create(array('op' => 'data', 'property' => 'a'), new DataHandler());
        $this->operationMatcher->shouldReceive('getMatchedOperations')
            ->andReturn(new Sequence(array($mpo)))->byDefault();
        $this->patchManager->handle(new SubjectA());
    }
}

class SubjectA implements Patchable
{
    private $a = 1;
}