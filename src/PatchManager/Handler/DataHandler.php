<?php

namespace PatchManager\Handler;

use PatchManager\OperationData;
use PatchManager\Patchable;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class DataHandler implements PatchOperationHandler
{
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
     * @param Patchable $patchable
     * @param OperationData $operationData
     */
    public function handle(Patchable $patchable, OperationData $operationData)
    {
        $pa = new PropertyAccessor();
    }

    /**
     * returns an array of keys required by the operation to be fulfilled.
     * "op" should not be included as it's required by the library.
     * i.e.: for a data operation we should require a property and a value,
     * so this method should returns array("property", "value")
     * The required json content for a PATCH request would be: {"op":"data","property":"name","value":"new-value"}
     *
     * @return array
     */
    public function getRequiredKeys()
    {
        return array('property', 'value');
    }
}