<?php

namespace App\EventSubscriber;

use App\Entity\Job\ImportFile;
use App\Exception\Import\FileDataValidationException;
use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Bnza\JobManagerBundle\Entity\WorkUnitErrorEntity;
use Bnza\JobManagerBundle\Exception\JobExceptionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Uid\Uuid;

class ImportFileCsvWorkUnitSubscriber implements EventSubscriberInterface
{
    /**
     * @var array<string,EntityManagerInterface>
     */
    private array $ems = [];

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly LoggerInterface $logger,
    ) {
        $this->ems['default'] = $this->doctrine->getManager('default');
        $this->ems['bnza_job_manager'] = $this->doctrine->getManager('bnza_job_manager');
    }

    private function getEntityManager(string $name = 'default'): EntityManagerInterface
    {
        if (!array_key_exists($name, $this->ems)) {
            throw new \LogicException("Unknown entity manager name '$name'.");
        }
        if (!$this->ems[$name]->isOpen()) {
            $this->ems[$name] = $this->doctrine->resetManager($name);
        }

        return $this->ems[$name];
    }

    public static function getSubscribedEvents(): array
    {
        return [WorkerMessageFailedEvent::class => ['onError', -10]];
    }

    public function onError(WorkerMessageFailedEvent $event): void
    {
        $throwable = $event->getThrowable();
        $exception = $throwable->getPrevious();
        if ($exception instanceof JobExceptionInterface) {
            $id = $exception->getJobId();
            $job = $this->getEntityManager('bnza_job_manager')->getRepository(WorkUnitEntity::class)->find($id);
            foreach ($job->getErrors() as $error) {
                match ($error->getClass()) {
                    FileDataValidationException::class => $this->persistValidationErrorFile($id, $error),
                    default => $this->logger->error($error->getMessage()),
                };
            }
        }
    }

    private function persistValidationErrorFile(Uuid $id, WorkUnitErrorEntity $errorEntity): void
    {
        try {
            $this->logger->info('Persisting CSV validation error file');

            $filePath = $errorEntity->getValue('filePath');

            if (is_null($filePath)) {
                throw new \LogicException("CSV validation error file \"$filePath\" is not set. Unable to persist it");
            }

            if (!file_exists($filePath)) {
                throw new \RuntimeException("CSV validation error file \"$filePath\" does not exist. Unable to persist it");
            }
            $copiedFilePath = tempnam(sys_get_temp_dir(), 'api_csv_validation_errors_').'.csv';

            if (!copy($filePath, $copiedFilePath)) {
                throw new \RuntimeException("Cannot copy \"$filePath\" to \"$copiedFilePath\". Unable to persist it");
            }

            $uploadedFile = new UploadedFile($copiedFilePath, basename($filePath), null, null, true);

            $importFile = new ImportFile($id)
                ->setFile($uploadedFile)
                ->setUploadDate(new \DateTimeImmutable());

            $this->getEntityManager()->persist($importFile);
            $this->getEntityManager()->flush();

            $this->logger->debug("CSV validation error file successfully saved [\"{$importFile->getId()}\"]");
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
