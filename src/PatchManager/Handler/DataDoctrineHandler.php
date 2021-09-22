<?php

namespace Cypress\PatchManager\Handler;

use Cypress\PatchManager\OperationData;
use Cypress\PatchManager\Patchable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Proxy;
use Symfony\Component\PropertyAccess\PropertyAccess;

class DataDoctrineHandler extends DataHandler
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManagerInterface;

    /**
     * @param EntityManagerInterface $entityManagerInterface
     */
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManagerInterface = $entityManagerInterface;
    }

    /**
     * @param Patchable $subject
     * @param OperationData $operationData
     * @throws \Doctrine\Persistence\Mapping\MappingException
     * @throws \ReflectionException
     */
    public function handle(Patchable $subject, OperationData $operationData): void
    {
        $propertyAccessorBuilder = PropertyAccess::createPropertyAccessorBuilder();
        $propertyAccessorBuilder = $this->magicCall ? $propertyAccessorBuilder->enableMagicCall() : $propertyAccessorBuilder;

        $propertyAccessor = $propertyAccessorBuilder->getPropertyAccessor();
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
        $propertyAccessor->setValue($subject, $property, $value);
    }

    /**
     * @param object|string $class
     *
     * @return bool
     */
    private function isEntity($class): bool
    {
        if (is_object($class)) {
            $class = $class instanceof Proxy ? get_parent_class($class) : get_class($class);

            return !$this->entityManagerInterface->getMetadataFactory()->isTransient($class);
        }

        return false;
    }
}
