<?php

declare(strict_types=1);

namespace Cypress\PatchManager\Tests;

use Cypress\PatchManager\MatchedPatchOperation;
use Cypress\PatchManager\OperationMatcher;
use Cypress\PatchManager\Request\Operations;
use Cypress\PatchManager\Tests\FakeObjects\SubjectA;
use Mockery as m;
use PhpCollection\Sequence;
use PHPUnit\Framework\Attributes\Test;

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
        $operations->allows('all')->andReturns($this->ops)->byDefault();
        $this->matcher = new OperationMatcher($operations);
    }

    #[Test]
    public function getMatchedOperationsWithoutHandlers(): void
    {
        $this->assertEquals(new Sequence(), $this->matcher->getMatchedOperations(new SubjectA()));
    }

    #[Test]
    public function getMatchedOperationsWithHandlerNotMatching(): void
    {
        $this->matcher->addHandler($this->mockHandler('method')->reveal());
        $this->assertInstanceOf(Sequence::class, $this->matcher->getMatchedOperations(new SubjectA()));
        $this->assertCount(0, $this->matcher->getMatchedOperations(new SubjectA()));
    }

    #[Test]
    public function getMatchedOperationsWithMatchingHandler(): void
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

    #[Test]
    public function getMatchedOperationsWithMultipleOperationsMatching(): void
    {
        $this->ops->add(['op' => 'data']);
        $this->matcher->addHandler($this->mockHandler('data')->reveal());
        $this->assertInstanceOf(Sequence::class, $this->matcher->getMatchedOperations(new SubjectA()));
        $mpos = $this->matcher->getMatchedOperations(new SubjectA());
        $this->assertCount(2, $mpos->filter($this->handlerNameMatcher('data')));
    }

    #[Test]
    public function getMatchedOperationsWithMultipleOperationsMatchingMultipleHandlers(): void
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

    #[Test]
    public function getUnmatchedOperationsWithHandlerNotMatching(): void
    {
        $this->matcher->addHandler($this->mockHandler('method')->reveal());
        $this->assertInstanceOf(Sequence::class, $this->matcher->getMatchedOperations(new SubjectA()));
        $this->assertCount(1, $this->matcher->getUnmatchedOperations(new SubjectA()));
        $this->assertInstanceOf(Sequence::class, $this->matcher->getUnmatchedOperations(new SubjectA()));
        $this->assertEquals(new Sequence(['data']), $this->matcher->getUnmatchedOperations(new SubjectA()));
    }

    #[Test]
    public function handlerThatRespondsFalseToCanHandle(): void
    {
        $this->matcher->addHandler($this->mockHandler('data', false)->reveal());
        $this->assertCount(0, $this->matcher->getMatchedOperations(new SubjectA()));
        $this->assertInstanceOf(Sequence::class, $this->matcher->getMatchedOperations(new SubjectA()));
        $this->assertEquals(new Sequence(), $this->matcher->getMatchedOperations(new SubjectA()));
        $this->assertCount(1, $this->matcher->getUnmatchedOperations(new SubjectA()));
        $this->assertInstanceOf(Sequence::class, $this->matcher->getUnmatchedOperations(new SubjectA()));
        $this->assertEquals(new Sequence(['data']), $this->matcher->getUnmatchedOperations(new SubjectA()));
    }

    private function handlerNameMatcher(string $name): \Closure
    {
        return static fn (MatchedPatchOperation $mpo) => $mpo->matchFor($name);
    }
}
