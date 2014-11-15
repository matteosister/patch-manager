<?php

namespace Cypress\PatchManagerBundle\Manager;

interface Patchable
{
    /**
     * returns an array of methods that can be called from the PatchManager
     *
     * @return array
     */
    public function getAllowedMethods();
}