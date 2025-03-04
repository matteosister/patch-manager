<?php

declare(strict_types=1);

namespace Cypress\PatchManager\Event;

final class PatchManagerEvents
{
    /**
     * this event gets fired before calling an handler
     */
    public const PATCH_MANAGER_PRE = 'patch_manager.pre';

    /**
     * this event gets fired after calling an handler
     */
    public const PATCH_MANAGER_POST = 'patch_manager.post';
}
