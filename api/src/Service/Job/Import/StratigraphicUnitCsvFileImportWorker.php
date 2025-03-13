<?php

namespace App\Service\Job\Import;

use App\Entity\Data\Site;
use App\Entity\Data\StratigraphicUnit;

final class StratigraphicUnitCsvFileImportWorker extends AbstractCsvFileImportWorker
{

    private function findSite(string $siteCode): Site
    {
        static $cache = [];
        if (!isset($cache[$siteCode])) {
            $cache[$siteCode] = $this->dataEntityManager->getRepository(Site::class)->findOneBy(['code' => $siteCode]);
        }

        return $cache[$siteCode];
    }

    protected function toEntity(array $rowData): object
    {
        $entity = new StratigraphicUnit();
        $entity->site = $this->findSite($rowData['site']);
        $entity->number = $rowData['number'];
        $entity->year = $rowData['year'];
        $entity->interpretation = $rowData['interpretation'];
        $entity->description = $rowData['description'];

        return $entity;
    }

    protected function getExpectedHeader(): array
    {
        return ['id', 'site', 'year', 'number', 'interpretation', 'description'];
    }
}
