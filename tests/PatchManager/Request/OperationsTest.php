<?php

declare(strict_types=1);

namespace Cypress\PatchManager\Tests\Request;

use Cypress\PatchManager\Exception\InvalidJsonRequestContent;
use Cypress\PatchManager\Exception\MissingOperationNameRequest;
use Cypress\PatchManager\Exception\MissingOperationRequest;
use Cypress\PatchManager\Request\Adapter;
use Cypress\PatchManager\Request\Operations;
use Cypress\PatchManager\Tests\PatchManagerTestCase;
use PHPUnit\Framework\Attributes\Test;

class OperationsTest extends PatchManagerTestCase
{
    #[Test]
    public function requestWithInvalidJson(): void
    {
        $this->expectException(InvalidJsonRequestContent::class);
        $adapter = $this->prophesize(Adapter::class);
        $adapter->getRequestBody()->willReturn('{"test": error}');
        $operations = new Operations($adapter->reveal());
        $operations->all();
    }

    #[Test]
    public function exceptionWithNullRequest(): void
    {
        $this->expectException(InvalidJsonRequestContent::class);
        $adapter = $this->prophesize(Adapter::class);
        $adapter->getRequestBody()->willReturn(null);
        $operations = new Operations($adapter->reveal());
        $operations->all();
    }

    #[Test]
    public function correctOperationsNumberWithOneOperation(): void
    {
        $adapter = $this->prophesize(Adapter::class);
        $adapter->getRequestBody()->willReturn('{"op": "data"}');
        $operations = new Operations($adapter->reveal());
        $this->assertCount(1, $operations->all());
        /** @var array $op */
        $op = $operations->all()->get(0);
        $this->assertEquals('data', $op['op']);
    }

    #[Test]
    public function correctOperationsNumberWithMultipleOperation(): void
    {
        $adapter = $this->prophesize(Adapter::class);
        $adapter->getRequestBody()->willReturn('[{"op": "data"},{"op": "data2"}]');
        $operations = new Operations($adapter->reveal());
        $this->assertCount(2, $operations->all());

        /** @var array $op1 */
        $op1 = $operations->all()->get(0);
        $this->assertEquals('data', $op1['op']);

        /** @var array $op2 */
        $op2 = $operations->all()->get(1);
        $this->assertEquals('data2', $op2['op']);
    }

    #[Test]
    public function execeptionWithEmptyRequest(): void
    {
        $this->expectException(MissingOperationRequest::class);
        $adapter = $this->prophesize(Adapter::class);
        $adapter->getRequestBody()->willReturn('""');
        $operations = new Operations($adapter->reveal());
        $operations->all();
    }

    #[Test]
    public function exceptionWithOperationWithoutOp(): void
    {
        $this->expectException(MissingOperationNameRequest::class);
        $adapter = $this->prophesize(Adapter::class);
        $adapter->getRequestBody()->willReturn('[{"op_wrong": "data"}]');
        $operations = new Operations($adapter->reveal());
        $operations->all();
    }

    #[Test]
    public function exceptionWithMultipleOperationWithoutOp(): void
    {
        $this->expectException(MissingOperationNameRequest::class);
        $adapter = $this->prophesize(Adapter::class);
        $adapter->getRequestBody()->willReturn('[{"op": "data"},{"op_wrong": "data"}]');
        $operations = new Operations($adapter->reveal());
        $operations->all();
    }
}
