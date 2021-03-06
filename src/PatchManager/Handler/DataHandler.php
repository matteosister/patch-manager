<?php

namespace Cypress\PatchManager\Handler;

use Cypress\PatchManager\OperationData;
use Cypress\PatchManager\PatchOperationHandler;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class DataHandler implements PatchOperationHandler
{
    protected $magicCall = false;

    /**
     * @param mixed $magicCall
     */
    public function useMagicCall($magicCall)
    {
        $this->magicCall = $magicCall;
    }

    /**
     * @param mixed $subject
     * @param OperationData $operationData
     */
    public function handle($subject, OperationData $operationData)
    {
        $pa = new PropertyAccessor($this->magicCall);
        $pa->setValue(
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
    public function getName()
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
    public function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setRequired(array('property', 'value'));
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
