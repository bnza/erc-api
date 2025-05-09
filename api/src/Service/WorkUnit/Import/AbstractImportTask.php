<?php

namespace App\Service\WorkUnit\Import;

use Bnza\JobManagerBundle\AbstractTask;
use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Bnza\JobManagerBundle\WorkUnitDefinition;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractImportTask extends AbstractTask
{
    public function __construct(
        protected readonly FileImportWorkerInterface $worker,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        WorkUnitDefinition $definition,
    ) {
        parent::__construct($eventDispatcher, $logger, $definition);
        $this->worker->reset();
    }

    public function configure(WorkUnitEntity $entity): void
    {
        parent::configure($entity);
        $this->worker->configure($entity);
    }

    public function getStepsCount(): int
    {
        return $this->worker->getStepsCount();
    }

    public function getSteps(): iterable
    {
        return $this->worker->getSteps();
    }

    public function executeStep(int $index, mixed $args): void
    {
        $this->worker->executeStep($index, $args);
    }

    public function tearDown(): void
    {
        $this->worker->tearDown();
    }
}
