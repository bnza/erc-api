<?php

namespace App\Service\Job\Import;


class StratigraphicUnitCsvFileImportJob extends AbstractImportJob
{

    protected function returnParameters(): array
    {
        return [];
    }

    public function getName(): string
    {
        return 'app:import:job:stratigraphic_unit';
    }

    public function getDescription(): string
    {
        return 'Import stratigraphic unit data from CSV file';

    }
}
