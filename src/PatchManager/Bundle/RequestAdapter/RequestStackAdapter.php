<?php

namespace Cypress\PatchManager\Bundle\RequestAdapter;

use Cypress\PatchManager\Request\Adapter;
use Cypress\PatchManager\Request\Operations;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestStackAdapter implements Adapter
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param Operations $operations
     */
    public function setRequestBody(Operations $operations)
    {
        $operations->setRequestBody($this->requestStack->getCurrentRequest()->getContent());
    }
}
