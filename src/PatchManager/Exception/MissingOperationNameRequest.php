<?php

namespace Cypress\PatchManager\Exception;

use Exception;

class MissingOperationNameRequest extends \Exception
{
    public function __construct(array $operationData, $message = "", $code = 0, Exception $previous = null)
    {
        $message = "You passed an operation without a name to the PatchManager. The json should contains
            an 'op' key. Here is the patch operation that failed: ".json_encode($operationData);
        parent::__construct($message, $code, $previous);
    }
}
