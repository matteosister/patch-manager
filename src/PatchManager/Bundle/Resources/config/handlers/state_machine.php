<?php

declare(strict_types=1);

use Cypress\PatchManager\Handler\FiniteHandler;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set('patch_manager.handler.state_machine', FiniteHandler::class)
        ->private();
};
