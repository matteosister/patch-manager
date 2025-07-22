<?php

declare(strict_types=1);

namespace Cypress\PatchManager\Tests\Handler;

use Cypress\PatchManager\Handler\FiniteHandler;
use Cypress\PatchManager\OperationData;
use Cypress\PatchManager\Tests\Handler\FakeObjects\FiniteSubject;
use Cypress\PatchManager\Tests\PatchManagerTestCase;
use Finite\Exception\StateException;
use Finite\State\State;
use Finite\State\StateInterface;
use Finite\StateMachine\StateMachine;
use Mockery as m;
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

        // Define states
        $this->stateMachine->addState(new State('s1', StateInterface::TYPE_INITIAL));
        $this->stateMachine->addState('s2');
        $this->stateMachine->addState(new State('s3', StateInterface::TYPE_FINAL));

        $finiteFactory = m::mock('Finite\Factory\FactoryInterface');
        $finiteFactory->shouldReceive('get')->andReturn($this->stateMachine);

        $this->handler = new FiniteHandler($finiteFactory);
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
        $this->stateMachine->setObject($patchable);
        $this->stateMachine->initialize();

        // Define transitions
        $this->stateMachine->addTransition('t12', 's1', 's2');
        $this->stateMachine->addTransition('t23', 's2', 's3');

        $this->handler->handle(
            $patchable,
            new OperationData(
                ['op' => 'finite', 'transition' => 't12', 'check' => false]
            )
        );
        $this->assertEquals('s2', $patchable->getFiniteState());
    }

    #[Test]
    public function handleShouldReturnAnException(): void
    {
        $this->expectException(StateException::class);
        $patchable = new FiniteSubject();
        $this->stateMachine->setObject($patchable);
        $this->stateMachine->initialize();

        // Define transitions
        $this->stateMachine->addTransition('t12', 's1', 's2');
        $this->stateMachine->addTransition('t23', 's2', 's3');

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
        $this->stateMachine->setObject($patchable);
        $this->stateMachine->initialize();

        // Define transitions
        $this->stateMachine->addTransition('t12', 's1', 's2');
        $this->stateMachine->addTransition('t23', 's2', 's3');

        $this->handler->handle(
            $patchable,
            new OperationData(
                ['op' => 'finite', 'transition' => 't23', 'check' => true]
            )
        );
        $this->assertEquals('s1', $patchable->getFiniteState());
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
