<?php

namespace App\Service;

use App\Entity\Data\MediaObject;
use App\Repository\DuplicateMediaObjectEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\HttpFoundation\File\File;

class MediaObjectDuplicateFinder
{
    public function __construct(readonly private EntityManagerInterface $entityManager)
    {
    }

    public function get(string $class_name, string $sha256): ?MediaObject
    {
        $repository = $this->entityManager->getRepository($class_name);
        if (!$repository instanceof DuplicateMediaObjectEntityRepositoryInterface) {
            throw new LogicException(
                'Repository must be an instance of DuplicateMediaObjectEntityRepositoryInterface'
            );
        }
        //        $sha256 = hash_file('sha256', $file->getPathname());

        // $repository = $this->entityManager->getRepository(MediaObject::class);
        $qb = $this->entityManager->getRepository(MediaObject::class)->createQueryBuilder('m');
        $qb->where($qb->expr()->eq('m.sha256', ':sha256'))->setParameter('sha256', $sha256);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
