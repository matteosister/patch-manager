<?php

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
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiniteHandlerTest extends PatchManagerTestCase
{
    /**
     * @var FiniteHandler
     */
    private FiniteHandler $handler;

    /**
     * @var StateMachine
     */
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

    public function testGetName(): void
    {
        $this->assertEquals('sm', $this->handler->getName());
    }

    public function testHandleOk(): void
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

    public function testHandleWithException(): void
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

    public function testHandleWrongWithCheck(): void
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

    public function testConfigureOptions(): void
    {
        $or = new OptionsResolver();
        $this->handler->configureOptions($or);
        $this->assertContains('transition', $or->getDefinedOptions());
        $this->assertContains('check', $or->getDefinedOptions());
        $this->assertEquals(['transition'], $or->getRequiredOptions());
        $this->assertTrue($or->hasDefault('check'));
    }
}
