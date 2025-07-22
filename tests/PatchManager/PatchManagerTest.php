<?php

declare(strict_types=1);

namespace Cypress\PatchManager\Tests;

use Cypress\PatchManager\Event\PatchManagerEvent;
use Cypress\PatchManager\Exception\HandlerNotFoundException;
use Cypress\PatchManager\MatchedPatchOperation;
use Cypress\PatchManager\OperationMatcher;
use Cypress\PatchManager\PatchManager;
use Cypress\PatchManager\PatchOperationHandler;
use Cypress\PatchManager\Request\Adapter;
use Cypress\PatchManager\Request\Operations;
use Cypress\PatchManager\Tests\FakeObjects\SubjectA;
use PhpCollection\Sequence;
use PHPUnit\Framework\Attributes\Test;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PatchManagerTest extends PatchManagerTestCase
{
    /**
     * @var ObjectProphecy<OperationMatcher>
     */
    private ObjectProphecy $operationMatcher;

    /**
     * @var ObjectProphecy<EventDispatcherInterface>
     */
    private ObjectProphecy $eventDispatcher;

    private PatchManager $patchManager;

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

    #[Test]
    public function strictMode(): void
    {
        $this->expectException(HandlerNotFoundException::class);
        $this->expectExceptionMessage("test");
        $this->operationMatcher->getUnmatchedOperations(Argument::any())->willReturn(new Sequence(['test']));
        $pm = new PatchManager($this->operationMatcher->reveal(), true);
        $pm->handle(new SubjectA());
    }

    #[Test]
    public function gtrictModeMultipleOps(): void
    {
        $this->expectException(HandlerNotFoundException::class);
        $this->expectExceptionMessage("test, test2");
        $this->operationMatcher->getUnmatchedOperations(Argument::any())->willReturn(new Sequence(['test', 'test2']));
        $pm = new PatchManager($this->operationMatcher->reveal(), true);
        $pm->handle(new SubjectA());
    }

    #[Test]
    public function arraySubject(): void
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

    #[Test]
    public function sequenceSubject(): void
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

    #[Test]
    public function handlerThrowsAnExceptionAndSendEvents(): void
    {
        $handler = $this->prophesize(PatchOperationHandler::class);
        $handler->configureOptions(Argument::any())->shouldBeCalled();
        $handler->getName()->willReturn('data');
        $handler->handle(Argument::any(), Argument::any())
            ->willThrow(\LogicException::class)
            ->shouldBeCalled();
        $handler->canHandle(Argument::type(SubjectA::class))->willReturn(true);

        $requestAdapter = $this->prophesize(Adapter::class);
        $requestAdapter->getRequestBody()->willReturn('{"op": "data"}');
        $operations = new Operations($requestAdapter->reveal());

        $operationMatcher = new OperationMatcher($operations);
        $operationMatcher->addHandler($handler->reveal());

        $mockEventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $mockEventDispatcher->dispatch(
            Argument::type(PatchManagerEvent::class),
            Argument::type('string')
        )->shouldBeCalledTimes(4);

        $pm = new PatchManager($operationMatcher, true);
        $pm->setEventDispatcherInterface(
            $mockEventDispatcher->reveal()
        );

        try {
            $pm->handle(new SubjectA());
        } catch (\LogicException $e) {
            $this->assertEquals(\LogicException::class, $e::class);
        }
    }
}
