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
     * @var Operations
     */
    private $operations;

    /**
     * @var Sequence
     */
    private $handlers;

    /**
     * @param Operations $operations
     */
    public function __construct(Operations $operations)
    {
        $this->handlers = new Sequence();
        $this->operations = $operations;
    }



    /**
     * @param Patchable $subject
     * @return array
     * @throws \Cypress\PatchManagerBundle\Exception\MissingOperationRequest
     */
    public function handle(Patchable $subject)
    {
        $events = [];
        $this->operations->all()
            ->map($this->handleOperation($events));
        return $events;
    }

    private function handleOperation(&$events = [])
    {
        return function ($operationData) use (&$events) {
            var_dump('handle');
            var_dump($operationData);
        };
    }
}
