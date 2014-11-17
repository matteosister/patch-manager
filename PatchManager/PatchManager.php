<?php

namespace Cypress\PatchManagerBundle\PatchManager;

use Cypress\PatchManagerBundle\PatchManager\Request\Operations;
use PhpCollection\Sequence;

/**
 * The main entry point for the PatchManager bundle
 */
class PatchManager
{
    /**
     * @var OperationMatcher
     */
    private $operationMatcher;

    /**
     * @param OperationMatcher $operationMatcher
     */
    public function __construct(OperationMatcher $operationMatcher)
    {
        $this->operationMatcher = $operationMatcher;
    }

    /**
     * @param Patchable $subject
     * @return array
     * @throws \Cypress\PatchManagerBundle\Exception\MissingOperationRequest
     */
    public function handle(Patchable $subject)
    {
        $this->operationMatcher
            ->getMatchedOperations()
            ->map($this->handleOperation($subject));
    }

    /**
     * calls the handle method for every patch manager maching an operation
     *
     * @param Patchable $subject
     * @return callable
     */
    private function handleOperation(Patchable $subject)
    {
        return function (MatchedPatchOperation $matchedPatchOperation) use ($subject) {
            $matchedPatchOperation->process($subject);
        };
    }
}
