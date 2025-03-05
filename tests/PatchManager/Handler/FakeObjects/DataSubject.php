<?php

namespace Cypress\PatchManager\Tests\Handler\FakeObjects;

use Cypress\PatchManager\Patchable;

class DataSubject implements Patchable
{
    /**
     * @var mixed
     */
    private $a;

    /**
     * @var mixed
     */
    private $b;

    public function __call(string $method, array $args): void
    {
        if ('setB' === $method) {
            $this->b = $args[0];
        }
    }

    /**
     * @param mixed $v
     */
    public function setA($v): void
    {
        $this->a = $v;
    }

    /**
     * @return mixed
     */
    public function getA()
    {
        return $this->a;
    }

    /**
     * @return mixed
     */
    public function getB()
    {
        return $this->b;
    }
}
