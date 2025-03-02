<?php

namespace App\Service\Job\Import;

use Bnza\JobManagerBundle\AbstractTask;

class ZooarchaeologyBoneImportTask extends AbstractTask
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

    public function executeStep(mixed $args)
    {

    }

    protected function validateParameters(array $params): void
    {
        // TODO: Implement validateParameters() method.
    }

    protected function returnParameters(): array
    {
        return [];
    }

    public function getStepsCount(): int
    {
        return count($this->getSteps());
    }
}
