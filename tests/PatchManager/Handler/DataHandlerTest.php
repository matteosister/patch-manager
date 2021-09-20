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
    private $handler;

    public function setUp(): void
    {
        parent::setUp();
        $this->handler = new DataHandler();
    }

    public function test_getName()
    {
        $this->assertEquals('data', $this->handler->getName());
    }

    public function test_handle()
    {
        $subject = new DataSubject();
        $this->assertNull($subject->getA());
        $this->handler->handle($subject, new OperationData(array('op' => 'data', 'property' => 'a', 'value' => 1)));
        $this->assertEquals(1, $subject->getA());
    }

    public function test_handle_with_magic_call()
    {
        $this->handler->useMagicCall(true);
        $subject = new DataSubject();
        $this->assertNull($subject->getB());
        $this->handler->handle($subject, new OperationData(array('op' => 'data', 'property' => 'b', 'value' => 1)));
        $this->assertEquals(1, $subject->getB());
    }
}
