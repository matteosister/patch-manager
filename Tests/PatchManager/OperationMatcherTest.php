<?php

namespace Cypress\PatchManagerBundle\Tests\PatchManager;

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

    public function setUp()
    {
        $operations = m::mock(Operations::class);
        $ops = new Sequence();
        $ops->add(['op' => 'data']);
        $operations->shouldReceive('all')->andReturn($ops)->byDefault();
        $this->matcher = new OperationMatcher($operations);
    }

    public function test_getMatchedOperations_without_handlers()
    {
        $this->assertEquals(new Sequence(), $this->matcher->getMatchedOperations());
    }

    public function test_getMatchedOperations_with_handler_not_matching()
    {
        $this->matcher->addHandler($this->mockHandler('method'));
        $this->assertInstanceOf(Sequence::class, $this->matcher->getMatchedOperations());
        $this->assertCount(0, $this->matcher->getMatchedOperations());
    }
}
