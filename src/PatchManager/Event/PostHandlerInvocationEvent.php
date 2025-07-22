<?php

declare(strict_types=1);

namespace Cypress\PatchManager\Event;

use Cypress\PatchManager\MatchedPatchOperation;
use Cypress\PatchManager\Patchable;

class PostHandlerInvocationEvent extends PatchManagerEvent
{
    public function __construct(
        MatchedPatchOperation $matchedPatchOperation,
        Patchable $subject,
        private readonly ?\Throwable $exception = null
    ) {
        parent::__construct(
            matchedPatchOperation: $matchedPatchOperation,
            subject: $subject
        );
    }

    public function getException(): ?\Throwable
    {
        return $this->exception;
    }
}
