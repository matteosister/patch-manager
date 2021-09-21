<?php

namespace Cypress\PatchManager\Bundle\RequestAdapter;

use Cypress\PatchManager\Request\Adapter;
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
     * @return null|string
     */
    public function getRequestBody(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (is_null($request)) {
            return null;
        }

        return $request->getContent();
    }
}
