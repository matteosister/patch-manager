<?php

declare(strict_types=1);

namespace Cypress\PatchManager\Tests\Handler;

use Cypress\PatchManager\Handler\FiniteHandler;
use Cypress\PatchManager\OperationData;
use Cypress\PatchManager\Tests\Handler\FakeObjects\FiniteSubject;
use Cypress\PatchManager\Tests\Handler\FakeObjects\FiniteSubjectState;
use Cypress\PatchManager\Tests\PatchManagerTestCase;
use Finite\Exception\TransitionNotReachableException;
use Finite\StateMachine;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiniteHandlerTest extends PatchManagerTestCase
{
    private FiniteHandler $handler;

    private StateMachine $stateMachine;

    public function setUp(): void
    {
        parent::setUp();
        $this->stateMachine = new StateMachine();

        $this->handler = new FiniteHandler($this->stateMachine);
    }

    #[Test]
    public function getNameShouldReturnTheHandlerIdentifier(): void
    {
        $this->assertEquals('sm', $this->handler->getName());
    }

    #[Test]
    public function handleShouldSucceed(): void
    {
        $patchable = new FiniteSubject();
        $this->handler->handle(
            $patchable,
            new OperationData(
                ['op' => 'finite', 'transition' => 't12', 'check' => false]
            )
        );
        $this->assertSame(FiniteSubjectState::S2, $patchable->getFiniteState());
    }

    #[Test]
    public function handleShouldReturnAnException(): void
    {
        $this->expectException(TransitionNotReachableException::class);
        $patchable = new FiniteSubject();

        $this->handler->handle(
            $patchable,
            new OperationData(
                ['op' => 'finite', 'transition' => 't23', 'check' => false]
            )
        );
    }

    #[Test]
    public function handleWithWrongCheck(): void
    {
        $patchable = new FiniteSubject();

        $this->handler->handle(
            $patchable,
            new OperationData(
                ['op' => 'finite', 'transition' => 't23', 'check' => true]
            )
        );
        $this->assertSame(FiniteSubjectState::S1, $patchable->getFiniteState());
    }

    #[Test]
    public function shouldConfigureOptions(): void
    {
        $or = new OptionsResolver();
        $this->handler->configureOptions($or);
        $this->assertContains('transition', $or->getDefinedOptions());
        $this->assertContains('check', $or->getDefinedOptions());
        $this->assertEquals(['transition'], $or->getRequiredOptions());
        $this->assertTrue($or->hasDefault('check'));
    }
}
