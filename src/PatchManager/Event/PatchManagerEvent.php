<?php

declare(strict_types=1);

namespace Cypress\PatchManager\Event;

use Cypress\PatchManager\MatchedPatchOperation;
use Cypress\PatchManager\Patchable;
use Symfony\Contracts\EventDispatcher\Event;

class PatchManagerEvent extends Event
{
    private MatchedPatchOperation $matchedPatchOperation;

    private Patchable $subject;

    public function __construct(MatchedPatchOperation $matchedPatchOperation, Patchable $subject)
    {
        $this->matchedPatchOperation = $matchedPatchOperation;
        $this->subject = $subject;
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
