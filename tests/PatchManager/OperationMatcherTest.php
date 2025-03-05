<?php

namespace Cypress\PatchManager\Tests;

use Cypress\PatchManager\MatchedPatchOperation;
use Cypress\PatchManager\OperationMatcher;
use Cypress\PatchManager\Request\Operations;
use Cypress\PatchManager\Tests\FakeObjects\SubjectA;
use Mockery as m;
use PhpCollection\Sequence;

class OperationMatcherTest extends PatchManagerTestCase
{
    private OperationMatcher $matcher;

    private Sequence $ops;

    public function setUp(): void
    {
        parent::setUp();
        $operations = m::mock(Operations::class);
        $this->ops = new Sequence();
        $this->ops->add(['op' => 'data']);
        $operations->shouldReceive('all')->andReturn($this->ops)->byDefault();
        $this->matcher = new OperationMatcher($operations);
    }

    public function testGetMatchedOperationsWithoutHandlers(): void
    {
        $this->assertEquals(new Sequence(), $this->matcher->getMatchedOperations(new SubjectA()));
    }

    public function testGetMatchedOperationsWithHandlerNotMatching(): void
    {
        $this->matcher->addHandler($this->mockHandler('method')->reveal());
        $this->assertInstanceOf(Sequence::class, $this->matcher->getMatchedOperations(new SubjectA()));
        $this->assertCount(0, $this->matcher->getMatchedOperations(new SubjectA()));
    }

    public function testGetMatchedOperationsWithMatchingHandler(): void
    {
        $this->matcher->addHandler($this->mockHandler('data')->reveal());
        $this->assertInstanceOf(Sequence::class, $this->matcher->getMatchedOperations(new SubjectA()));
        $this->assertCount(1, $this->matcher->getMatchedOperations(new SubjectA()));
        $mpos = $this->matcher->getMatchedOperations(new SubjectA());
        $this->assertInstanceOf(
            MatchedPatchOperation::class,
            $mpos->find($this->handlerNameMatcher('data'))->get()
        );
    }

    public function testGetMatchedOperationsWithMultipleOperationsMatching(): void
    {
        $this->ops->add(['op' => 'data']);
        $this->matcher->addHandler($this->mockHandler('data')->reveal());
        $this->assertInstanceOf(Sequence::class, $this->matcher->getMatchedOperations(new SubjectA()));
        $mpos = $this->matcher->getMatchedOperations(new SubjectA());
        $this->assertCount(2, $mpos->filter($this->handlerNameMatcher('data')));
    }

    public function testGetMatchedOperationsWithMultipleOperationsMatchingMultipleHandlers(): void
    {
        $this->ops->add(['op' => 'method']);
        $this->matcher->addHandler($this->mockHandler('data')->reveal());
        $this->matcher->addHandler($this->mockHandler('method')->reveal());
        $this->assertInstanceOf(Sequence::class, $this->matcher->getMatchedOperations(new SubjectA()));
        $this->assertCount(2, $this->matcher->getMatchedOperations(new SubjectA()));
        $mpos = $this->matcher->getMatchedOperations(new SubjectA());
        $this->assertInstanceOf(
            MatchedPatchOperation::class,
            $mpos->find($this->handlerNameMatcher('data'))->get()
        );
    }

    public function testGetUnmatchedOperationsWithHandlerNotMatching(): void
    {
        $this->matcher->addHandler($this->mockHandler('method')->reveal());
        $this->assertInstanceOf(Sequence::class, $this->matcher->getMatchedOperations(new SubjectA()));
        $this->assertCount(1, $this->matcher->getUnmatchedOperations(new SubjectA()));
        $this->assertInstanceOf(Sequence::class, $this->matcher->getUnmatchedOperations(new SubjectA()));
        $this->assertEquals(new Sequence(['data']), $this->matcher->getUnmatchedOperations(new SubjectA()));
    }

    public function testHandlerThatRespondsFalseToCanHandle(): void
    {
        $this->matcher->addHandler($this->mockHandler('data', false)->reveal());
        $this->assertCount(0, $this->matcher->getMatchedOperations(new SubjectA()));
        $this->assertInstanceOf(Sequence::class, $this->matcher->getMatchedOperations(new SubjectA()));
        $this->assertEquals(new Sequence(), $this->matcher->getMatchedOperations(new SubjectA()));
        $this->assertCount(1, $this->matcher->getUnmatchedOperations(new SubjectA()));
        $this->assertInstanceOf(Sequence::class, $this->matcher->getUnmatchedOperations(new SubjectA()));
        $this->assertEquals(new Sequence(['data']), $this->matcher->getUnmatchedOperations(new SubjectA()));
    }

    /**
     * @param string $name
     */
    private function handlerNameMatcher($name): \Closure
    {
        return fn (MatchedPatchOperation $mpo) => $mpo->matchFor($name);
    }
}
