<?php

namespace Cypress\PatchManager\Event;

use Cypress\PatchManager\MatchedPatchOperation;
use Cypress\PatchManager\Patchable;
use Symfony\Contracts\EventDispatcher\Event;

class PatchManagerEvent extends Event
{
    /**
     * @var MatchedPatchOperation
     */
    private MatchedPatchOperation $matchedPatchOperation;

    /**
     * @var Patchable
     */
    private Patchable $subject;

    /**
     * @param MatchedPatchOperation $matchedPatchOperation
     * @param Patchable $subject
     */
    public function __construct(MatchedPatchOperation $matchedPatchOperation, Patchable $subject)
    {
        $this->matchedPatchOperation = $matchedPatchOperation;
        $this->subject = $subject;
    }

    /**
     * @return MatchedPatchOperation
     */
    public function getMatchedPatchOperation(): MatchedPatchOperation
    {
        return $this->matchedPatchOperation;
    }

    /**
     * @return Patchable
     */
    public function getSubject(): Patchable
    {
        return $this->subject;
    }
}
