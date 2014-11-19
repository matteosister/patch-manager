<?php

namespace PatchManager;

use PatchManager\Exception\MissingOperationRequest;

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
     * @throws MissingOperationRequest
     */
    public function handle(Patchable $subject)
    {
        $this->operationMatcher
            ->getMatchedOperations()
            ->map(function (MatchedPatchOperation $matchedPatchOperation) use ($subject) {
                $matchedPatchOperation->process($subject);
            });
    }
}
