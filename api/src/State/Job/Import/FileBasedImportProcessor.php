<?php

namespace App\State\Job\Import;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Job\ImportFile;
use Bnza\JobManagerBundle\Entity\Job;
use Bnza\JobManagerBundle\JobServicesIdLocator;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use LogicException;
use Psr\Cache\CacheItemPoolInterface;

class FileBasedImportProcessor implements ProcessorInterface
{
    const CONTEXT_KEY = 'bnza_job_manager.service_id';

    public function __construct(
        private EntityManagerInterface $appEntityManager,
        private EntityManagerInterface $jobEntityManager,
        private JobServicesIdLocator $locator,
        private CacheItemPoolInterface $redisCache
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $denormalizationContext = $operation->getDenormalizationContext();
        if (is_null($denormalizationContext) || !isset($denormalizationContext[self::CONTEXT_KEY])) {
            throw new LogicException("Missing context key ".self::CONTEXT_KEY);
        }
        $serviceId = $denormalizationContext[self::CONTEXT_KEY];

        if (!$this->locator->has($serviceId)) {
            throw new InvalidArgumentException("Service id $serviceId does not exist.");
        }

        $service = $this->locator->get($serviceId);

        $importFile = new ImportFile();
        $importFile->file = $data->file;
        $importFile->uploadDate = new DateTimeImmutable();
        $this->appEntityManager->persist($importFile);
        $this->appEntityManager->flush();

        $job = $service
            ->toEntity()
            ->setService($serviceId)
            ->setParameters([
                'fileId' => $importFile->getId()->toString(),
                'filePath' => $importFile->file->getRealPath(),
            ]);

        $this->jobEntityManager->persist($job);
        $this->jobEntityManager->flush();

        $redisKey = 'job.'.$job->getId();
        $item = $this->redisCache->getItem($redisKey);
        $item->set((string)$job->getId());
        if (!$this->redisCache->save($item)) {
            echo "error";
        }

        return $job->getId();
    }
}
