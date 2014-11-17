<?php

namespace Cypress\PatchManagerBundle\PatchManager;

use Cypress\PatchManagerBundle\Exception\MissingKeysRequest;
use Cypress\PatchManagerBundle\PatchManager\Handler\PatchOperationHandler;
use Cypress\PatchManagerBundle\PatchManager\Request\Operations;
use PhpCollection\Sequence;

class MatchedPatchOperation
{
    /**
     * @var array
     */
    private $operationData;

    /**
     * @var PatchOperationHandler
     */
    private $handler;

    /**
     * @param array $operationData
     * @param PatchOperationHandler $handler
     */
    private function __construct(array $operationData, PatchOperationHandler $handler)
    {
        $this->operationData = $operationData;
        $this->handler = $handler;
    }

    /**
     * @param array $operationData
     * @param PatchOperationHandler $handler
     * @return MatchedPatchOperation
     */
    public static function create(array $operationData, PatchOperationHandler $handler)
    {
        return new self($operationData, $handler);
    }

    /**
     * @param string $operationName
     * @return bool
     */
    public function matchFor($operationName)
    {
        return $operationName === $this->handler->getName();
    }

    /**
     * call handle on the handler
     *
     * @param Patchable $patchable
     */
    public function process(Patchable $patchable)
    {
        $this->validate();
        $this->handler->handle($patchable, $this->operationData);
    }

    private function validate()
    {
        $opData = $this->operationData;
        unset($opData[Operations::OP_KEY_NAME]);
        $keys = array_keys($opData);
        $requiredKeys = new Sequence($this->handler->getRequiredKeys());
        $missingKeys = $requiredKeys->filterNot(function ($key) use ($keys) { return in_array($key, $keys); });
        if ($missingKeys->count() > 0) {
            throw new MissingKeysRequest($opData, $missingKeys);
        }
    }
}
