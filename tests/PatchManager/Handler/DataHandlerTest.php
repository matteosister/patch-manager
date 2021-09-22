<?php

namespace Cypress\PatchManager\Tests\Handler;

use Cypress\PatchManager\Handler\DataHandler;
use Cypress\PatchManager\OperationData;
use Cypress\PatchManager\Tests\Handler\FakeObjects\DataSubject;
use Cypress\PatchManager\Tests\PatchManagerTestCase;

class DataHandlerTest extends PatchManagerTestCase
{
    /**
     * @var DataHandler
     */
    private DataHandler $handler;

    public function setUp(): void
    {
        parent::setUp();
        $this->handler = new DataHandler();
    }

    public function testGetName(): void
    {
        $this->assertEquals('data', $this->handler->getName());
    }

    public function testHandle(): void
    {
        $subject = new DataSubject();
        $this->assertNull($subject->getA());
        $this->handler->handle($subject, new OperationData(['op' => 'data', 'property' => 'a', 'value' => 1]));
        $this->assertEquals(1, $subject->getA());
    }

    public function testHandleWithMagicCall(): void
    {
        $this->handler->useMagicCall(true);
        $subject = new DataSubject();
        $this->assertNull($subject->getB());
        $this->handler->handle($subject, new OperationData(['op' => 'data', 'property' => 'b', 'value' => 1]));
        $this->assertEquals(1, $subject->getB());
    }
}
