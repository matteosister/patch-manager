<?php

namespace Cypress\PatchManager\Tests;

use Cypress\PatchManager\Exception\HandlerNotFoundException;
use Cypress\PatchManager\Handler\DataHandler;
use Cypress\PatchManager\MatchedPatchOperation;
use Cypress\PatchManager\OperationData;
use Cypress\PatchManager\OperationMatcher;
use Cypress\PatchManager\Patchable as IPatchable;
use Cypress\PatchManager\PatchManager;
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
        parent::setUp();
        $this->operationMatcher = m::mock('PatchManager\OperationMatcher');
        $this->operationMatcher->shouldReceive('getMatchedOperations')
            ->andReturn(new Sequence())->byDefault();
        $this->operationMatcher->shouldReceive('getUnmatchedOperations')
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
     * @expectedException HandlerNotFoundException
     * @expectedExceptionMessage 'test'
     */
    public function test_strict_mode()
    {
        $this->operationMatcher->shouldReceive('getUnmatchedOperations')->andReturn(new Sequence(array('test')));
        $pm = new PatchManager($this->operationMatcher, true);
        $pm->handle(new SubjectA());
    }

    /**
     * @expectedException HandlerNotFoundException
     * @expectedExceptionMessage 'test, test2'
     */
    public function test_strict_mode_multiple_ops()
    {
        $this->operationMatcher->shouldReceive('getUnmatchedOperations')->andReturn(new Sequence(array('test', 'test2')));
        $pm = new PatchManager($this->operationMatcher, true);
        $pm->handle(new SubjectA());
    }

    public function test_array_subject()
    {
        $handler = $this->mockHandler('data');
        $handler->shouldReceive('handle')->twice()->andReturn();
        $operation = MatchedPatchOperation::create(array('op' => 'data'), $handler);
        $this->operationMatcher->shouldReceive('getMatchedOperations')
            ->andReturn(new Sequence(array($operation)));
        $pm = new PatchManager($this->operationMatcher, true);
        $mockEventDispatcher = m::mock('\Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $mockEventDispatcher->shouldReceive('dispatch')->times(8)->andReturn();
        $pm->setEventDispatcherInterface($mockEventDispatcher);
        $pm->handle(array(new SubjectA(), new SubjectB()));
    }

    public function test_sequence_subject()
    {
        $handler = $this->mockHandler('data');
        $handler->shouldReceive('handle')->twice()->andReturn();
        $operation = MatchedPatchOperation::create(array('op' => 'data'), $handler);
        $this->operationMatcher->shouldReceive('getMatchedOperations')
            ->andReturn(new Sequence(array($operation)));
        $pm = new PatchManager($this->operationMatcher, true);
        $mockEventDispatcher = m::mock('\Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $mockEventDispatcher->shouldReceive('dispatch')->times(8)->andReturn();
        $pm->setEventDispatcherInterface($mockEventDispatcher);
        $pm->handle(new Sequence(array(new SubjectA(), new SubjectB())));
    }
}

class SubjectA implements IPatchable
{
    private $a = 1;
}

class SubjectB implements IPatchable
{
    private $a = 1;
}