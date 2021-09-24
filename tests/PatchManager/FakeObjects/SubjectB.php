<?php

namespace Cypress\PatchManager\Tests\FakeObjects;

use Cypress\PatchManager\Patchable as IPatchable;

class SubjectB implements IPatchable
{
    private int $a = 1;
}
