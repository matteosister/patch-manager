<?php

namespace Cypress\PatchManager\Exception;

use PhpCollection\Sequence;

class HandlerNotFoundException extends PatchManagerException
{
    public function __construct(Sequence $unmatchedOperations)
    {
        $message = 'No handler was found ';
        if ($unmatchedOperations->count() > 1) {
            $message .= 'for the operators \''.implode(', ', $unmatchedOperations->all()).'\'. ';
        } else {
            $message .= 'for the operator \''.$unmatchedOperations->first()->get().'\'. ';
        }
        $message .= 'This is not allowed with strict_mode enabled';
        parent::__construct($message);
    }
}
