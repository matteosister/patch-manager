<?php

namespace Cypress\PatchManagerBundle\Exception;

class InvalidJsonRequestContent extends PatchManagerException
{
    protected $message = "The Request passed to the PatchManagerHandler contains invalid json data";
} 