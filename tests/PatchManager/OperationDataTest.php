<?php

declare(strict_types=1);

namespace Cypress\PatchManager\Tests;

use Cypress\PatchManager\OperationData;
use PhpCollection\Sequence;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class OperationDataTest extends PatchManagerTestCase
{
    #[Test]
    public function getOpWithEmptyData(): void
    {
        $od = new OperationData();
        $this->assertNull($od->getOp()->getOrElse(null));
    }

    #[Test]
    public function getOpWithData(): void
    {
        $od = new OperationData(['op' => 'data']);
        $this->assertEquals('data', $od->getOp()->getOrElse(null));
    }

    #[Test]
    public function getDataWithEmptyData(): void
    {
        $od = new OperationData();
        $this->assertTrue($od->getData()->isEmpty());
    }

    #[Test]
    public function getDataWithOpOnly(): void
    {
        $od = new OperationData(['op' => 'data']);
        $this->assertTrue($od->getData()->isEmpty());
    }

    #[Test]
    public function getDataWithData(): void
    {
        $od = new OperationData(['op' => 'data', 'test' => 1, 'test2' => '2']);
        $this->assertFalse($od->getData()->isEmpty());
        $this->assertCount(2, $od->getData());
        $this->assertContains('test', $od->getData()->keys());
        $this->assertContains('test2', $od->getData()->keys());
        $this->assertSame(1, $od->getData()->get('test')->getOrElse(null));
        $this->assertSame('2', $od->getData()->get('test2')->getOrElse(null));
    }

    #[DataProvider("diffKeysProvider")]
    #[Test]
    public function diffKeys(Sequence $expected, array $requiredKeys): void
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
