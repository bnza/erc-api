<?php

namespace App\Service\WorkUnit\Import;

use Bnza\JobManagerBundle\AbstractJobFactory;
use Bnza\JobManagerBundle\WorkUnitDefinitionInterface;
use Bnza\JobManagerBundle\WorkUnitInterface;
use Doctrine\ORM\EntityManagerInterface;
use Override;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ImportJobAbstractFactory extends AbstractJobFactory
{
    final public function __construct(
        protected readonly EntityManagerInterface $dataEntityManager,
        array $workUnitFactories,
        WorkUnitDefinitionInterface $definition,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger
    ) {
        parent::__construct($workUnitFactories, $definition, $eventDispatcher, $logger);
    }

    #[Override] final public function create(): WorkUnitInterface
    {
        $class = $this->definition->getClass();

        if (!class_exists($class)) {
            throw new RuntimeException('Class "'.$class.'" does not exist.');
        }

        if (!class_implements($class, AbstractImportJob::class)) {
            throw new RuntimeException('Class "'.$class.'" does not implement AbstractImportJob.');
        }

        return new $class(
            $this->dataEntityManager,
            $this->eventDispatcher,
            $this->workUnitFactories,
            $this->logger,
            $this->definition
        );
    }
}
