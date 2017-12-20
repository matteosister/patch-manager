<?php

namespace Cypress\PatchManager\Event;

use Cypress\PatchManager\MatchedPatchOperation;
use Cypress\PatchManager\Patchable;
use Symfony\Component\EventDispatcher\Event;

class PatchManagerEvent extends Event
{
    /**
     * @var MatchedPatchOperation
     */
    private $matchedPatchOperation;

    /**
     * @var Patchable
     */
    private $subject;

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
    public function getMatchedPatchOperation()
    {
        return $this->matchedPatchOperation;
    }

    /**
     * @return Patchable
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
