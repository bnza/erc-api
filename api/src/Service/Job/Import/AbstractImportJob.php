<?php

namespace App\Service\Job\Import;

use Bnza\JobManagerBundle\AbstractJob;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\File;

abstract class AbstractImportJob extends AbstractJob
{

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        array $workUnits,
        protected readonly EntityManagerInterface $dataEntityManager,
        LoggerInterface $logger
    ) {
        parent::__construct($eventDispatcher, $workUnits, $logger);
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
