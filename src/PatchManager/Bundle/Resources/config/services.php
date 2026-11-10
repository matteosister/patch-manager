<?php

declare(strict_types=1);

use Cypress\PatchManager\Bundle\RequestAdapter\RequestStackAdapter;
use Cypress\PatchManager\OperationMatcher;
use Cypress\PatchManager\PatchManager;
use Cypress\PatchManager\Request\Operations;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('patch_manager.request_adapter', RequestStackAdapter::class)
        ->arg(0, service('request_stack'));

    $services->set('patch_manager.operations', Operations::class)
        ->private()
        ->arg(0, service('patch_manager.request_adapter'));

    $services->set('patch_manager.operation_matcher', OperationMatcher::class)
        ->private()
        ->arg(0, service('patch_manager.operations'));

    $services->set('patch_manager', PatchManager::class)
        ->public()
        ->arg(0, service('patch_manager.operation_matcher'))
        ->arg(1, '%patch_manager.strict_mode%');
};
