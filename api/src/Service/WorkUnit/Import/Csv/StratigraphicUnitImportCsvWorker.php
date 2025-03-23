<?php

namespace App\Service\WorkUnit\Import\Csv;

use App\DTO\Import\CSV\StratigraphicUnitDTO;
use App\Entity\Data\Site;
use App\Entity\Data\StratigraphicUnit;
use UnexpectedValueException;

final class StratigraphicUnitImportCsvWorker extends AbstractCsvFileImportWorker
{

    private function findSite(string $siteCode): Site
    {
        static $cache = [];
        if (!isset($cache[$siteCode])) {
            $cache[$siteCode] = $this->dataEntityManager->getRepository(Site::class)->findOneBy(['code' => $siteCode]);
        }

        return $cache[$siteCode];
    }

    protected function toEntity(object $dto): object
    {
        $dtoClass = $this->getDtoClass();

        /* @var StratigraphicUnitDTO $dto */
        if (!$dto instanceof $dtoClass) {
            throw new UnexpectedValueException('Expected instance of '.$dtoClass);
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
        return StratigraphicUnitDTO::class;
    }
}
