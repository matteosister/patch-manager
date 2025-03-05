<?php

declare(strict_types=1);

namespace Cypress\PatchManager;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface PatchOperationHandler
{
    /**
     * implement here the logic for the handler
     */
    public function handle(Patchable $subject, OperationData $operationData): void;

    /**
     * the operation name
     */
    public function getName(): string;

    /**
     * use the OptionResolver instance to configure the required and optional fields that needs to be passed
     * with the request body. See http://symfony.com/doc/current/components/options_resolver.html to check all
     * possible options
     */
    public function configureOptions(OptionsResolver $optionsResolver): void;

    /**
     * whether the handler is able to handle the given subject
     */
    public function canHandle(Patchable $subject): bool;
}
