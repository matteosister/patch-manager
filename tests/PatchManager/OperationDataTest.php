<?php

declare(strict_types=1);

namespace Cypress\PatchManager\Tests;

use Cypress\PatchManager\OperationData;
use PhpCollection\Sequence;
use PHPUnit\Framework\Attributes\DataProvider;

class OperationDataTest extends PatchManagerTestCase
{
    public function testGetOpWithEmptyData(): void
    {
        $od = new OperationData();
        $this->assertNull($od->getOp()->getOrElse(null));
    }

    public function testGetOpWithData(): void
    {
        $od = new OperationData(['op' => 'data']);
        $this->assertEquals('data', $od->getOp()->getOrElse(null));
    }

    public function testGetDataWithEmptyData(): void
    {
        $od = new OperationData();
        $this->assertTrue($od->getData()->isEmpty());
    }

    public function testGetDataWithOpOnly(): void
    {
        $od = new OperationData(['op' => 'data']);
        $this->assertTrue($od->getData()->isEmpty());
    }

    public function testGetDataWithData(): void
    {
        $od = new OperationData(['op' => 'data', 'test' => 1, 'test2' => '2']);
        $this->assertFalse($od->getData()->isEmpty());
        $this->assertCount(2, $od->getData());
        $this->assertContains('test', $od->getData()->keys());
        $this->assertContains('test2', $od->getData()->keys());
        $this->assertSame(1, $od->getData()->get('test')->getOrElse(null));
        $this->assertSame('2', $od->getData()->get('test2')->getOrElse(null));
    }

    /**
     * @param mixed $expected
     * @param mixed $requiredKeys
     */
    #[DataProvider("diffKeysProvider")]
    public function testDiffKeys(Sequence $expected, array $requiredKeys): void
    {
        $od = new OperationData(['op' => 'data', 'test' => 1, 'test2' => '2']);
        $this->assertEquals($expected, $od->diffKeys($requiredKeys));
    }

    public static function diffKeysProvider(): array
    {
        return [
            [new Sequence(), ['test', 'test2']],
            [new Sequence(['test']), ['test2']],
            [new Sequence(['test']), ['test2']],
        ];
    }
}
