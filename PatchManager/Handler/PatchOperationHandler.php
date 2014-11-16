<?php

namespace Cypress\PatchManagerBundle\PatchManager\Handler;

interface PatchOperationHandler
{
    /**
     * the operation name
     *
     * @return string
     */
    public function getName();

    /**
     * @param Patchable $patchable
     */
    public function handle(Patchable $patchable);
}
