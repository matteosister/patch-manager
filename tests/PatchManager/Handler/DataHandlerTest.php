<?php


namespace PatchManager\Handler;


use PatchManager\Tests\PatchManagerTestCase;

class DataHandlerTest extends PatchManagerTestCase
{
    public function test_getName()
    {
        $handler = new DataHandler();
        $this->assertEquals('data', $handler->getName());
    }

    public function test_getRequiredKeys()
    {
        $handler = new DataHandler();
        $this->assertEquals(array('property', 'value'), $handler->getRequiredKeys());
    }
} 