<?php

namespace Cypress\PatchManager\Request;

use Cypress\PatchManager\Exception\InvalidJsonRequestContent;
use Cypress\PatchManager\Exception\MissingOperationNameRequest;
use Cypress\PatchManager\Exception\MissingOperationRequest;
use PhpCollection\Sequence;

class Operations
{
    public const OP_KEY_NAME = 'op';

    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
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
        $operations = $this->parseJson($this->adapter->getRequestBody());
        if (!is_array($operations)) {
            throw new MissingOperationRequest();
        }
        $operations = new Sequence($this->isAssociative($operations) ? [$operations] : $operations);
        $operationsWithoutOpKey = $operations->filterNot($this->operationWithKey());
        if (!$operationsWithoutOpKey->isEmpty()) {
            /** @var array $operationData */
            $operationData = $operationsWithoutOpKey->first()->get();

            throw new MissingOperationNameRequest($operationData);
        }

        return $operations;
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
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidJsonRequestContent();
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
     * @param string $key
     * @return \Closure
     */
    private function operationWithKey($key = self::OP_KEY_NAME)
    {
        return fn ($operationData) => array_key_exists($key, $operationData);
    }
}
