<?php

namespace Cypress\PatchManagerBundle\PatchManager\Request;

use Cypress\PatchManagerBundle\Exception\InvalidJsonRequestContent;
use Cypress\PatchManagerBundle\Exception\MissingOperationRequest;
use PhpCollection\Sequence;
use Symfony\Component\HttpFoundation\RequestStack;

class Operations
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
     * directly from stack overflow: http://stackoverflow.com/a/6041773
     * check if a string is valid json, and returns the parsed content
     *
     * @param $string
     *
     * @throws InvalidJsonRequestContent
     * @return array
     */
    protected function parseJson($string)
    {
        $parsedContent = json_decode($string, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidJsonRequestContent;
        }
        return $parsedContent;
    }

    /**
     * @param array $arr
     * @return bool
     */
    private function isAssociative($arr)
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * @throws InvalidJsonRequestContent
     * @throws MissingOperationRequest
     *
     * @return Sequence
     */
    public function all()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (! $currentRequest->isMethod('PATCH')) {
            return new Sequence();
        }
        $operations =$this->parseJson($currentRequest->getContent());
        if (! is_array($operations)) {
            throw new MissingOperationRequest();
        }
        return new Sequence($this->isAssociative($operations) ? [$operations] : $operations);
    }
}
