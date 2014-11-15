<?php

namespace Cypress\PatchManagerBundle\Manager;

use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\EntityManagerInterface;
use PhpCollection\Sequence;
use Prima\Exception\Service\Ws\PatchManager\NonExistentOperationException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class PatchManager
 * @package Prima\Service\Ws
 */
class PatchManager
{
    const PATCH_OP_DATA = 'data';
    const PATCH_OP_METHOD = 'method';
    const PATCH_OP_STATE_MACHINE = 'sm';

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var EntityManagerInterface
     */
    private $entityManagerInterface;

    /**
     * @var FactoryInterface
     */
    private $finiteFactory;

    /**
     * @var Sequence
     */
    private $handlers;

    /**
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $entityManagerInterface
     * @param FactoryInterface $finiteFactory
     */
    public function __construct(
        RequestStack $requestStack,
        EntityManagerInterface $entityManagerInterface,
        FactoryInterface $finiteFactory
    ) {
        $this->requestStack = $requestStack;
        $this->entityManagerInterface = $entityManagerInterface;
        $this->finiteFactory = $finiteFactory;
        $this->handlers = new Sequence();
    }

    /**
     * @param PatchHandlerInterface $handler
     * @internal param $op
     * @internal param callable $callable
     */
    public function addHandler(PatchHandlerInterface $handler)
    {
        $this->handlers->add($handler);
    }

    /**
     * @param $subject
     * @return array
     *
     * @throws \Prima\Exception\Service\Ws\PatchManager\NonExistentOperationException
     */
    public function handle($subject)
    {
        if (!$this->getRequest()->isMethod('PATCH')) {
            throw new BadRequestHttpException('You should call this service using a request called with PATCH method');
        }
        if (empty($patchOptions = $this->getRequest()->request->all())) {
            throw new BadRequestHttpException('You should pass a patch option object like {\'op\':\'some_options\'}');
        }
        if ($this->isAssociative($patchOptions)) {
            $patchOptions = [$patchOptions];
        }
        $events = [];
        foreach ($patchOptions as $patchOption) {
            $flush = array_key_exists('flush', $patchOption) ? $patchOption['flush'] : false;
            $events = array_merge($events, $this->handlePatchOption($patchOption, $subject));
            if ($flush) {
                $this->entityManagerInterface->flush();
            }
        }
        return $events;
    }

    /**
     * @param array $arr
     * @return bool
     */
    private function isAssociative($arr)
    {
        if (empty($arr)) {
            throw new \RuntimeException('An empty array cannot be tested for isAssociative');
        }
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * @param $patchOption
     * @param $subject
     *
     * @return array
     *
     * @throws NonExistentOperationException
     */
    private function handlePatchOption($patchOption, $subject)
    {
        if (is_null($op = $patchOption['op'])) {
            throw new BadRequestHttpException('You should pass an \'op\' value');
        }
        $method = 'handle'.ucfirst($op);
        if (method_exists($this, $method)) {
            return $this->$method($patchOption, $subject);
        }
        $handlers = $this->handlers->filter(function (PatchHandlerInterface $handler) use ($op) {
            return $op === $handler->getOperation();
        });
        if ($handlers->isEmpty()) {
            $operationsList = join(', ', array_map(function (PatchHandlerInterface $handler) {
                return $handler->getOperation();
            }, $this->handlers->all()));
            throw new NonExistentOperationException(
                sprintf('No handlers to manage the \'%s\' op, available handlers: %s', $op, $operationsList)
            );
        }
        /** @var PatchHandlerInterface $handler */
        foreach ($handlers as $handler) {
            if ($handler instanceof PatchOptionReceiverInterface) {
                $handler->setPatchOption($patchOption);
            }
            $handler->handle($subject);
            return $this->createEvents([$handler->getOperation()], $subject);
        }
    }

    /**
     * handles "data" operations
     *
     * @param array $patchOption
     * @param $subject
     *
     * @return array
     */
    public function handleData($patchOption, $subject)
    {
        if (null === $field = $patchOption['field']) {
            throw new BadRequestHttpException('You should pass a "field" value, for a "data" patch operation');
        }
        $value = array_key_exists('value', $patchOption) ? $value = $patchOption['value'] : null;
        $propertyAccessor = new PropertyAccessor();
        if ($this->isEntity($subject)) {
            $metadata = $this->entityManagerInterface->getMetadataFactory()->getMetadataFor(get_class($subject));
            if (in_array($field, $metadata->getAssociationNames())) {
                $targetClass = $metadata->getAssociationTargetClass($field);
                $value = $this->entityManagerInterface->find($targetClass, $value);
            }
            $fieldType = $metadata->getTypeOfField($field);
            if ('date' === $fieldType) {
                $value = new \DateTime($value);
            }
        }
        $propertyAccessor->setValue($subject, $field, $value);
        return $this->createEvents([self::PATCH_OP_DATA, self::PATCH_OP_DATA.'.'.$field], $subject);
    }

    /**
     * handles "method" operations (calling a method on the object)
     *
     * @param $patchOption
     * @param PatchableInterface $subject
     *
     * @return array
     */
    public function handleMethod($patchOption, PatchableInterface $subject)
    {
        if (!array_key_exists('method', $patchOption)) {
            throw new BadRequestHttpException('You should pass a "method" value, for a "method" patch operation');
        }
        $method = $patchOption['method'];
        if (! in_array($method, $subject->getAllowedMethods())) {
            throw new BadRequestHttpException(sprintf('The method %s is not allowed', $method));
        }
        if (!is_callable([$subject, $method])) {
            throw new BadRequestHttpException(sprintf('The method %s do not exists on the subject', $method));
        }
        $arguments = array_key_exists('arguments', $patchOption) ? $patchOption['arguments'] : [];
        call_user_func_array([$subject, $method], $arguments);
        return $this->createEvents([self::PATCH_OP_METHOD, self::PATCH_OP_METHOD.'.'.$method], $subject);
    }

    /**
     * handles "sm" operations (applying a state machine transition on the object)
     *
     * @param $patchOption
     * @param StatefulInterface $subject
     * @return array
     */
    public function handleSm($patchOption, StatefulInterface $subject)
    {
        if (!array_key_exists('transition', $patchOption)) {
            throw new BadRequestHttpException('You should pass a "transition" value, for a "sm" patch operation');
        }
        $transition = $patchOption['transition'];
        $sm = $this->finiteFactory->get($subject);
        $sm->apply($transition);
        return [];
    }

    /**
     * @param $events
     * @param $subject
     *
     * @return array
     */
    private function createEvents($events, $subject)
    {
        $outputEvents = [];
        foreach ($events as $eventName) {
            $outputEvents['prima.patch_manager.' . $eventName] = new PatchManagerEvent($subject);
        }
        return $outputEvents;
    }

    /**
     * @return null|\Symfony\Component\HttpFoundation\Request
     */
    private function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * @param string|object $class
     *
     * @return boolean
     */
    private function isEntity($class)
    {
        if (is_object($class)) {
            $class = ($class instanceof Proxy)
                ? get_parent_class($class)
                : get_class($class);
            return ! $this->entityManagerInterface->getMetadataFactory()->isTransient($class);
        }
        return false;
    }
}