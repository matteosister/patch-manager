<?php

namespace spec\PatchManager\Handler;

use PatchManager\OperationData;
use PatchManager\Patchable;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DataHandlerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PatchManager\Handler\DataHandler');
    }

    function it_is_a_PatchOperationHandler()
    {
        $this->shouldHaveType('PatchManager\Handler\PatchOperationHandler');
    }

    function it_should_update_data_by_calling_the_setter(SubjectSetter $subject)
    {
        $subject->setA(1)->shouldBeCalled();
        $this->handle($subject, new OperationData(array('op' => 'data', 'property' => 'a', 'value' => 1)));
    }
}


class SubjectSetter implements Patchable
{
    private $a;

    public function setA($v)
    {
        $this->a = $v;
    }
}