<?php

namespace App\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\AssociationMapping;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ManyToManyAssociationMapping;
use Doctrine\ORM\Mapping\OneToManyAssociationMapping;
use Doctrine\ORM\Mapping\OneToOneAssociationMapping;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;


class IsResourceNotReferencedValidator extends ConstraintValidator
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function validate($entity, Constraint $constraint): void
    {
        if (!$constraint instanceof IsResourceNotReferenced) {
            throw new UnexpectedTypeException($constraint, IsResourceNotReferenced::class);
        }

        $entityClass = get_class($entity);
        $metadataFactory = $this->em->getMetadataFactory();
        $allMetadata = $metadataFactory->getAllMetadata();
        $references = [];

        foreach ($allMetadata as $classMetadata) {
            foreach ($classMetadata->getAssociationMappings() as $mapping) {
                // Skip if this association doesn't target our entity
                if ($mapping->targetEntity !== $entityClass) {
                    continue;
                }

                // Skip if $sourceClass is a view
                if (str_starts_with($this->getEntityName($mapping->sourceEntity), 'Vw')) {
                    continue;
                }

                // Check for onDelete=CASCADE in the owning side's join columns
                $hasCascadeDelete = false;
                if (isset($mapping['joinColumns'])) {
                    foreach ($mapping['joinColumns'] as $joinColumn) {
                        if (isset($joinColumn['onDelete']) && strtoupper($joinColumn['onDelete']) === 'CASCADE') {
                            $hasCascadeDelete = true;
                            break;
                        }
                    }
                }

                if ($hasCascadeDelete) {
                    continue;
                }

                // Now check if there are any references
                $count = $this->checkReferenceCount($mapping, $entity);
                if ($count > 0) {
                    $references[] = [
                        'entity' => $this->getEntityName($classMetadata->getName()),
                        'count' => $count,
                    ];
                }
            }
        }

        if (!empty($references)) {
            foreach ($references as $reference) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ entity }}', $reference['entity'])
                    ->setParameter('{{ count }}', (string)$reference['count'])
                    ->addViolation();
            }
        }
    }


    private function checkReferenceCount(
        AssociationMapping $mapping,
        object $entity
    ): int {

        if ($mapping instanceof OneToManyAssociationMapping || $mapping instanceof ManyToManyAssociationMapping) {
            return 0;
        }

        if ($mapping instanceof OneToOneAssociationMapping) {
            // Only check if this is the inverse side
            if ($mapping->isOwningSide()) {
                return 0;
            }

            $related = $this->em->getRepository($mapping->targetEntity)
                ->findOneBy([$mapping->mappedBy => $entity]);

            return $related ? 1 : 0;
        }

        // $mapping is ManyToOneAssociationMapping
        return (int)$this->em->createQueryBuilder()
            ->select('COUNT(r.id)')
            ->from($mapping->sourceEntity, 'r')
            ->where('r.'.$mapping->fieldName.' = :entity')
            ->setParameter('entity', $entity)
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function getEntityName(string $class): string
    {
        $parts = explode('\\', $class);

        return end($parts);
    }

}
