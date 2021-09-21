<?php

namespace Cypress\PatchManager\Tests\Handler;

use Cypress\PatchManager\Handler\DataDoctrineHandler;
use Cypress\PatchManager\Handler\DataHandler;
use Cypress\PatchManager\OperationData;
use Cypress\PatchManager\Tests\Handler\FakeObjects\DataDoctrineSubject;
use Cypress\PatchManager\Tests\PatchManagerTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Mockery as m;

class DataDoctrineHandlerTest extends PatchManagerTestCase
{
    /**
     * @var m\MockInterface
     */
    private $em;

    /**
     * @var m\MockInterface
     */
    private $metadata;

    /**
     * @var DataHandler
     */
    private $handler;

    public function setUp(): void
    {
        parent::setUp();
        $this->em = m::mock(EntityManagerInterface::class);
        $this->em->shouldReceive('getMetadataFactory->isTransient')->andReturn(false)->byDefault();
        $this->metadata = m::mock(ClassMetadata::class);
        $this->metadata->shouldReceive('getAssociationNames')->andReturn(['a']);
        $this->metadata->shouldReceive('getAssociationTargetClass')->andReturn('TestClass');
        $this->metadata->shouldReceive('getTypeOfField')->andReturnNull()->byDefault();
        $this->em->shouldReceive('getMetadataFactory->getMetadataFor')
            ->with(DataDoctrineSubject::class)
            ->andReturn($this->metadata);
        $this->handler = new DataDoctrineHandler($this->em);
    }

    public function testGetName()
    {
        $this->assertEquals('data', $this->handler->getName());
    }

    public function testHandle()
    {
        $value = new \stdClass();
        $value->test = "test";
        $this->em->shouldReceive('find')->with('TestClass', 1)->once()->andReturn($value);
        $subject = new DataDoctrineSubject();
        $this->assertNull($subject->getA());
        $this->handler->handle($subject, new OperationData(['op' => 'data', 'property' => 'a', 'value' => 1]));
        $this->assertSame($value, $subject->getA());
    }

    public function testHandleWithDate()
    {
        $this->metadata->shouldReceive('getTypeOfField')->andReturn('date');
        $this->em->shouldReceive('find')->with('TestClass', 1)->once()->andReturn();
        $subject = new DataDoctrineSubject();
        $this->assertNull($subject->getA());
        $this->handler->handle($subject, new OperationData(['op' => 'data', 'property' => 'a', 'value' => 1]));
        $this->assertInstanceOf('\DateTime', $subject->getA());
    }

    public function testHandleWithoutForeignKey()
    {
        $this->em->shouldReceive('getMetadataFactory->isTransient')->andReturn(true);
        $subject = new DataDoctrineSubject();
        $this->assertNull($subject->getA());
        $this->handler->handle($subject, new OperationData(['op' => 'data', 'property' => 'a', 'value' => 'test_data']));
        $this->assertSame('test_data', $subject->getA());
    }

    public function testHandleWithMagicCall()
    {
        $this->handler->useMagicCall(true);
        $subject = new DataDoctrineSubject();
        $this->assertNull($subject->getB());
        $this->handler->handle($subject, new OperationData(['op' => 'data', 'property' => 'b', 'value' => 1]));
        $this->assertSame(1, $subject->getB());
    }

    public function testIsEntityWithNonObject()
    {
        $refl = new \ReflectionClass($this->handler);
        $method = $refl->getMethod('isEntity');
        $method->setAccessible(true);
        $this->assertFalse($method->invoke($this->handler, ''));
    }
}
