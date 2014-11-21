<?php

namespace PatchManager;

use PatchManager\Request\Operations;
use PhpCollection\Sequence;

/**
 * Match the correct handlers based on actual operations
 */
class OperationMatcher
{
    /**
     * @var Sequence
     */
    private $handlers;

    /**
     * @var Operations
     */
    private $operations;

    /**
     * @param Operations $operations
     */
    public function __construct(Operations $operations)
    {
        $this->handlers = new Sequence();
        $this->operations = $operations;
    }

    /**
     * @param PatchOperationHandler $handler
     */
    public function addHandler(PatchOperationHandler $handler)
    {
        $this->handlers->add($handler);
    }

    /**
     * @return Sequence
     */
    public function getMatchedOperations()
    {
        $handlers = $this->handlers;
        return $this->operations
            ->all()
            ->foldLeft(
                new Sequence(),
                function (Sequence $matchedOperations, array $operationData) use ($handlers) {
                    $handler = $handlers->find(function (PatchOperationHandler $handler) use ($operationData) {
                        return $operationData[Operations::OP_KEY_NAME] === $handler->getName();
                    });
                    if ($handler->isDefined()) {
                        $matchedOperations->add(MatchedPatchOperation::create($operationData, $handler->get()));
                    }
                    return $matchedOperations;
                }
            );
    }
}
