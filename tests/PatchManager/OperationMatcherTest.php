<?php

namespace Cypress\PatchManager\Tests;

use Cypress\PatchManager\MatchedPatchOperation;
use Cypress\PatchManager\OperationData;
use Cypress\PatchManager\OperationMatcher;
use Mockery as m;
use PhpCollection\Sequence;

class OperationMatcherTest extends PatchManagerTestCase
{
    /**
     * @var OperationMatcher
     */
    private $matcher;

    /**
     * @var Sequence
     */
    private $ops;

    public function setUp(): void
    {
        parent::setUp();
        $operations = m::mock('Cypress\PatchManager\Request\Operations');
        $this->ops = new Sequence();
        $this->ops->add(array('op' => 'data'));
        $operations->shouldReceive('all')->andReturn($this->ops)->byDefault();
        $this->matcher = new OperationMatcher($operations);
    }

    public function test_getMatchedOperations_without_handlers()
    {
        $this->assertEquals(new Sequence(), $this->matcher->getMatchedOperations('test'));
    }

    public function test_getMatchedOperations_with_handler_not_matching()
    {
        $this->matcher->addHandler($this->mockHandler('method')->reveal());
        $this->assertInstanceOf('PhpCollection\Sequence', $this->matcher->getMatchedOperations('test'));
        $this->assertCount(0, $this->matcher->getMatchedOperations('test'));
    }

    public function test_getMatchedOperations_with_matching_handler()
    {
        $this->matcher->addHandler($this->mockHandler('data')->reveal());
        $this->assertInstanceOf('PhpCollection\Sequence', $this->matcher->getMatchedOperations('test'));
        $this->assertCount(1, $this->matcher->getMatchedOperations('test'));
        $mpos = $this->matcher->getMatchedOperations('test');
        $this->assertInstanceOf(
            'Cypress\PatchManager\MatchedPatchOperation',
            $mpos->find($this->handlerNameMatcher('data'))->get()
        );
    }

    public function test_getMatchedOperations_with_multiple_operations_matching()
    {
        $this->ops->add(array('op' => 'data'));
        $this->matcher->addHandler($this->mockHandler('data')->reveal());
        $this->assertInstanceOf('PhpCollection\Sequence', $this->matcher->getMatchedOperations('test'));
        $mpos = $this->matcher->getMatchedOperations('test');
        $this->assertCount(2, $mpos->filter($this->handlerNameMatcher('data')));
    }

    public function test_getMatchedOperations_with_multiple_operations_matching_multiple_handlers()
    {
        $this->ops->add(array('op' => 'method'));
        $this->matcher->addHandler($this->mockHandler('data')->reveal());
        $this->matcher->addHandler($this->mockHandler('method')->reveal());
        $this->assertInstanceOf('PhpCollection\Sequence', $this->matcher->getMatchedOperations('test'));
        $this->assertCount(2, $this->matcher->getMatchedOperations('test'));
        $mpos = $this->matcher->getMatchedOperations('test');
        $this->assertInstanceOf(
            'Cypress\PatchManager\MatchedPatchOperation',
            $mpos->find($this->handlerNameMatcher('data'))->get()
        );
    }

    public function test_getUnmatchedOperations_with_handler_not_matching()
    {
        $this->matcher->addHandler($this->mockHandler('method')->reveal());
        $this->assertInstanceOf('PhpCollection\Sequence', $this->matcher->getMatchedOperations('test'));
        $this->assertCount(1, $this->matcher->getUnmatchedOperations('test'));
        $this->assertInstanceOf('PhpCollection\Sequence', $this->matcher->getUnmatchedOperations('test'));
        $this->assertEquals(new Sequence(array('data')), $this->matcher->getUnmatchedOperations('test'));
    }

    public function test_handler_that_responds_false_to_canHandle()
    {
        $this->matcher->addHandler($this->mockHandler('data', false)->reveal());
        $this->assertCount(0, $this->matcher->getMatchedOperations('test'));
        $this->assertInstanceOf('PhpCollection\Sequence', $this->matcher->getMatchedOperations('test'));
        $this->assertEquals(new Sequence(), $this->matcher->getMatchedOperations('test'));
        $this->assertCount(1, $this->matcher->getUnmatchedOperations('test'));
        $this->assertInstanceOf('PhpCollection\Sequence', $this->matcher->getUnmatchedOperations('test'));
        $this->assertEquals(new Sequence(array('data')), $this->matcher->getUnmatchedOperations('test'));
    }

    private function handlerNameMatcher($name)
    {
        return function (MatchedPatchOperation $mpo) use ($name) {
            return $mpo->matchFor($name);
        };
    }
}
