<?php

namespace PatchManager\Event;

final class PatchManagerEvents
{
    /**
     * this event gets fired before calling an handler
     */
    const PATCH_MANAGER_PRE = 'patch_manager.pre';

    /**
     * this event gets fired after calling an handler
     */
    const PATCH_MANAGER_POST = 'patch_manager.post';
}
