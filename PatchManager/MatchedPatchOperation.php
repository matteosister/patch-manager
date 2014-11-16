<?php

namespace Cypress\PatchManagerBundle\PatchManager;

use Cypress\PatchManagerBundle\PatchManager\Handler\PatchOperationHandler;

class MatchedPatchOperation
{
    /**
     * @var array
     */
    private $operationData;

    /**
     * @var PatchOperationHandler
     */
    private $handler;

    /**
     * @param array $operationData
     * @param PatchOperationHandler $handler
     */
    private function __construct(array $operationData, PatchOperationHandler $handler)
    {
        $this->operationData = $operationData;
        $this->handler = $handler;
    }

    /**
     * @param array $operationData
     * @param PatchOperationHandler $handler
     * @return MatchedPatchOperation
     */
    public static function create(array $operationData, PatchOperationHandler $handler)
    {
        return new self($operationData, $handler);
    }
}
