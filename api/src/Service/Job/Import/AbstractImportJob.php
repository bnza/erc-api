<?php

namespace App\Service\Job\Import;

use Bnza\JobManagerBundle\AbstractJob;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\File;

abstract class AbstractImportJob extends AbstractJob
{

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        array $workUnits,
        protected readonly EntityManagerInterface $dataEntityManager,
    ) {
        parent::__construct($eventDispatcher, $workUnits);
    }

    public function setUp(): void
    {
        $this->dataEntityManager->beginTransaction();
    }

    public function tearDown(): void
    {
        $this->dataEntityManager->commit();
    }

    public function rollback(): void
    {
        $this->dataEntityManager->rollback();
    }

}
