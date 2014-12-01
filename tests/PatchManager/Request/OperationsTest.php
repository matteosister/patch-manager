<?php

namespace PatchManager\Tests\Request;

use PatchManager\Tests\PatchManagerTestCase;
use PatchManager\Request\Operations;
use Mockery as m;

class OperationsTest extends PatchManagerTestCase
{
    /**
     * @expectedException \PatchManager\Exception\InvalidJsonRequestContent
     */
    public function test_request_with_invalid_json()
    {
        $operations = new Operations();
        $operations->setRequestBody('{"test": error}');
        $operations->all();
    }

    public function test_correct_operations_number_with_one_operation()
    {
        $operations = new Operations();
        $operations->setRequestBody('{"op": "data"}');
        $this->assertCount(1, $operations->all());
        $op = $operations->all()->get(0);
        $this->assertEquals('data', $op['op']);
    }

    public function test_correct_operations_number_with_multiple_operation()
    {
        $operations = new Operations();
        $operations->setRequestBody('[{"op": "data"},{"op": "data2"}]');
        $this->assertCount(2, $operations->all());
        $op1 = $operations->all()->get(0);
        $this->assertEquals('data', $op1['op']);
        $op2 = $operations->all()->get(1);
        $this->assertEquals('data2', $op2['op']);
    }

    /**
     * @expectedException \PatchManager\Exception\MissingOperationRequest
     */
    public function test_exeception_with_empty_request()
    {
        $operations = new Operations();
        $operations->setRequestBody('');
        $operations->all();
    }

    /**
     * @expectedException \PatchManager\Exception\MissingOperationNameRequest
     */
    public function test_exeception_with_operation_without_op()
    {
        $operations = new Operations();
        $operations->setRequestBody('[{"op_wrong": "data"}]');
        $operations->all();
    }

    /**
     * @expectedException \PatchManager\Exception\MissingOperationNameRequest
     */
    public function test_exeception_with_multiple_operation_without_op()
    {
        $operations = new Operations();
        $operations->setRequestBody('[{"op": "data"},{"op_wrong": "data"}]');
        $operations->all();
    }
}
