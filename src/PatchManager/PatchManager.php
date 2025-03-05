<?php

declare(strict_types=1);

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
    private OperationMatcher $operationMatcher;

    private EventDispatcherInterface $eventDispatcherInterface;

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

    protected function doHandle(MatchedPatchOperation $matchedPatchOperation, Patchable $subject): void
    {
        $event = new PatchManagerEvent($matchedPatchOperation, $subject);
        $this->dispatchEvents($event, $matchedPatchOperation->getOpName(), PatchManagerEvents::PATCH_MANAGER_PRE);

        $matchedPatchOperation->process($subject);
        $this->dispatchEvents($event, $matchedPatchOperation->getOpName(), PatchManagerEvents::PATCH_MANAGER_POST);
    }

    /**
     * dispatch events if the eventDispatcher is present
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
     * @param iterable|Patchable $subject a Patchable instance or a collection of instances
     * @throws Exception\MissingOperationNameRequest
     * @throws Exception\MissingOperationRequest
     * @throws Exception\InvalidJsonRequestContent
     */
    private function handleSubject(Patchable|iterable $subject, Sequence $matchedOperations): void
    {
        if (is_iterable($subject)) {
            $this->handleMany($subject);

            return;
        }

        foreach ($matchedOperations as $matchedPatchOperation) {
            $this->doHandle($matchedPatchOperation, $subject);
        }
    }

    /**
     * @param array<Patchable>|\Traversable<Patchable> $subjects
     * @throws Exception\InvalidJsonRequestContent
     * @throws Exception\MissingOperationNameRequest
     * @throws Exception\MissingOperationRequest
     */
    private function handleMany(iterable $subjects): void
    {
        foreach ($subjects as $subject) {
            foreach ($this->operationMatcher->getMatchedOperations($subject) as $matchedPatchOperation) {
                $this->doHandle($matchedPatchOperation, $subject);
            }
        }
    }
}
