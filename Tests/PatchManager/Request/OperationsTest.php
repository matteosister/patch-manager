<?php

namespace Cypress\PatchManagerBundle\Tests\PatchManager\Request;

use Cypress\PatchManagerBundle\Exception\InvalidJsonRequestContent;
use Cypress\PatchManagerBundle\PatchManager\Request\Operations;
use Mockery as m;

class OperationsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var m\MockInterface
     */
    private $currentRequest;

    /**
     * @var Operations
     */
    private $service;

    public function setUp()
    {
        $requestStack = m::mock('Symfony\Component\HttpFoundation\RequestStack');
        $this->currentRequest = m::mock('Symfony\Component\HttpFoundation\Request');
        $this->currentRequest->shouldReceive('isMethod')->with('PATCH')->andReturn(true)->byDefault();
        $requestStack->shouldReceive('getCurrentRequest')->andReturn($this->currentRequest);

        $this->service = new Operations($requestStack);
    }

    /**
     * @expectedException \Cypress\PatchManagerBundle\Exception\InvalidJsonRequestContent
     */
    public function test_request_with_invalid_json()
    {
        $this->currentRequest->shouldReceive('getContent')->andReturn('{"test": error}');
        $this->service->all();
    }

    public function test_empty_sequence_for_requests_different_from_patch()
    {
        $this->currentRequest->shouldReceive('isMethod')->with('PATCH')->andReturn(false);
        $this->assertEmpty($this->service->all());
    }

    public function test_correct_operations_number_with_one_operation()
    {
        $this->currentRequest->shouldReceive('getContent')->andReturn('{"op": "data"}');
        $this->assertCount(1, $this->service->all());
    }

    public function test_correct_operations_number_with_multiple_operation()
    {
        $this->currentRequest->shouldReceive('getContent')->andReturn('[{"op": "data"}, {"op": "data"}]');
        $this->assertCount(2, $this->service->all());
    }

    /**
     * @expectedException \Cypress\PatchManagerBundle\Exception\MissingOperationRequest
     */
    public function test_exeception_with_empty_request()
    {
        $this->currentRequest->shouldReceive('getContent')->andReturn('');
        $this->assertCount(2, $this->service->all());
    }

    public function parseProvider()
    {
        return [
            [InvalidJsonRequestContent::class, '']
        ];
    }
}
