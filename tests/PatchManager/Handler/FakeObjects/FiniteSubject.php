<?php

namespace Cypress\PatchManager\Tests\Handler\FakeObjects;

use Cypress\PatchManager\Patchable;
use Finite\StatefulInterface;

class FiniteSubject implements StatefulInterface, Patchable
{
    private $state;

    public function __construct()
    {
        $this->state = 's1';
    }

    /**
     * Sets the object state
     *
     * @return string
     */
    public function getFiniteState()
    {
        return $this->state;
    }

    /**
     * Sets the object state
     *
     * @param string $state
     */
    public function setFiniteState($state)
    {
        $this->state = $state;
    }
}
