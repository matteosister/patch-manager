<?php

declare(strict_types=1);

namespace Cypress\PatchManager\Event;

use Cypress\PatchManager\MatchedPatchOperation;
use Cypress\PatchManager\Patchable;
use Symfony\Contracts\EventDispatcher\Event;

abstract class PatchManagerEvent extends Event
{
    public function __construct(
        private readonly MatchedPatchOperation $matchedPatchOperation,
        private readonly Patchable $subject
    ) {
    }

    public function getMatchedPatchOperation(): MatchedPatchOperation
    {
        return $this->matchedPatchOperation;
    }

    public function getSubject(): Patchable
    {
        return $this->subject;
    }
}
