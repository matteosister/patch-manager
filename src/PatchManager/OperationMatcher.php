<?php

namespace Cypress\PatchManager;

use Cypress\PatchManager\Request\Operations;
use PhpCollection\Sequence;

/**
 * Match the correct handlers based on actual operations
 */
class OperationMatcher
{
    /**
     * @var Sequence
     */
    private Sequence $handlers;

    /**
     * @var Operations
     */
    private Operations $operations;

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
    public function addHandler(PatchOperationHandler $handler): void
    {
        $this->handlers->add($handler);
    }

    /**
     * @param array|Patchable|\Traversable $subject a Patchable instance or a collection of instances
     *
     * @throws Exception\MissingOperationNameRequest
     * @throws Exception\MissingOperationRequest
     * @throws Exception\InvalidJsonRequestContent
     * @return Sequence
     */
    public function getMatchedOperations($subject): Sequence
    {
        $handlers = $this->handlers;

        return $this->operations
            ->all()
            ->foldLeft(
                new Sequence(),
                function (Sequence $matchedOperations, array $operationData) use ($handlers, $subject) {
                    $handler = $handlers->find(fn (PatchOperationHandler $patchHandler) => $operationData[Operations::OP_KEY_NAME] === $patchHandler->getName());
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
     * @param array|Patchable|\Traversable $subject a Patchable instance or a collection of instances
     * @throws Exception\InvalidJsonRequestContent
     * @throws Exception\MissingOperationNameRequest
     * @throws Exception\MissingOperationRequest
     * @return Sequence
     */
    public function getUnmatchedOperations($subject): Sequence
    {
        return $this->operations
            ->all()
            ->filter(fn (array $operationData) => $operationData !== $this->getMatchedOperations($subject))
            ->map(fn (array $operationData) => $operationData['op']);
    }
}
