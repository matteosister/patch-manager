<?php

namespace Cypress\PatchManager;

use Cypress\PatchManager\Request\Operations;
use PhpCollection\Map;
use PhpCollection\Sequence;
use PhpOption\Option;

class OperationData extends Map
{
    public function __construct(array $elements = [])
    {
        parent::__construct($elements);
    }

    /**
     * @return Option
     */
    public function getOp(): Option
    {
        return $this->get(Operations::OP_KEY_NAME);
    }

    /**
     * @return Map
     */
    public function getData(): Map
    {
        $operationData = new Map($this->elements);
        if ($operationData->containsKey(Operations::OP_KEY_NAME)) {
            $operationData->remove(Operations::OP_KEY_NAME);
        }

        return $operationData;
    }

    /**
     * @param array $keys
     * @return Sequence
     */
    public function diffKeys(array $keys): Sequence
    {
        $filtered = array_filter($this->getData()->keys(), fn ($key) => !in_array($key, $keys));

        return new Sequence($filtered);
    }
}
