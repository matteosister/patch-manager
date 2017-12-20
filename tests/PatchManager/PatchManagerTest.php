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
use Prophecy\Argument;

class PatchManagerTest extends PatchManagerTestCase
{
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy
     */
    private $operationMatcher;

    /**
     * @var \Prophecy\Prophecy\ObjectProphecy
     */
    private $eventDispatcher;

    /**
     * @var PatchManager
     */
    private $patchManager;

    public function setUp()
    {
        parent::setUp();
        $this->operationMatcher = $this->prophesize('Cypress\PatchManager\OperationMatcher');
        $this->operationMatcher->getMatchedOperations(Argument::any())
            ->willReturn(new Sequence());
        $this->operationMatcher->getUnmatchedOperations(Argument::any())
            ->willReturn(new Sequence());
        $this->eventDispatcher = $this->prophesize('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->patchManager = new PatchManager($this->operationMatcher->reveal());
        $this->patchManager->setEventDispatcherInterface($this->eventDispatcher->reveal());
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function test_handle_without_required_keys()
    {
        $this->eventDispatcher->dispatch(Argument::containingString("patch_manager."), Argument::any())->shouldBeCalled();
        $mpo = MatchedPatchOperation::create(array('op' => 'data', 'property' => 'a'), new DataHandler());
        $this->operationMatcher->getMatchedOperations(Argument::any())
            ->willReturn(new Sequence(array($mpo)));
        $this->patchManager->handle(new SubjectA());
    }

    /**
     * @expectedException \Cypress\PatchManager\Exception\HandlerNotFoundException
     * @expectedExceptionMessage 'test'
     */
    public function test_strict_mode()
    {
        $this->operationMatcher->getUnmatchedOperations(Argument::any())->willReturn(new Sequence(array('test')));
        $pm = new PatchManager($this->operationMatcher->reveal(), true);
        $pm->handle(new SubjectA());
    }

    /**
     * @expectedException \Cypress\PatchManager\Exception\HandlerNotFoundException
     * @expectedExceptionMessage 'test, test2'
     */
    public function test_strict_mode_multiple_ops()
    {
        $this->operationMatcher->getUnmatchedOperations(Argument::any())->willReturn(new Sequence(array('test', 'test2')));
        $pm = new PatchManager($this->operationMatcher->reveal(), true);
        $pm->handle(new SubjectA());
    }

    public function test_array_subject()
    {
        $handler = $this->mockHandler('data');
        $handler->handle(Argument::any(), Argument::any())->shouldBeCalled()->willReturn();
        $operation = MatchedPatchOperation::create(array('op' => 'data'), $handler->reveal());
        $this->operationMatcher->getMatchedOperations(Argument::any())
            ->willReturn(new Sequence(array($operation)));
        $pm = new PatchManager($this->operationMatcher->reveal(), true);
        $mockEventDispatcher = $this->prophesize('\Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $mockEventDispatcher->dispatch()->willReturn();
        $pm->setEventDispatcherInterface($mockEventDispatcher);
        $pm->handle(array(new SubjectA(), new SubjectB()));
    }

    public function test_sequence_subject()
    {
        $handler = $this->mockHandler('data');
        $handler->handle(Argument::any(), Argument::any())->shouldBeCalled()->willReturn();
        $operation = MatchedPatchOperation::create(array('op' => 'data'), $handler->reveal());
        $this->operationMatcher->getMatchedOperations(Argument::any())
            ->willReturn(new Sequence(array($operation)));
        $pm = new PatchManager($this->operationMatcher->reveal(), true);
        $mockEventDispatcher = $this->prophesize('\Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $mockEventDispatcher->dispatch()->willReturn();
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