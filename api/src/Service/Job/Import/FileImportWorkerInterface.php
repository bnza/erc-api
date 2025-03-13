<?php

namespace App\Service\Job\Import;

use Bnza\JobManagerBundle\WorkerInterface;

interface FileImportWorkerInterface extends WorkerInterface
{
    public function getFilePath(): string;
}
