<?php

namespace App\EventSubscriber;

use App\Entity\Data\MediaObject;
use App\Entity\Job\ImportedFile;
use App\Entity\Job\ImportFile;
use App\Service\WorkUnit\Import\AbstractFileImportWorker;
use App\Service\WorkUnit\Import\AbstractImportJob;
use App\Service\WorkUnit\Import\AbstractImportTask;
use Bnza\JobManagerBundle\AbstractWorkUnit;
use Bnza\JobManagerBundle\Event\WorkUnitEvent;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Throwable;
use Vich\UploaderBundle\Storage\FileSystemStorage;

class ImportFileWorkUnitSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityManagerInterface $dataEntityManager,
        private readonly LoggerInterface $logger,
        private readonly FileSystemStorage $fileSystemStorage,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [WorkUnitEvent::SUCCESS => ['onSuccess', -10], WorkUnitEvent::TERMINATED => ['onTerminated', -10]];
    }

    public function onSuccess(WorkUnitEvent $event): void
    {
        $workUnit = $event->getWorkUnit();
        if ($workUnit instanceof AbstractImportTask) {
            $this->persistImportedFile($workUnit);
        }
    }

    public function onTerminated(WorkUnitEvent $event): void
    {

        $workUnit = $event->getWorkUnit();
        if ($workUnit instanceof AbstractImportJob) {
            $this->logger->info('Removing imported temporary file');
            try {
                $importFile = $this->getImportFile($workUnit);
                $id = $importFile->getId();
                $this->dataEntityManager->remove($importFile);
                $this->dataEntityManager->flush();
                $this->logger->debug("Removed imported temporary file : \"$id\"");
            } catch (Throwable $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    private function getImportFile(AbstractWorkUnit $workUnit): ImportFile
    {
        $param = $workUnit->getEntity()->getParameters();

        if (!$param || !array_key_exists(AbstractFileImportWorker::FILE_ID, $param)) {
            throw new InvalidArgumentException('Imported file ID is not set. Unable to persist it');
        }
        $id = $param[AbstractFileImportWorker::FILE_ID];

        $importFile = $this->dataEntityManager->find(ImportFile::class, $id);
        if (!$importFile) {
            throw new RuntimeException("Imported file \"$id\" does not exist. Unable to persist it");
        }

        return $importFile;
    }

    private function persistImportedFile(AbstractImportTask $task): void
    {

        try {
            $this->logger->info('Persisting imported file process');

            $importFile = $this->getImportFile($task);

            $importedFilePath = $this->fileSystemStorage->resolvePath($importFile);

            if (!file_exists($importedFilePath)) {
                throw new RuntimeException("Imported file \"$importedFilePath\" does not exist. Unable to persist it");
            }

            $copiedFilePath = (sys_get_temp_dir().DIRECTORY_SEPARATOR.basename($importedFilePath));

            if (!copy($importedFilePath, $copiedFilePath)) {
                throw new RuntimeException(
                    "Cannot copy \"$importedFilePath\" to \"$copiedFilePath\". Unable to persist it"
                );
            }

            $uploadedFile = new UploadedFile($copiedFilePath, $importFile->originalFilename, null, null, true);

            $mediaObject = new MediaObject()->setFile($uploadedFile);
            $mediaObject->uploadDate = new DateTimeImmutable();

            $importedFile = new ImportedFile()
                ->setId($task->getEntity()->getRoot()->getId())
                ->setService($task->getService())
                ->setMediaObject($mediaObject)
                ->setUserId($task->getEntity()->getUserId())
                ->setUploadDate(new DateTimeImmutable());

            $this->dataEntityManager->persist($importedFile);
            $this->dataEntityManager->flush();

            $this->logger->debug("Imported file successfully saved [\"$importedFile->id\"]");
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
