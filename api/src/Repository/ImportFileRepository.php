<?php

namespace App\Repository;

use App\Entity\Job\ImportFile;
use DateMalformedStringException;
use DateTimeImmutable;
use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;

class ImportFileRepository extends EntityRepository
{
    /**
     * @param string $relativeDateTimeString
     * @return array<ImportFile>|null
     */
    public function findRecordOlderThan(string $relativeDateTimeString = '-1 day'): array|null
    {
        if (!$this->validateBasicRelativeDate($relativeDateTimeString, true)) {
            throw new InvalidArgumentException("Relative date string \"$relativeDateTimeString\"is invalid");
        }

        $relativeDateTime = new DateTimeImmutable($relativeDateTimeString);

        $queryBuilder = $this->createQueryBuilder('e');
        $queryBuilder->where('e.uploadDate < :relativeDateTime');
        $queryBuilder->setParameter('relativeDateTime', $relativeDateTime);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param string $relativeDateTimeString
     * @return array<string>
     */
    public function deleteRecordOlderThan(string $relativeDateTimeString): array
    {
        $entities = $this->findRecordOlderThan($relativeDateTimeString);

        $em = $this->getEntityManager();
        $deleted = [];
        foreach ($entities as $entity) {
            $em->remove($entity);
            $deleted[] = $entity->filePath;
        }
        $em->flush();

        return $entities;
    }

    private function validateBasicRelativeDate(string $relativeDateTimeString, bool $onlyNegative = false): bool
    {
        $pattern = $onlyNegative ? '/^-' : '/^[+-]';
        // Allow formats like "+1 day", "-2 weeks", "+3 months", "-4 years", "+10 hours"
        $pattern .= '\d+\s*(day|days|week|weeks|month|months|year|years|hour|hours|minute|minutes|second|seconds)$/i';

        if (!preg_match($pattern, $relativeDateTimeString)) {
            return false; // Invalid format
        }

        // Final verification using DateTimeImmutable
        $date = new DateTimeImmutable();

        try {
            return (bool)$date->modify($relativeDateTimeString);
        } catch (DateMalformedStringException $e) {
            return false;
        }

    }
}
