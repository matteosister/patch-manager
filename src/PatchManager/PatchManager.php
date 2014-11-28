<?php

namespace PatchManager;

use PatchManager\Event\PatchManagerEvent;
use PatchManager\Event\PatchManagerEvents;
use PatchManager\Exception\HandlerNotFoundException;
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
     * @var bool
     */
    private $strictMode;

    /**
     * @param OperationMatcher $operationMatcher
     * @param bool $strictMode if true throws an error if no handler is found
     */
    public function __construct(OperationMatcher $operationMatcher, $strictMode = false)
    {
        $this->operationMatcher = $operationMatcher;
        $this->strictMode = $strictMode;
    }

    /**
     * @param EventDispatcherInterface $eventDispatcherInterface
     */
    public function setEventDispatcherInterface($eventDispatcherInterface)
    {
        $this->eventDispatcherInterface = $eventDispatcherInterface;
    }

    /**
     * @param Patchable|array|\Traversable $subject a Patchable instance or a collection of instances
     * @throws HandlerNotFoundException
     * @return array
     */
    public function handle($subject)
    {
        if ($this->strictMode && $this->operationMatcher->getMatchedOperations()->isEmpty()) {
            throw new HandlerNotFoundException($this->operationMatcher->getUnmatchedOperations());
        }
        if (is_array($subject) || $subject instanceof \Traversable) {
            $this->handleMany($subject);
        } else {
            foreach ($this->operationMatcher->getMatchedOperations() as $matchedPatchOperation) {
                $this->doHandle($matchedPatchOperation, $subject);
            }
        }

    }

    /**
     * @param array|\Traversable $subjects
     */
    private function handleMany($subjects)
    {
        foreach ($this->operationMatcher->getMatchedOperations() as $matchedPatchOperation) {
            foreach ($subjects as $subject) {
                $this->doHandle($matchedPatchOperation, $subject);
            }
        }
    }

    /**
     * @param MatchedPatchOperation $matchedPatchOperation
     * @param $subject
     */
    public function doHandle(MatchedPatchOperation $matchedPatchOperation, $subject)
    {
        $event = new PatchManagerEvent($matchedPatchOperation, $subject);
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
