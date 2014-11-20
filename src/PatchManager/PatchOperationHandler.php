<?php

namespace PatchManager;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface PatchOperationHandler
{
    /**
     * @param Patchable $patchable
     * @param OperationData $operationData
     */
    public function handle(Patchable $patchable, OperationData $operationData);

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
     */
    public function configureOptions(OptionsResolver $optionsResolver);
}
