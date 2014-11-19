<?php

namespace PatchManager;

use PatchManager\Exception\MissingKeysRequest;
use PatchManager\Handler\PatchOperationHandler;
use PatchManager\Request\Operations;
use PhpCollection\Sequence;

class MatchedPatchOperation
{
    /**
     * @var OperationData
     */
    private $operationData;

    /**
     * @var PatchOperationHandler
     */
    private $handler;

    /**
     * @param OperationData $operationData
     * @param PatchOperationHandler $handler
     */
    private function __construct(OperationData $operationData, PatchOperationHandler $handler)
    {
        $this->operationData = $operationData;
        $this->handler = $handler;
    }

    /**
     * @param OperationData $operationData
     * @param PatchOperationHandler $handler
     * @return MatchedPatchOperation
     */
    public static function create(OperationData $operationData, PatchOperationHandler $handler)
    {
        return new self($operationData, $handler);
    }

    /**
     * @param string $operationName
     * @return bool
     */
    public function matchFor($operationName)
    {
        return $operationName === $this->handler->getName();
    }

    /**
     * call handle on the handler
     *
     * @param Patchable $patchable
     */
    public function process(Patchable $patchable)
    {
        $this->validate();
        $this->handler->handle($patchable, $this->operationData);
    }

    /**
     * @throws MissingKeysRequest
     */
    private function validate()
    {
        if (! $this->operationData->containsKeys($this->handler->getRequiredKeys())) {
            throw new MissingKeysRequest(
                $this->operationData,
                $this->operationData->diffKeys($this->handler->getRequiredKeys())
            );
        }
    }
}
