<?php

namespace App\State\Job\Import;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Job\Import\Csv\CsvFileImportedEntityInterface;
use App\Entity\Job\ImportFile;
use Bnza\JobManagerBundle\Entity\Status;
use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Bnza\JobManagerBundle\WorkUnitFactoryServiceLocator;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use LogicException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @template T1
 * @template T2
 */
class FileBasedImportProcessor implements ProcessorInterface
{
    const string CONTEXT_KEY = 'bnza_job_manager.service_id';
    private array $ems = [];

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly WorkUnitFactoryServiceLocator $locator,
        private readonly Security $security,
    ) {
        $this->ems['default'] = $this->doctrine->getManager('default');
        $this->ems['bnza_job_manager'] = $this->doctrine->getManager('bnza_job_manager');
    }

    private function getEntityManager(string $name = 'default'): EntityManagerInterface
    {
        if (!array_key_exists($name, $this->ems)) {
            throw new LogicException("Unknown entity manager name '$name'.");
        }
        if (!$this->ems[$name]->isOpen()) {
            $this->ems[$name] = $this->doctrine->resetManager($name);
        }

        return $this->ems[$name];
    }

    /**
     * Handles the state.
     *
     * @inheritDoc
     * @param CsvFileImportedEntityInterface $data
     * @return WorkUnitEntity
     */
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

        $importFile = new ImportFile()
            ->setFile($data->getFile())
            ->setDescription($data->getDescription())
            ->setUploadDate(new DateTimeImmutable());

        $this->getEntityManager()->persist($importFile);
        $this->getEntityManager()->flush();

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

        $this->getEntityManager('bnza_job_manager')->persist($job);
        $this->getEntityManager('bnza_job_manager')->flush();

        return $job;
    }
}
