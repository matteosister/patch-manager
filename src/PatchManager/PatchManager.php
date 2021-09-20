<?php

namespace Cypress\PatchManager;

use Cypress\PatchManager\Event\PatchManagerEvent;
use Cypress\PatchManager\Event\PatchManagerEvents;
use Cypress\PatchManager\Exception\HandlerNotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * The main entry point for the PatchManager bundle
 */
class PatchManager
{
    /**
     * @var OperationMatcher
     */
    private OperationMatcher $operationMatcher;

    /**
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcherInterface;

    /**
     * @var bool
     */
    private bool $strictMode;

    /**
     * @param OperationMatcher $operationMatcher
     * @param bool $strictMode if true throws an error if no handler is found
     */
    public function __construct(OperationMatcher $operationMatcher, bool $strictMode = false)
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
        $matchedOperations = $this->operationMatcher->getMatchedOperations($subject);
        if ($this->strictMode && $matchedOperations->isEmpty()) {
            throw new HandlerNotFoundException($this->operationMatcher->getUnmatchedOperations($subject));
        }
        if (is_array($subject) || $subject instanceof \Traversable) {
            $this->handleMany($subject);
        } else {
            foreach ($matchedOperations as $matchedPatchOperation) {
                $this->doHandle($matchedPatchOperation, $subject);
            }
        }

    }

    /**
     * @param array|\Traversable $subjects
     * @throws Exception\MissingOperationNameRequest
     * @throws Exception\MissingOperationRequest
     */
    private function handleMany($subjects)
    {
        foreach ($subjects as $subject) {
            foreach ($this->operationMatcher->getMatchedOperations($subject) as $matchedPatchOperation) {
                $this->doHandle($matchedPatchOperation, $subject);
            }
        }
    }

    /**
     * @param MatchedPatchOperation $matchedPatchOperation
     * @param $subject
     */
    protected function doHandle(MatchedPatchOperation $matchedPatchOperation, $subject)
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
     * @param string $opName
     * @param $type
     */
    protected function dispatchEvents(PatchManagerEvent $event, string $opName, string $type): void
    {
        if (!$this->eventDispatcherInterface) {
            return;
        }
        $this->eventDispatcherInterface->dispatch($event, $type);
        $this->eventDispatcherInterface->dispatch(
            $event,
            sprintf('%s.%s', $type, $opName)
        );
    }
}
