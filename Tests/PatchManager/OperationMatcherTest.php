<?php

namespace Cypress\PatchManagerBundle\Tests\PatchManager;

use Cypress\PatchManagerBundle\PatchManager\MatchedPatchOperation;
use Cypress\PatchManagerBundle\PatchManager\OperationMatcher;
use Cypress\PatchManagerBundle\PatchManager\Request\Operations;
use Cypress\PatchManagerBundle\Tests\PatchManagerTestCase;
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

    public function setUp()
    {
        $operations = m::mock('Cypress\PatchManagerBundle\PatchManager\Request\Operations');
        $this->ops = new Sequence();
        $this->ops->add(array('op' => 'data'));
        $operations->shouldReceive('all')->andReturn($this->ops)->byDefault();
        $this->matcher = new OperationMatcher($operations);
    }

    public function test_getMatchedOperations_without_handlers()
    {
        $this->assertEquals(new Sequence(), $this->matcher->getMatchedOperations());
    }

    public function test_getMatchedOperations_with_handler_not_matching()
    {
        $this->matcher->addHandler($this->mockHandler('method'));
        $this->assertInstanceOf('PhpCollection\Sequence', $this->matcher->getMatchedOperations());
        $this->assertCount(0, $this->matcher->getMatchedOperations());
    }

    public function test_getMatchedOperations_with_matching_handler()
    {
        $this->matcher->addHandler($this->mockHandler('data'));
        $this->assertInstanceOf('PhpCollection\Sequence', $this->matcher->getMatchedOperations());
        $this->assertCount(1, $this->matcher->getMatchedOperations());
        $mpos = $this->matcher->getMatchedOperations();
        $this->assertInstanceOf(
            'Cypress\PatchManagerBundle\PatchManager\MatchedPatchOperation',
            $mpos->find($this->handlerNameMatcher('data'))->get()
        );
    }

    public function test_getMatchedOperations_with_multiple_operations_matching()
    {
        $this->ops->add(array('op' => 'data'));
        $this->matcher->addHandler($this->mockHandler('data'));
        $this->assertInstanceOf('PhpCollection\Sequence', $this->matcher->getMatchedOperations());
        $mpos = $this->matcher->getMatchedOperations();
        $this->assertCount(2, $mpos->filter($this->handlerNameMatcher('data')));
    }

    public function test_getMatchedOperations_with_multiple_operations_matching_multiple_handlers()
    {
        $this->ops->add(array('op' => 'method'));
        $this->matcher->addHandler($this->mockHandler('data'));
        $this->matcher->addHandler($this->mockHandler('method'));
        $this->assertInstanceOf('PhpCollection\Sequence', $this->matcher->getMatchedOperations());
        $this->assertCount(2, $this->matcher->getMatchedOperations());
        $mpos = $this->matcher->getMatchedOperations();
        $this->assertInstanceOf(
            'Cypress\PatchManagerBundle\PatchManager\MatchedPatchOperation',
            $mpos->find($this->handlerNameMatcher('data'))->get()
        );
    }

    private function handlerNameMatcher($name)
    {
        return function (MatchedPatchOperation $mpo) use ($name) {
            return $mpo->matchFor($name);
        };
    }
}
