<?php

namespace Cypress\PatchManager;

use Cypress\PatchManager\Request\Operations;
use PhpCollection\Sequence;
use PhpOption\Option;

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
     * @param $subject
     *
     * @return Sequence
     *
     * @throws Exception\MissingOperationNameRequest
     * @throws Exception\MissingOperationRequest
     */
    public function getMatchedOperations($subject)
    {
        $handlers = $this->handlers;
        return $this->operations
            ->all()
            ->foldLeft(
                new Sequence(),
                function (Sequence $matchedOperations, array $operationData) use ($handlers, $subject) {
                    /** @var Option $handler */
                    $handler = $handlers->find(function (PatchOperationHandler $handler) use ($operationData) {
                        return $operationData[Operations::OP_KEY_NAME] === $handler->getName();
                    });
                    if ($handler->isDefined()) {
                        /** @var PatchOperationHandler $patchOperationHandler */
                        $patchOperationHandler = $handler->get();
                        if ($patchOperationHandler->canHandle($subject)) {
                            $matchedOperations->add(MatchedPatchOperation::create($operationData, $handler->get()));
                        }
                    }
                    return $matchedOperations;
                }
            );
    }

    /**
     * @param $subject
     *
     * @return Sequence
     *
     * @throws Exception\MissingOperationNameRequest
     * @throws Exception\MissingOperationRequest
     */
    public function getUnmatchedOperations($subject)
    {
        $matchedOperations = $this->getMatchedOperations($subject);
        return $this->operations
            ->all()
            ->filter(function (array $operationData) use ($matchedOperations) {
                return $operationData !== $matchedOperations;
            })
            ->map(function (array $operationData) {
                return $operationData['op'];
            });
    }
}
