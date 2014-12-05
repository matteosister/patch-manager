<?php

namespace PatchManager\Request;

/**
 * Interface Adapter
 *
 * Every Request adapter should implement this interface
 */
interface Adapter
{
    /**
     * @param Operations $operations
     * @return void
     */
    public function setRequestBody(Operations $operations);
}
