<?php

namespace Cypress\PatchManager\Exception;

class InvalidJsonRequestContent extends PatchManagerException
{
    /**
     * @var string
     */
    protected $message = "The Request passed to the PatchManagerHandler contains invalid json data";
}
