<?php

namespace App\Service\WorkUnit\Import;

use Bnza\JobManagerBundle\AbstractJob;
use Bnza\JobManagerBundle\WorkUnitDefinition;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractImportJob extends AbstractJob
{
    public function __construct(
        protected readonly EntityManagerInterface $dataEntityManager,
        EventDispatcherInterface $eventDispatcher,
        array $workUnits,
        LoggerInterface $logger,
        WorkUnitDefinition $definition,
    ) {
        parent::__construct($eventDispatcher, $workUnits, $logger, $definition);
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
