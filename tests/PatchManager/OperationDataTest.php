<?php

namespace Cypress\PatchManager\Tests;

use Cypress\PatchManager\OperationData;
use PhpCollection\Sequence;

class OperationDataTest extends PatchManagerTestCase
{
    public function test_getOp_with_empty_data()
    {
        $od = new OperationData();
        $this->assertNull($od->getOp()->getOrElse(null));
    }

    public function test_getOp_with_data()
    {
        $od = new OperationData(array('op' => 'data'));
        $this->assertEquals('data', $od->getOp()->getOrElse(null));
    }

    public function test_getData_with_empty_data()
    {
        $od = new OperationData();
        $this->assertTrue($od->getData()->isEmpty());
    }

    public function test_getData_with_op_only()
    {
        $od = new OperationData(array('op' => 'data'));
        $this->assertTrue($od->getData()->isEmpty());
    }

    public function test_getData_with_data()
    {
        $od = new OperationData(array('op' => 'data', 'test' => 1, 'test2' => '2'));
        $this->assertFalse($od->getData()->isEmpty());
        $this->assertInstanceOf('PhpCollection\Map', $od->getData());
        $this->assertCount(2, $od->getData());
        $this->assertContains('test', $od->getData()->keys());
        $this->assertContains('test2', $od->getData()->keys());
        $this->assertSame(1, $od->getData()->get('test')->getOrElse(null));
        $this->assertSame('2', $od->getData()->get('test2')->getOrElse(null));
    }

    /**
     * @param $expected
     * @param $requiredKeys
     *
     * @dataProvider diffKeysProvider
     */
    public function test_diffKeys($expected, $requiredKeys)
    {
        $od = new OperationData(array('op' => 'data', 'test' => 1, 'test2' => '2'));
        $this->assertEquals($expected, $od->diffKeys($requiredKeys));
    }

    public function diffKeysProvider(): array
    {
        return [
            [new Sequence(), ['test', 'test2']],
            [new Sequence(['test']), ['test2']],
            [new Sequence(['test']), ['test2']],
        ];
    }
}