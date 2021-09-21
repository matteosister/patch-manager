<?php

namespace Cypress\PatchManager\Handler;

use Cypress\PatchManager\OperationData;
use Cypress\PatchManager\PatchOperationHandler;
use Cypress\PatchManager\Patchable;
use Finite\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiniteHandler implements PatchOperationHandler
{
    /**
     * @var FactoryInterface
     */
    private FactoryInterface $factoryInterface;

    /**
     * @param FactoryInterface $factoryInterface
     */
    public function __construct(FactoryInterface $factoryInterface)
    {
        $this->factoryInterface = $factoryInterface;
    }

    /**
     * @param Patchable $subject
     * @param OperationData $operationData
     */
    public function handle(Patchable $subject, OperationData $operationData): void
    {
        $sm = $this->factoryInterface->get($subject);
        $transition = $operationData->get('transition')->get();
        if ($operationData->get('check')->get() && !$sm->can($transition)) {
            return;
        }
        $sm->apply($transition);
    }

    /**
     * the operation name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'sm';
    }

    /**
     * use the OptionResolver instance to configure the required and optional fields that needs to be passed
     * with the request body. See http://symfony.com/doc/current/components/options_resolver.html to check all
     * possible options
     *
     * @param OptionsResolver $optionsResolver
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
     *
     * @param $subject
     * @return bool
     */
    public function canHandle($subject): bool
    {
        return true;
    }
}
