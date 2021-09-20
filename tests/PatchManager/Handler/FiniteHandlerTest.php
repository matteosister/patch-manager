<?php

namespace Cypress\PatchManager\Tests\Handler;

use Cypress\PatchManager\Handler\FiniteHandler;
use Cypress\PatchManager\Tests\Handler\FakeObjects\FiniteSubject;
use Finite\Exception\StateException;
use Finite\State\State;
use Finite\State\StateInterface;
use Finite\StateMachine\StateMachine;
use Cypress\PatchManager\OperationData;
use Cypress\PatchManager\Tests\PatchManagerTestCase;
use Mockery as m;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiniteHandlerTest extends PatchManagerTestCase
{
    /**
     * @var FiniteHandler
     */
    private $handler;

    /**
     * @var StateMachine
     */
    private $sm;

    public function setUp(): void
    {
        parent::setUp();
        $this->sm = new StateMachine();

        // Define states
        $this->sm->addState(new State('s1', StateInterface::TYPE_INITIAL));
        $this->sm->addState('s2');
        $this->sm->addState(new State('s3', StateInterface::TYPE_FINAL));

        $finiteFactory = m::mock('Finite\Factory\FactoryInterface');
        $finiteFactory->shouldReceive('get')->andReturn($this->sm);

        $this->handler = new FiniteHandler($finiteFactory);
    }

    public function test_getName()
    {
        $this->assertEquals('sm', $this->handler->getName());
    }

    public function test_handle_ok()
    {
        $patchable = new FiniteSubject();
        $this->sm->setObject($patchable);
        $this->sm->initialize();

        // Define transitions
        $this->sm->addTransition('t12', 's1', 's2');
        $this->sm->addTransition('t23', 's2', 's3');

        $this->handler->handle(
            $patchable,
            new OperationData(
                ['op' => 'finite', 'transition' => 't12', 'check' => false]
            )
        );
        $this->assertEquals('s2', $patchable->getFiniteState());
    }

    public function test_handle_with_exception()
    {
        $this->expectException(StateException::class);
        $patchable = new FiniteSubject();
        $this->sm->setObject($patchable);
        $this->sm->initialize();

        // Define transitions
        $this->sm->addTransition('t12', 's1', 's2');
        $this->sm->addTransition('t23', 's2', 's3');

        $this->handler->handle(
            $patchable,
            new OperationData(
                array('op' => 'finite', 'transition' => 't23', 'check' => false)
            )
        );
    }

    public function test_handle_wrong_with_check()
    {
        $patchable = new FiniteSubject();
        $this->sm->setObject($patchable);
        $this->sm->initialize();

        // Define transitions
        $this->sm->addTransition('t12', 's1', 's2');
        $this->sm->addTransition('t23', 's2', 's3');

        $this->handler->handle(
            $patchable,
            new OperationData(
                ['op' => 'finite', 'transition' => 't23', 'check' => true]
            )
        );
        $this->assertEquals('s1', $patchable->getFiniteState());
    }

    public function test_configureOptions()
    {
        $or = new OptionsResolver();
        $this->handler->configureOptions($or);
        $this->assertContains('transition', $or->getDefinedOptions());
        $this->assertContains('check', $or->getDefinedOptions());
        $this->assertEquals(array('transition'), $or->getRequiredOptions());
        $this->assertTrue($or->hasDefault('check'));
    }
}
