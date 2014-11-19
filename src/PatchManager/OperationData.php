<?php


namespace PatchManager;

use PatchManager\Request\Operations;
use PhpCollection\Map;
use PhpCollection\Sequence;

class OperationData extends Map
{
    /**
     * @return \PhpOption\None|\PhpOption\Some
     */
    public function getOp()
    {
        return $this->get(Operations::OP_KEY_NAME);
    }

    /**
     * @return Map
     */
    public function getData()
    {
        $operationData = new Map($this->elements);
        if ($operationData->containsKey(Operations::OP_KEY_NAME)) {
            $operationData->remove(Operations::OP_KEY_NAME);
        }
        return $operationData;
    }

    /**
     * @param array $keys
     * @return bool
     */
    public function containsKeys(array $keys)
    {
        foreach ($keys as $key) {
            if (! $this->containsKey($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array $keys
     * @return Sequence
     */
    public function diffKeys(array $keys)
    {
        return new Sequence(
            array_filter(
                $this->getData()->keys(),
                function ($key) use ($keys) {
                    return ! in_array($key, $keys);
                }
            )
        );
    }
}