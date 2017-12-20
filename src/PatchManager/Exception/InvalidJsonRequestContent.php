<?php

namespace Cypress\PatchManager\Exception;

class InvalidJsonRequestContent extends PatchManagerException
{
    protected $message = "The Request passed to the PatchManagerHandler contains invalid json data";
}
