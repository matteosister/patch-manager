<?php

namespace PatchManager\Event;

use PatchManager\MatchedPatchOperation;
use Symfony\Component\EventDispatcher\Event;

class PatchManagerEvent extends Event
{
    /**
     * @var MatchedPatchOperation
     */
    private $matchedPatchOperation;

    /**
     * @param MatchedPatchOperation $matchedPatchOperation
     */
    public function __construct(MatchedPatchOperation $matchedPatchOperation)
    {
        $this->matchedPatchOperation = $matchedPatchOperation;
    }
}
