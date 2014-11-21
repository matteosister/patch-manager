<?php

namespace PatchManager\Handler;

use Doctrine\ORM\EntityManagerInterface;
use PatchManager\OperationData;
use PatchManager\Patchable;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Doctrine\Common\Persistence\Proxy;

class DataDoctrineHandler extends DataHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManagerInterface;

    /**
     * @param EntityManagerInterface $entityManagerInterface
     */
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManagerInterface = $entityManagerInterface;
    }

    /**
     * @param mixed $subject
     * @param OperationData $operationData
     */
    public function handle($subject, OperationData $operationData)
    {
        $pa = new PropertyAccessor($this->magicCall);
        $property = $operationData->get('property')->get();
        $value = $operationData->get('value')->get();
        if ($this->isEntity($subject)) {
            // if it's an associated entity I fetch if from the db
            $metadata = $this->entityManagerInterface->getMetadataFactory()->getMetadataFor(get_class($subject));
            if (in_array($property, $metadata->getAssociationNames())) {
                $targetClass = $metadata->getAssociationTargetClass($property);
                $value = $this->entityManagerInterface->find($targetClass, $value);
            }
            // if it's a date field I cast the value to date
            $fieldType = $metadata->getTypeOfField($property);
            if ('date' === $fieldType) {
                $value = new \DateTime($value);
            }
        }
        $pa->setValue($subject, $property, $value);
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
