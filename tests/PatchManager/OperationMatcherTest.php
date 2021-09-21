<?php

namespace Cypress\PatchManager\Tests;

use Cypress\PatchManager\MatchedPatchOperation;
use Cypress\PatchManager\OperationMatcher;
use Mockery as m;
use PhpCollection\Sequence;

class OperationMatcherTest extends PatchManagerTestCase
{
    /**
     * @var OperationMatcher
     */
    private OperationMatcher $matcher;

    /**
     * @var Sequence
     */
    private Sequence $ops;

    public function setUp(): void
    {
        parent::setUp();
        $operations = m::mock('Cypress\PatchManager\Request\Operations');
        $this->ops = new Sequence();
        $this->ops->add(['op' => 'data']);
        $operations->shouldReceive('all')->andReturn($this->ops)->byDefault();
        $this->matcher = new OperationMatcher($operations);
    }

    public function testGetMatchedOperationsWithoutHandlers()
    {
        $this->assertEquals(new Sequence(), $this->matcher->getMatchedOperations('test'));
    }

    public function testGetMatchedOperationsWithHandlerNotMatching()
    {
        $this->matcher->addHandler($this->mockHandler('method')->reveal());
        $this->assertInstanceOf('PhpCollection\Sequence', $this->matcher->getMatchedOperations('test'));
        $this->assertCount(0, $this->matcher->getMatchedOperations('test'));
    }

    public function testGetMatchedOperationsWithMatchingHandler()
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

    public function testGetMatchedOperationsWithMultipleOperationsMatching()
    {
        $this->ops->add(['op' => 'data']);
        $this->matcher->addHandler($this->mockHandler('data')->reveal());
        $this->assertInstanceOf('PhpCollection\Sequence', $this->matcher->getMatchedOperations('test'));
        $mpos = $this->matcher->getMatchedOperations('test');
        $this->assertCount(2, $mpos->filter($this->handlerNameMatcher('data')));
    }

    public function testGetMatchedOperationsWithMultipleOperationsMatchingMultipleHandlers()
    {
        $this->ops->add(['op' => 'method']);
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

    public function testGetUnmatchedOperationsWithHandlerNotMatching()
    {
        $this->matcher->addHandler($this->mockHandler('method')->reveal());
        $this->assertInstanceOf('PhpCollection\Sequence', $this->matcher->getMatchedOperations('test'));
        $this->assertCount(1, $this->matcher->getUnmatchedOperations('test'));
        $this->assertInstanceOf('PhpCollection\Sequence', $this->matcher->getUnmatchedOperations('test'));
        $this->assertEquals(new Sequence(['data']), $this->matcher->getUnmatchedOperations('test'));
    }

    public function testHandlerThatRespondsFalseToCanHandle()
    {
        $this->matcher->addHandler($this->mockHandler('data', false)->reveal());
        $this->assertCount(0, $this->matcher->getMatchedOperations('test'));
        $this->assertInstanceOf('PhpCollection\Sequence', $this->matcher->getMatchedOperations('test'));
        $this->assertEquals(new Sequence(), $this->matcher->getMatchedOperations('test'));
        $this->assertCount(1, $this->matcher->getUnmatchedOperations('test'));
        $this->assertInstanceOf('PhpCollection\Sequence', $this->matcher->getUnmatchedOperations('test'));
        $this->assertEquals(new Sequence(['data']), $this->matcher->getUnmatchedOperations('test'));
    }

    private function handlerNameMatcher($name)
    {
        return fn (MatchedPatchOperation $mpo) => $mpo->matchFor($name);
    }
}
