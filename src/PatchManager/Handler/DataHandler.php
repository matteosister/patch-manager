<?php

namespace Cypress\PatchManager\Handler;

use Cypress\PatchManager\OperationData;
use Cypress\PatchManager\PatchOperationHandler;
use Cypress\PatchManager\Patchable;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class DataHandler implements PatchOperationHandler
{
    protected bool $magicCall = false;

    /**
     * @param bool $magicCall
     */
    public function useMagicCall(bool $magicCall): void
    {
        $this->magicCall = $magicCall;
    }

    /**
     * @param Patchable $subject
     * @param OperationData $operationData
     */
    public function handle(Patchable $subject, OperationData $operationData): void
    {
        $propertyAccessorBuilder = PropertyAccess::createPropertyAccessorBuilder();
        $propertyAccessorBuilder = $this->magicCall? $propertyAccessorBuilder->enableMagicCall() : $propertyAccessorBuilder;

        $propertyAccessor = $propertyAccessorBuilder->getPropertyAccessor();
        $propertyAccessor->setValue(
            $subject,
            $operationData->get('property')->get(),
            $operationData->get('value')->get()
        );
    }

    /**
     * the operation name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'data';
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
        $optionsResolver->setRequired(['property', 'value']);
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
