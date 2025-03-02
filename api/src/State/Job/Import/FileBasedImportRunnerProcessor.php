<?php

namespace App\State\Job\Import;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use Bnza\JobManagerBundle\JobRunner;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Uid\Uuid;

class FileBasedImportRunnerProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly JobRunner $runner
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if (!$operation instanceof Post) {
            throw new RuntimeException("Only post operations are supported");
        }
        if (!isset($uriVariables['id'])) {
            throw new InvalidArgumentException("Missing 'id' parameter");
        }

        $id = Uuid::fromString($uriVariables['id']);

        $this->runner->run($id);
    }
}
