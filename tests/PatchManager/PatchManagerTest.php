<?php

namespace Cypress\PatchManager\Tests;

use Cypress\PatchManager\Event\PatchManagerEvent;
use Cypress\PatchManager\Exception\HandlerNotFoundException;
use Cypress\PatchManager\MatchedPatchOperation;
use Cypress\PatchManager\OperationMatcher;
use Cypress\PatchManager\PatchManager;
use Cypress\PatchManager\Tests\FakeObjects\SubjectA;
use PhpCollection\Sequence;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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

    public function setUp(): void
    {
        parent::setUp();
        $this->operationMatcher = $this->prophesize(OperationMatcher::class);
        $this->operationMatcher->getMatchedOperations(Argument::any())
            ->willReturn(new Sequence());
        $this->operationMatcher->getUnmatchedOperations(Argument::any())
            ->willReturn(new Sequence());
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->patchManager = new PatchManager($this->operationMatcher->reveal());
        $this->patchManager->setEventDispatcherInterface($this->eventDispatcher->reveal());
    }

    public function testStrictMode(): void
    {
        $this->expectException(HandlerNotFoundException::class);
        $this->expectExceptionMessage("test");
        $this->operationMatcher->getUnmatchedOperations(Argument::any())->willReturn(new Sequence(['test']));
        $pm = new PatchManager($this->operationMatcher->reveal(), true);
        $pm->handle(new SubjectA());
    }

    public function testStrictModeMultipleOps(): void
    {
        $this->expectException(HandlerNotFoundException::class);
        $this->expectExceptionMessage("test, test2");
        $this->operationMatcher->getUnmatchedOperations(Argument::any())->willReturn(new Sequence(['test', 'test2']));
        $pm = new PatchManager($this->operationMatcher->reveal(), true);
        $pm->handle(new SubjectA());
    }

    public function testArraySubject(): void
    {
        $handler = $this->mockHandler('data');
        $handler->handle(Argument::any(), Argument::any())->shouldBeCalled();
        $operation = MatchedPatchOperation::create(['op' => 'data'], $handler->reveal());
        $this->operationMatcher->getMatchedOperations(Argument::any())
            ->willReturn(new Sequence([$operation]));

        $event = $this->prophesize(PatchManagerEvent::class);
        $mockEventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $mockEventDispatcher->dispatch(
            Argument::type(PatchManagerEvent::class),
            Argument::type('string')
        )->willReturn($event->reveal(), $event->reveal(), $event->reveal(), $event->reveal(), $event->reveal());

        $pm = new PatchManager($this->operationMatcher->reveal(), true);
        $pm->setEventDispatcherInterface($mockEventDispatcher->reveal());

        $pm->handle([new SubjectA(), new FakeObjects\SubjectB()]);
    }

    public function testSequenceSubject(): void
    {
        $handler = $this->mockHandler('data');
        $handler->handle(Argument::any(), Argument::any())->shouldBeCalled();
        $operation = MatchedPatchOperation::create(['op' => 'data'], $handler->reveal());
        $this->operationMatcher->getMatchedOperations(Argument::any())
            ->willReturn(new Sequence([$operation]));

        $event = $this->prophesize(PatchManagerEvent::class);
        $mockEventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $mockEventDispatcher->dispatch(
            Argument::type(PatchManagerEvent::class),
            Argument::type('string')
        )->willReturn(
            $event->reveal(),
            $event->reveal(),
            $event->reveal(),
            $event->reveal()
        );

        $pm = new PatchManager($this->operationMatcher->reveal(), true);
        $pm->setEventDispatcherInterface(
            $mockEventDispatcher->reveal()
        );

        $pm->handle(new Sequence([new SubjectA(), new FakeObjects\SubjectB()]));
    }
}
