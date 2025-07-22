<?php

declare(strict_types=1);

namespace Cypress\PatchManager\Event;

final class PatchManagerEvents
{
    /**
     * this event gets fired before calling a handler
     */
    public const string PATCH_MANAGER_PRE = 'patch_manager.pre';

    /**
     * this event gets fired after calling a handler
     */
    public const string PATCH_MANAGER_POST = 'patch_manager.post';
}
