<?php

namespace spec\PatchManager\Handler;

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
}
