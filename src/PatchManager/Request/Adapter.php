<?php

namespace Cypress\PatchManager\Request;

/**
 * Interface Adapter
 *
 * Every Request adapter should implement this interface
 */
interface Adapter
{
    /**
     * @return null|string
     */
    public function getRequestBody(): ?string;
}
