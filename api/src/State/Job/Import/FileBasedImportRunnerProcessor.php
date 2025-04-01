<?php

namespace App\State\Job\Import;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Bnza\JobManagerBundle\Message\JobRunnerMessage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

class FileBasedImportRunnerProcessor implements ProcessorInterface
{
    private EntityManagerInterface $jobEntityManager;

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly MessageBusInterface $bus,
        private readonly Security $security,
    ) {
        $this->jobEntityManager = $this->doctrine->getManager('bnza_job_manager');
    }

    public function getEntityManager(): EntityManagerInterface
    {
        if (!$this->jobEntityManager->isOpen()) {
            $this->jobEntityManager = $this->doctrine->resetManager('bnza_job_manager');
        }

        return $this->jobEntityManager;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$operation instanceof Post) {
            throw new RuntimeException("Only post operations are supported");
        }
        if (!isset($uriVariables['id'])) {
            throw new InvalidArgumentException("Missing 'id' parameter");
        }

        $id = Uuid::fromString($uriVariables['id']);
        if (!Uuid::isValid($id)) {
            throw new RuntimeException("Invalid id \"$id\"");
        }
        $id = Uuid::fromString($id);

        $job = $this->jobEntityManager->find(WorkUnitEntity::class, $id);
        if (!$job) {
            throw new NotFoundHttpException("Job \"$id\" not found");
        }

        $message = new JobRunnerMessage($id, $this->security->getUser());
        $this->bus->dispatch($message);

        return $job;
    }
}
