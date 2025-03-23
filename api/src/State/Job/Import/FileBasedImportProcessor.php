<?php

namespace App\State\Job\Import;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Job\ImportFile;
use Bnza\JobManagerBundle\Entity\Status;
use Bnza\JobManagerBundle\WorkUnitFactoryServiceLocator;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class FileBasedImportProcessor implements ProcessorInterface
{
    const CONTEXT_KEY = 'bnza_job_manager.service_id';

    public function __construct(
        private readonly EntityManagerInterface $appEntityManager,
        private readonly EntityManagerInterface $jobEntityManager,
        private readonly WorkUnitFactoryServiceLocator $locator,
        private readonly Security $security,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $denormalizationContext = $operation->getDenormalizationContext();
        if (is_null($denormalizationContext) || !isset($denormalizationContext[self::CONTEXT_KEY])) {
            throw new LogicException("Missing context key ".self::CONTEXT_KEY);
        }
        $serviceId = $denormalizationContext[self::CONTEXT_KEY];

        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedHttpException('Access denied');
        }

        $factory = $this->locator->get($serviceId);

        $importFile = new ImportFile();
        $importFile->file = $data->file;
        $importFile->uploadDate = new DateTimeImmutable();
        $this->appEntityManager->persist($importFile);
        $this->appEntityManager->flush();

        $job = $factory
            ->toEntity()
            ->setStatus(new Status())
            ->setParameters([
                'fileId' => $importFile->getId()->toString(),
                'filePath' => $importFile->file->getRealPath(),
            ])
            ->setUserId(
                $this->security->getUser()->getUserIdentifier()
            );

        $this->jobEntityManager->persist($job);
        $this->jobEntityManager->flush();

        return $job;
    }
}
