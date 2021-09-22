<?php

namespace Cypress\PatchManager;

use Cypress\PatchManager\Event\PatchManagerEvent;
use Cypress\PatchManager\Event\PatchManagerEvents;
use Cypress\PatchManager\Exception\HandlerNotFoundException;
use PhpCollection\Sequence;
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
    public function setEventDispatcherInterface(EventDispatcherInterface $eventDispatcherInterface): void
    {
        $this->eventDispatcherInterface = $eventDispatcherInterface;
    }

    /**
     * @param array<Patchable>|Patchable|\Traversable $subject a Patchable instance or a collection of instances
     * @throws Exception\InvalidJsonRequestContent
     * @throws Exception\MissingOperationNameRequest
     * @throws Exception\MissingOperationRequest
     * @throws HandlerNotFoundException
     */
    public function handle($subject): void
    {
        $matchedOperations = $this->getMatchedOperations($subject);
        $this->handleSubject($subject, $matchedOperations);
    }

    /**
     * @param MatchedPatchOperation $matchedPatchOperation
     * @param Patchable $subject
     */
    protected function doHandle(MatchedPatchOperation $matchedPatchOperation, Patchable $subject): void
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
     * @param string $type
     */
    protected function dispatchEvents(PatchManagerEvent $event, string $opName, string $type): void
    {
        if (!isset($this->eventDispatcherInterface)) {
            return;
        }
        $this->eventDispatcherInterface->dispatch($event, $type);
        $this->eventDispatcherInterface->dispatch(
            $event,
            sprintf('%s.%s', $type, $opName)
        );
    }

    /**
     * @param array|Patchable|\Traversable $subject a Patchable instance or a collection of instances
     * @throws Exception\InvalidJsonRequestContent
     * @throws Exception\MissingOperationNameRequest
     * @throws Exception\MissingOperationRequest
     * @throws HandlerNotFoundException
     * @return Sequence
     */
    private function getMatchedOperations($subject): Sequence
    {
        $matchedOperations = $this->operationMatcher->getMatchedOperations($subject);
        if ($this->strictMode && $matchedOperations->isEmpty()) {
            throw new HandlerNotFoundException($this->operationMatcher->getUnmatchedOperations($subject));
        }

        return $matchedOperations;
    }

    /**
     * @param array|Patchable|\Traversable $subject a Patchable instance or a collection of instances
     * @param Sequence $matchedOperations
     * @throws Exception\MissingOperationNameRequest
     * @throws Exception\MissingOperationRequest
     */
    private function handleSubject($subject, Sequence $matchedOperations): void
    {
        if (is_array($subject) || $subject instanceof \Traversable) {
            $this->handleMany($subject);

            return;
        }

        foreach ($matchedOperations as $matchedPatchOperation) {
            $this->doHandle($matchedPatchOperation, $subject);
        }
    }

    /**
     * @param array|\Traversable $subjects
     * @throws Exception\InvalidJsonRequestContent
     * @throws Exception\MissingOperationNameRequest
     * @throws Exception\MissingOperationRequest
     */
    private function handleMany($subjects): void
    {
        foreach ($subjects as $subject) {
            foreach ($this->operationMatcher->getMatchedOperations($subject) as $matchedPatchOperation) {
                $this->doHandle($matchedPatchOperation, $subject);
            }
        }
    }
}
