<?php

namespace App\Service\WorkUnit\Import;

use Bnza\JobManagerBundle\WorkerInterface;

interface FileImportWorkerInterface extends WorkerInterface
{
    public function getFilePath(): string;
}
