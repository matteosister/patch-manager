<?php

namespace Cypress\PatchManager;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface PatchOperationHandler
{
    /**
     * implement here the logic for the handler
     *
     * @param Patchable $subject
     * @param OperationData $operationData
     * @return void
     */
    public function handle(Patchable $subject, OperationData $operationData): void;

    /**
     * the operation name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * use the OptionResolver instance to configure the required and optional fields that needs to be passed
     * with the request body. See http://symfony.com/doc/current/components/options_resolver.html to check all
     * possible options
     *
     * @param OptionsResolver $optionsResolver
     * @return void
     */
    public function configureOptions(OptionsResolver $optionsResolver): void;

    /**
     * whether the handler is able to handle the given subject
     *
     * @param Patchable $subject
     * @return bool
     */
    public function canHandle(Patchable $subject): bool;
}
