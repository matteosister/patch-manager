<?php

require_once __DIR__.'/../vendor/autoload.php';

use PatchManager\Request\Operations;
use PatchManager\OperationMatcher;
use PatchManager\Handler\DataHandler;
use PatchManager\PatchManager;

class Subject implements \PatchManager\Patchable
{
    private $a = 1;

    public function setA($a)
    {
        $this->a = $a;
    }

    public function getA()
    {
        return $this->a;
    }
}

$operations = new Operations();
$operations->setRequestBody('{"op": "data", "property": "a", "value": 2}');
$operationMatcher = new OperationMatcher($operations);
$dataHandler = new DataHandler();
$dataHandler->useMagicCall(false);
$operationMatcher->addHandler($dataHandler);
$pm = new PatchManager($operationMatcher);

$subject = new Subject();
echo sprintf("value of 'a' property: %s\n", $subject->getA());
echo "Patch manager operation\n";
$pm->handle($subject);
echo sprintf("value of 'a' property: %s\n", $subject->getA());
