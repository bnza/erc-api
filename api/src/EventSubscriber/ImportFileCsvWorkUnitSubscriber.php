<?php

namespace App\EventSubscriber;

use App\Entity\Job\ImportFile;
use App\Exception\Import\FileDataValidationException;
use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Bnza\JobManagerBundle\Entity\WorkUnitErrorEntity;
use Bnza\JobManagerBundle\Exception\JobExceptionInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Uid\Uuid;
use Throwable;

class ImportFileCsvWorkUnitSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityManagerInterface $dataEntityManager,
        private readonly EntityManagerInterface $jobEntityManager,
        private readonly LoggerInterface $logger,
    ) {
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
            $job = $this->jobEntityManager->getRepository(WorkUnitEntity::class)->find($id);
            foreach ($job->getErrors() as $error) {
                match ($error->getClass()) {
                    FileDataValidationException::class => $this->persistValidationErrorFile($id, $error),
                    default => $this->logger->error($error->getMessage())
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
                throw new LogicException("CSV validation error file \"$filePath\" is not set. Unable to persist it");
            }

            if (!file_exists($filePath)) {
                throw new RuntimeException(
                    "CSV validation error file \"$filePath\" does not exist. Unable to persist it"
                );
            }
            $copiedFilePath = tempnam(sys_get_temp_dir(), 'api_csv_validation_errors_').'.csv';

            if (!copy($filePath, $copiedFilePath)) {
                throw new RuntimeException(
                    "Cannot copy \"$filePath\" to \"$copiedFilePath\". Unable to persist it"
                );
            }

            $uploadedFile = new UploadedFile($copiedFilePath, basename($filePath), null, null, true);

            $importFile = new ImportFile($id)
                ->setFile($uploadedFile)
                ->setUploadDate(new DateTimeImmutable());

            $this->dataEntityManager->persist($importFile);
            $this->dataEntityManager->flush();

            $this->logger->debug("CSV validation error file successfully saved [\"{$importFile->getId()}\"]");
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
