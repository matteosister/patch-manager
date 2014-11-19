<?php

require __DIR__.'/vendor/autoload.php';

$e = new PatchManager\Exception\MissingOperationRequest();

throw $e;