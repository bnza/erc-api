<?php

namespace App\Service\WorkUnit\Import\Csv;

use App\Dto\Import\Csv\StratigraphicUnitDto;
use App\Entity\Data\Site;
use App\Entity\Data\StratigraphicUnit;

final class StratigraphicUnitImportCsvWorker extends AbstractCsvFileImportWorker
{
    private array $siteCache = [];

    private function findSite(string $siteCode): Site
    {
        if (!isset($this->siteCache[$siteCode])) {
            $this->siteCache[$siteCode] = $this->dataEntityManager->getRepository(Site::class)->findOneBy(
                ['code' => $siteCode]
            );
        }

        return $this->siteCache[$siteCode];
    }

    protected function toEntity(object $dto): object
    {
        $dtoClass = $this->getDtoClass();

        /* @var StratigraphicUnitDto $dto */
        if (!$dto instanceof $dtoClass) {
            throw new \UnexpectedValueException('Expected instance of '.$dtoClass);
        }
        $entity = new StratigraphicUnit();
        $entity->site = $this->findSite($dto->site);
        foreach (['number', 'year', 'public', 'interpretation', 'description', 'public'] as $property) {
            $entity->{$property} = $dto->{$property};
        }

        return $entity;
    }

    protected function getExpectedHeader(): array
    {
        return ['id', 'site', 'year', 'number', 'interpretation', 'description'];
    }

    protected function getDtoClass(): string
    {
        return StratigraphicUnitDto::class;
    }

    public function reset(): void
    {
        parent::reset();
        $this->siteCache = [];
    }
}
