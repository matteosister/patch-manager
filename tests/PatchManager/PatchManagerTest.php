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
     * @var m\MockInterface
     */
    private $eventDispatcher;

    /**
     * @var PatchManager
     */
    private $patchManager;

    public function setUp()
    {
        $this->operationMatcher = m::mock('PatchManager\OperationMatcher');
        $this->operationMatcher->shouldReceive('getMatchedOperations')
            ->andReturn(new Sequence())->byDefault();
        $this->eventDispatcher = m::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->patchManager = new PatchManager($this->operationMatcher);
        $this->patchManager->setEventDispatcherInterface($this->eventDispatcher);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function test_handle_without_required_keys()
    {
        $this->eventDispatcher->shouldReceive('dispatch')->twice()->andReturn();
        $mpo = MatchedPatchOperation::create(array('op' => 'data', 'property' => 'a'), new DataHandler());
        $this->operationMatcher->shouldReceive('getMatchedOperations')
            ->andReturn(new Sequence(array($mpo)))->byDefault();
        $this->patchManager->handle(new SubjectA());
    }

    /**
     * @expectedException \PatchManager\Exception\HandlerNotFoundException
     * @expectedExceptionMessage 'test'
     */
    public function test_strict_mode()
    {
        $this->operationMatcher->shouldReceive('getUnmatchedOperations')->andReturn(new Sequence(array('test')));
        $pm = new PatchManager($this->operationMatcher, true);
        $pm->handle(new SubjectA());
    }

    /**
     * @expectedException \PatchManager\Exception\HandlerNotFoundException
     * @expectedExceptionMessage 'test, test2'
     */
    public function test_strict_mode_multiple_ops()
    {
        $this->operationMatcher->shouldReceive('getUnmatchedOperations')->andReturn(new Sequence(array('test', 'test2')));
        $pm = new PatchManager($this->operationMatcher, true);
        $pm->handle(new SubjectA());
    }
}

class SubjectA implements IPatchable
{
    private $a = 1;
}