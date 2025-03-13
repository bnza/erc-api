<?php

namespace App\Service\Job\Import;

use Bnza\JobManagerBundle\AbstractTask;

class ZooarchaeologyBoneImportTask extends AbstractImportTask
{

    public function getName(): string
    {
        return 'app:import:task:zooarchaeology:bone';
    }

    public function getDescription(): string
    {
        return 'Import zooarchaeology bones data from CSV file';
    }

    public function getSteps(): iterable
    {
        return ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
    }

    protected function returnParameters(): array
    {
        return [];
    }

}
