<?php

namespace Cypress\PatchManager\Exception;

class MissingOperationNameRequest extends PatchManagerException
{
    public function __construct(array $operationData)
    {
        $message = "You passed an operation without a name to the PatchManager. The json should contains
            an 'op' key. Here is the patch operation that failed: ".json_encode($operationData);
        parent::__construct($message);
    }
}
