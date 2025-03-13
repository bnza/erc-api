<?php

namespace App\Service\Job\Import;

class StratigraphicUnitCsvFileImportTask extends AbstractImportTask
{

    protected function returnParameters(): array
    {
        return [];
    }

    public function getName(): string
    {
        return 'app:import:task:stratigraphic_unit';
    }

    public function getDescription(): string
    {
        return 'Import stratigraphic data from CSV file';
    }
}
