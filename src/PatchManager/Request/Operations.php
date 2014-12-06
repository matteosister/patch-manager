<?php

namespace PatchManager\Request;

use PatchManager\Exception\InvalidJsonRequestContent;
use PatchManager\Exception\MissingOperationNameRequest;
use PatchManager\Exception\MissingOperationRequest;
use PhpCollection\Sequence;

class Operations
{
    const OP_KEY_NAME = 'op';

    /**
     * @var string
     */
    private $requestBody;

    /**
     * @param string $requestBody
     */
    public function setRequestBody($requestBody)
    {
        $this->requestBody = $requestBody;
    }

    /**
     * directly from stack overflow: http://stackoverflow.com/a/6041773
     * check if a string is valid json, and returns the parsed content
     *
     * @param string $string
     *
     * @throws InvalidJsonRequestContent
     * @return array
     */
    private function parseJson($string)
    {
        $parsedContent = json_decode($string, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidJsonRequestContent;
        }
        return $parsedContent;
    }

    /**
     * @param array $arr
     * @return bool
     */
    private function isAssociative($arr)
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * @throws InvalidJsonRequestContent
     * @throws MissingOperationNameRequest
     * @throws MissingOperationRequest
     *
     * @return Sequence
     */
    public function all()
    {
        $operations =$this->parseJson($this->requestBody);
        if (! is_array($operations)) {
            throw new MissingOperationRequest();
        }
        $operations = new Sequence($this->isAssociative($operations) ? array($operations) : $operations);
        $operationsWithoutOpKey = $operations->filterNot($this->operationWithKey());
        if (! $operationsWithoutOpKey->isEmpty()) {
            /** @var array $operationData */
            $operationData = $operationsWithoutOpKey->first()->get();
            throw new MissingOperationNameRequest($operationData);
        }
        return $operations;
    }

    /**
     * @param string $key
     * @return \Closure
     */
    private function operationWithKey($key = self::OP_KEY_NAME)
    {
        return function ($operationData) use ($key) {
            return array_key_exists($key, $operationData);
        };
    }
}
