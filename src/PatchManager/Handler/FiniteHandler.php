<?php

declare(strict_types=1);

namespace Cypress\PatchManager\Handler;

use Cypress\PatchManager\OperationData;
use Cypress\PatchManager\Patchable;
use Cypress\PatchManager\PatchOperationHandler;
use Finite\StateMachine;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiniteHandler implements PatchOperationHandler
{
    private StateMachine $stateMachine;

    public function __construct(StateMachine $stateMachine)
    {
        $this->stateMachine = $stateMachine;
    }

    public function handle(Patchable $subject, OperationData $operationData): void
    {
        $transition = (string) $operationData->get('transition')->get();
        if ($operationData->get('check')->get() && !$this->stateMachine->can($subject, $transition)) {
            return;
        }
        $this->stateMachine->apply($subject, $transition);
    }

    /**
     * the operation name
     */
    public function getName(): string
    {
        return 'sm';
    }

    /**
     * use the OptionResolver instance to configure the required and optional fields that needs to be passed
     * with the request body. See http://symfony.com/doc/current/components/options_resolver.html to check all
     * possible options
     */
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver
            ->setRequired(['transition'])
            ->setDefined(['check'])
            ->setDefaults(['check' => false]);
    }

    /**
     * whether the handler is able to handle the given subject
     */
    public function canHandle(Patchable $subject): bool
    {
        return true;
    }
}
