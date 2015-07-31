<?php


namespace Cypress\PatchManager\Exception;


class MissingOperationRequest extends PatchManagerException
{
    protected $message = "You can't call the PatchManager without passing an operation in the PATCH request body";
}
