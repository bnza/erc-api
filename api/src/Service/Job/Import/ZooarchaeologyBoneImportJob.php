<?php

namespace App\Service\Job\Import;

use Bnza\JobManagerBundle\AbstractJob;

class ZooarchaeologyBoneImportJob extends AbstractJob
{

    public function getName(): string
    {
        return 'app:import:job:zooarchaeology:bone';
    }

    public function getDescription(): string
    {
        return 'Import zooarchaeology bones data from CSV file';
    }

    protected function validateParameters(array $params): void
    {
        // TODO: Implement validateParameters() method.
    }

    protected function returnParameters(): array
    {
        return [];
    }
}
