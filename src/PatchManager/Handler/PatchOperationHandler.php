<?php

namespace PatchManager\Handler;

use PatchManager\Patchable;

interface PatchOperationHandler
{
    /**
     * the operation name
     *
     * @return string
     */
    public function getName();

    /**
     * @param Patchable $patchable
     * @param array $operationData
     *
     * @return
     */
    public function handle(Patchable $patchable, array $operationData);

    /**
     * returns an array of keys required by the operation to be fulfilled
     * "op" should not be included as it's required by the library
     * i.e.: for a data operation we should require a property and a value,
     * so this method should returns array("property", "value")
     * The required json content for a PATCH request would be {"op":"data","property":"name","value":"new-value"}
     *
     * @return array
     */
    public function getRequiredKeys();
}
