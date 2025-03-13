<?php

namespace App\Service\Job\Import;

class ZooarchaeologyBoneImportJob extends AbstractImportJob
{

    public function getName(): string
    {
        return 'app:import:job:zooarchaeology:bone';
    }

    public function getDescription(): string
    {
        return 'Import zooarchaeology bones data from CSV file';
    }

    protected function returnParameters(): array
    {
        return [];
    }
}
