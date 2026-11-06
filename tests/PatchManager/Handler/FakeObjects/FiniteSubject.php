<?php

declare(strict_types=1);

namespace Cypress\PatchManager\Tests\Handler\FakeObjects;

use Cypress\PatchManager\Patchable;
use Finite\State;
use Finite\Transition\Transition;

enum FiniteSubjectState: string implements State
{
    case S1 = 's1';
    case S2 = 's2';
    case S3 = 's3';

    public static function getTransitions(): array
    {
        return [
            new Transition('t12', [self::S1], self::S2),
            new Transition('t23', [self::S2], self::S3),
        ];
    }
}

class FiniteSubject implements Patchable
{
    private FiniteSubjectState $state = FiniteSubjectState::S1;

    public function getFiniteState(): FiniteSubjectState
    {
        return $this->state;
    }

    public function setFiniteState(FiniteSubjectState $state): void
    {
        $this->state = $state;
    }
}
