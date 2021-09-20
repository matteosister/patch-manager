<?php

namespace Cypress\PatchManager\Tests\Handler\FakeObjects;

use Cypress\PatchManager\Patchable;

class DataDoctrineSubject implements Patchable
{
    private $a;

    private $b;

    public function setA($v)
    {
        $this->a = $v;
    }

    public function getA()
    {
        return $this->a;
    }

    public function getB()
    {
        return $this->b;
    }

    public function __call($method, $args)
    {
        if ('setB' === $method) {
            $this->b = $args[0];
        }
    }
}
