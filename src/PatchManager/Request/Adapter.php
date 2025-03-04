<?php

declare(strict_types=1);

namespace Cypress\PatchManager\Request;

/**
 * Interface Adapter
 *
 * Every Request adapter should implement this interface
 */
interface Adapter
{
    public function getRequestBody(): ?string;
}
