<?php

declare(strict_types=1);

namespace Cypress\PatchManager\Request;

use Cypress\PatchManager\Exception\InvalidJsonRequestContent;
use Cypress\PatchManager\Exception\MissingOperationNameRequest;
use Cypress\PatchManager\Exception\MissingOperationRequest;
use PhpCollection\Sequence;

class Operations
{
    public const OP_KEY_NAME = 'op';

    private Adapter $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @throws InvalidJsonRequestContent
     * @throws MissingOperationNameRequest
     * @throws MissingOperationRequest
     * @return Sequence
     */
    public function all(): Sequence
    {
        $operationsJson = $this->parseJson($this->adapter->getRequestBody());
        $operations = $this->toSequence($operationsJson);
        $operationsWithoutOpKey = $operations->filterNot(fn ($operationData) => array_key_exists(self::OP_KEY_NAME, $operationData));

        if (!$operationsWithoutOpKey->isEmpty()) {
            /** @var array $operationData */
            $operationData = $operationsWithoutOpKey->first()->get();

            throw new MissingOperationNameRequest($operationData);
        }

        return $operations;
    }

    /**
     * @throws InvalidJsonRequestContent
     * @throws MissingOperationRequest
     */
    private function parseJson(?string $string): array
    {
        if (is_null($string)) {
            throw new InvalidJsonRequestContent();
        }

        try {
            $json = json_decode($string, associative: true, flags: JSON_THROW_ON_ERROR);

            // we need this control because json_decode('2', true, 512, JSON_THROW_ON_ERROR) returns a valid result: int(2)
            if (!is_array($json)) {
                throw new MissingOperationRequest();
            }

            return $json;
        } catch (\JsonException $e) {
            throw new InvalidJsonRequestContent();
        }
    }

    /**
     * @param array $operations
     * @return Sequence
     */
    private function toSequence(array $operations): Sequence
    {
        $operations = $this->isAssociative($operations) ? [$operations] : $operations;

        return new Sequence($operations);
    }

    private function isAssociative(array $arr): bool
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
