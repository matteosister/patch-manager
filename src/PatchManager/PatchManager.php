<?php

namespace PatchManager;

use PatchManager\Event\PatchManagerEvent;
use PatchManager\Event\PatchManagerEvents;
use PatchManager\Exception\MissingOperationRequest;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
     * @var EventDispatcherInterface
     */
    private $eventDispatcherInterface;

    /**
     * @param OperationMatcher $operationMatcher
     * @param EventDispatcherInterface $eventDispatcherInterface
     */
    public function __construct(OperationMatcher $operationMatcher, EventDispatcherInterface $eventDispatcherInterface)
    {
        $this->operationMatcher = $operationMatcher;
        $this->eventDispatcherInterface = $eventDispatcherInterface;
    }

    /**
     * @param Patchable $subject
     * @return array
     * @throws MissingOperationRequest
     */
    public function handle(Patchable $subject)
    {
        foreach ($this->operationMatcher->getMatchedOperations() as $matchedPatchOperation) {
            $this->doHandle($matchedPatchOperation, $subject);
        }
    }

    public function doHandle(MatchedPatchOperation $matchedPatchOperation, $subject)
    {
        $event = new PatchManagerEvent($matchedPatchOperation);
        $this->eventDispatcherInterface->dispatch(PatchManagerEvents::PATCH_MANAGER_PRE, $event);
        $this->eventDispatcherInterface->dispatch(
            sprintf('%s.%s', PatchManagerEvents::PATCH_MANAGER_PRE, $matchedPatchOperation->getOpName()),
            $event
        );

        $matchedPatchOperation->process($subject);

        $this->eventDispatcherInterface->dispatch(PatchManagerEvents::PATCH_MANAGER_POST, $event);
        $this->eventDispatcherInterface->dispatch(
            sprintf('%s.%s', PatchManagerEvents::PATCH_MANAGER_POST, $matchedPatchOperation->getOpName()),
            $event
        );
    }
}
