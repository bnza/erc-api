<?php

namespace App\Service\WorkUnit\Import;

use Bnza\JobManagerBundle\AbstractWorkUnitFactory;
use Bnza\JobManagerBundle\WorkUnitDefinitionInterface;
use Bnza\JobManagerBundle\WorkUnitInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ImportTaskAbstractFactory extends AbstractWorkUnitFactory
{
    final public function __construct(
        protected readonly FileImportWorkerInterface $worker,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        WorkUnitDefinitionInterface $definition,
    ) {
        parent::__construct($definition, $eventDispatcher, $logger);
    }

    #[\Override]
    final public function create(): WorkUnitInterface
    {
        $class = $this->definition->getClass();

        if (!class_exists($class)) {
            throw new \RuntimeException('Class "'.$class.'" does not exist.');
        }

        if (!class_implements($class, AbstractImportTask::class)) {
            throw new \RuntimeException('Class "'.$class.'" does not implement AbstractImportJob.');
        }

        return new $class($this->worker, $this->eventDispatcher, $this->logger, $this->definition);
    }
}
