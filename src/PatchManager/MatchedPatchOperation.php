<?php

declare(strict_types=1);

namespace Cypress\PatchManager;

use Symfony\Component\OptionsResolver\OptionsResolver;

class MatchedPatchOperation
{
    private array $operationData;

    private PatchOperationHandler $handler;

    private function __construct(array $operationData, PatchOperationHandler $handler)
    {
        $this->operationData = $operationData;
        $this->handler = $handler;
    }

    public static function create(array $operationData, PatchOperationHandler $handler): MatchedPatchOperation
    {
        return new self($operationData, $handler);
    }

    public function matchFor(string $operationName): bool
    {
        return $operationName === $this->handler->getName();
    }

    /**
     * call handle on the handler
     */
    public function process(Patchable $patchable): void
    {
        $optionResolver = new OptionsResolver();
        $optionResolver->setRequired(['op']);
        $this->handler->configureOptions($optionResolver);
        $options = new OperationData($optionResolver->resolve($this->operationData));
        $this->handler->handle($patchable, $options);
    }

    public function getOpName(): string
    {
        return $this->handler->getName();
    }
}
