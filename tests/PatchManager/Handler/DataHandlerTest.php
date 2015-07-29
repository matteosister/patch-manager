<?php


namespace Cypress\PatchManager\Handler;

use Cypress\PatchManager\OperationData;
use Cypress\PatchManager\Patchable;
use Cypress\PatchManager\Tests\PatchManagerTestCase;

class DataHandlerTest extends PatchManagerTestCase
{
    /**
     * @var DataHandler
     */
    private $handler;

    public function setUp()
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

class DataSubject implements Patchable
{
    private $a;

    private $b;

    public function setA($v)
    {
        $this->a = $v;
    }

    public function getA()
    {
        return $this->a;
    }

    public function getB()
    {
        return $this->b;
    }

    public function __call($method, $args)
    {
        if ('setB' === $method) {
            $this->b = $args[0];
        }
    }
}