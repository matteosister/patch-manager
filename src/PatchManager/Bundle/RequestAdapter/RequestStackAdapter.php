<?php

namespace PatchManager\Bundle\RequestAdapter;

use Symfony\Component\HttpFoundation\RequestStack;

class RequestStackAdapter
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
}
