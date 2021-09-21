<?php

namespace Cypress\PatchManager;

use Symfony\Component\OptionsResolver\OptionsResolver;

class MatchedPatchOperation
{
    /**
     * @var array
     */
    private array $operationData;

    /**
     * @var PatchOperationHandler
     */
    private PatchOperationHandler $handler;

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
    public function matchFor(string $operationName): bool
    {
        return $operationName === $this->handler->getName();
    }

    /**
     * call handle on the handler
     *
     * @param Patchable $patchable
     */
    public function process(Patchable $patchable): void
    {
        $optionResolver = new OptionsResolver();
        $optionResolver->setRequired(['op']);
        $this->handler->configureOptions($optionResolver);
        $options = new OperationData($optionResolver->resolve($this->operationData));
        $this->handler->handle($patchable, $options);
    }

    /**
     * @return string
     */
    public function getOpName()
    {
        return $this->handler->getName();
    }
}
