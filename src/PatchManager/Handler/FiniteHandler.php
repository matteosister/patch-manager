<?php

namespace Cypress\PatchManager\Handler;

use Finite\Factory\FactoryInterface;
use Cypress\PatchManager\OperationData;
use Cypress\PatchManager\PatchOperationHandler;
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
     * @param mixed $subject
     * @param OperationData $operationData
     */
    public function handle($subject, OperationData $operationData)
    {
        $sm = $this->factoryInterface->get($subject);
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
            ->setDefined(array('check'))
            ->setDefaults(array('check' => false));
    }

    /**
     * wether the handler is able to handle the given subject
     *
     * @param $subject
     * @return bool
     */
    public function canHandle($subject)
    {
        return true;
    }
}
