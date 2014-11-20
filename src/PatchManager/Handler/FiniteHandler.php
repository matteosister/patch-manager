<?php

namespace PatchManager\Handler;

use Finite\Factory\FactoryInterface;
use PatchManager\OperationData;
use PatchManager\Patchable;
use PatchManager\PatchOperationHandler;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiniteHandler implements PatchOperationHandler
{
    /**
     * @var FactoryInterface
     */
    private $factoryInterface;

    /**
     * @param FactoryInterface $factoryInterface
     */
    public function __construct(FactoryInterface $factoryInterface)
    {
        $this->factoryInterface = $factoryInterface;
    }

    /**
     * @param Patchable $patchable
     * @param OperationData $operationData
     */
    public function handle(Patchable $patchable, OperationData $operationData)
    {
        $sm = $this->factoryInterface->get($patchable);
        $transition = $operationData->get('transition')->get();
        if ($operationData->get('check')->get() && ! $sm->can($transition)) {
            return;
        }
        $sm->apply($transition);
    }

    /**
     * the operation name
     *
     * @return string
     */
    public function getName()
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
    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver
            ->setRequired(array('transition'))
            ->setOptional(array('check'))
            ->setDefaults(array('check' => false));
    }
}