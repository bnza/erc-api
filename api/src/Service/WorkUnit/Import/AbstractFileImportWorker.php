<?php

namespace App\Service\WorkUnit\Import;

use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractFileImportWorker implements FileImportWorkerInterface
{
    public const string FILE_PATH_KEY = 'filePath';

    protected array $params = [];

    abstract protected function getDtoClass(): string;

    public function __construct(
        protected readonly EntityManagerInterface $dataEntityManager,
        protected readonly ValidatorInterface $validator,
        protected readonly SerializerInterface $serializer
    ) {
    }

    public function reset(): void
    {
        $this->params = [];
        $this->dataEntityManager->clear();
    }

    protected function validateParams(array $params): void
    {
        if (!array_key_exists(self::FILE_PATH_KEY, $params)) {
            throw new InvalidArgumentException(sprintf("Missing \"%s\" key", self::FILE_PATH_KEY));
        }
    }

    public function configure(WorkUnitEntity $entity): void
    {
        $params = array_merge([], $entity->getParameters());
        $this->params = $this->mapParameters($params);
        $this->validateParams($this->params);
    }

    public function getFilePath(): string
    {
        return $this->params[self::FILE_PATH_KEY];
    }

    private function mapParameters(array $params): array
    {
        $paramsMapping = [];
        if (array_key_exists('paramsMapping', $params) && is_array($params['paramsMapping'])) {
            $paramsMapping = array_merge($paramsMapping, $params['paramsMapping']);
            foreach ($paramsMapping as $providedParamKey => $workerParamKey) {
                if (!is_string($workerParamKey)) {
                    throw new InvalidArgumentException("Parameter mapping worker's key should be string");
                }
                if (!is_string($providedParamKey)) {
                    throw new InvalidArgumentException("Parameter mapping provided key should be string");
                }
                if (!array_key_exists($providedParamKey, $params)) {
                    throw new InvalidArgumentException(
                        'Parameter mapping provided key "$providedParamKey" does not exist"'
                    );
                }
            }
        }

        $_params = [];
        foreach ($params as $key => $value) {
            if (array_key_exists($key, $paramsMapping)) {
                $_params[$key] = $params[$paramsMapping[$key]];
            } else {
                $_params[$key] = $value;
            }
        }

        return $_params;
    }
}
