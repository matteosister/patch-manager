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
    public function handle($subject, OperationData $operationData);

    /**
     * the operation name
     *
     * @return string
     */
    public function getName();

    /**
     * use the OptionResolver instance to configure the required and optional fields that needs to be passed
     * with the request body. See http://symfony.com/doc/current/components/options_resolver.html to check all
     * possible options
     *
     * @param OptionsResolver $optionsResolver
     * @return void
     */
    public function configureOptions(OptionsResolver $optionsResolver);
}
