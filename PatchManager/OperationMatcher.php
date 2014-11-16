<?php

namespace Cypress\PatchManagerBundle\PatchManager;

use Cypress\PatchManagerBundle\PatchManager\Handler\PatchOperationHandler;
use Cypress\PatchManagerBundle\PatchManager\Request\Operations;
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
        return $this->operations
            ->all()
            ->foldLeft(new Sequence(), function (Sequence $matchedOperations, $operationData) {
                $handler = $this->handlers->find(function(PatchOperationHandler $handler) use ($operationData) {
                    return $operationData[Operations::OP_KEY_NAME] === $handler->getName();
                });
                if ($handler->isDefined()) {
                    $matchedOperations->add(MatchedPatchOperation::create($operationData, $handler->get()));
                }
                return $matchedOperations;
            });
    }
}
