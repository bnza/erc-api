<?php

namespace App\Service\Job\Import;


use Bnza\JobManagerBundle\AbstractTask;
use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractImportTask extends AbstractTask
{

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        protected readonly FileImportWorkerInterface $worker,
    ) {
        parent::__construct($eventDispatcher);
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
