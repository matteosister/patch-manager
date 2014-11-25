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
     */
    public function __construct(OperationMatcher $operationMatcher)
    {
        $this->operationMatcher = $operationMatcher;
    }

    /**
     * @param EventDispatcherInterface $eventDispatcherInterface
     */
    public function setEventDispatcherInterface($eventDispatcherInterface)
    {
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

    /**
     * @param MatchedPatchOperation $matchedPatchOperation
     * @param $subject
     */
    public function doHandle(MatchedPatchOperation $matchedPatchOperation, $subject)
    {
        $event = new PatchManagerEvent($matchedPatchOperation);
        $this->dispatchEvents($event, $matchedPatchOperation->getOpName(), PatchManagerEvents::PATCH_MANAGER_PRE);
        $matchedPatchOperation->process($subject);
        $this->dispatchEvents($event, $matchedPatchOperation->getOpName(), PatchManagerEvents::PATCH_MANAGER_POST);
    }

    /**
     * dispatch events if the eventDispatcher is present
     *
     * @param PatchManagerEvent $event
     * @param $opName
     * @param $type
     */
    public function dispatchEvents(PatchManagerEvent $event, $opName, $type)
    {
        if (! $this->eventDispatcherInterface) {
            return;
        }
        $this->eventDispatcherInterface->dispatch(PatchManagerEvents::PATCH_MANAGER_POST, $event);
        $this->eventDispatcherInterface->dispatch(
            sprintf('%s.%s', $type, $opName),
            $event
        );
    }
}
