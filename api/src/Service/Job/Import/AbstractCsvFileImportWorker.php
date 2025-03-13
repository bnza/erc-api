<?php

namespace App\Service\Job\Import;

use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Bnza\JobManagerBundle\Event\WorkUnitEvent;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Reader;
use League\Csv\UnavailableStream;
use League\Csv\Writer;
use SplTempFileObject;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

abstract class AbstractCsvFileImportWorker extends AbstractFileImportWorker
{
    public const string HEADER_OFFSET_KEY = 'CsvHeaderOffset';
    public const string DELIMITER_KEY = 'CsvDelimiter';

    public const string VALIDATION_ERRORS_FIELD_NAME = 'validation_errors';

    private Reader $reader;

    private Writer $writer;

    private ConstraintViolationList $violations;

    abstract protected function toEntity(array $rowData): object;

    /**
     * @return string[]
     */
    abstract protected function getExpectedHeader(): array;

    public function executeStep(int $index, mixed $args): void
    {
        if (!is_array($args)) {
            throw new InvalidArgumentException('Argument $args must be an array');
        }
        $_args = array_merge([], $args);
        $entity = $this->toEntity($args);
        $errors = $this->validator->validate($entity);
        if (count($errors) > 0) {
            $this->violations->addAll($errors);
            $_args[self::VALIDATION_ERRORS_FIELD_NAME] = (string)$errors;
            $this->getWriter()->insertOne($_args);
        } else {
            $this->dataEntityManager->persist($entity);
        }
    }

    public function getSteps(): iterable
    {
        return $this->getReader()->getRecords();
    }

    public function configure(WorkUnitEntity $entity): void
    {
        parent::configure($entity);
        $this->validateHeaders();
    }

    public function tearDown(): void
    {
        if (isset($this->violations) && $this->violations->count() > 0) {
            throw new ValidationFailedException($this->writer->toString(), $this->violations);
        }
        $this->dataEntityManager->flush();
    }

    protected function validateHeaders(): void
    {
        $expectedHeader = $this->getExpectedHeader();
        $header = $this->getReader()->getHeader();
        $missingHeaders = array_diff($expectedHeader, $header);
        if (count($missingHeaders) > 0) {
            throw new InvalidArgumentException(sprintf('Missing headers: %s', implode(', ', $missingHeaders)));
        }
    }

    protected function validateParams(array $params): void
    {
        parent::validateParams($params);

        if (!file_exists($params[self::FILE_PATH_KEY])) {
            throw new InvalidArgumentException(sprintf("File \"%s\" does not exist.", $params[self::FILE_PATH_KEY]));
        }
        $file = new File($params[self::FILE_PATH_KEY]);
        if ($file->getMimeType() !== 'text/csv') {
            throw new  InvalidArgumentException(sprintf("File \"%s\" is not a valid CSV file.", self::FILE_PATH_KEY));
        }
        if (isset($params[self::HEADER_OFFSET_KEY]) && !is_integer($params[self::HEADER_OFFSET_KEY])) {
            throw new InvalidArgumentException('Header offset must be an integer');
        }
        if (isset($params[self::DELIMITER_KEY]) && !is_string($params[self::DELIMITER_KEY])) {
            throw new InvalidArgumentException('Header offset must be an integer');
        }
    }

    /**
     * @throws Exception
     * @throws InvalidArgument
     * @throws UnavailableStream
     */
    public function getStepsCount(): int
    {
        return count($this->getReader());
    }

    /**
     * @throws UnavailableStream
     * @throws InvalidArgument|Exception
     */
    protected function getReader(): Reader
    {
        if (!isset($this->reader)) {
            $this->reader = Reader::createFromPath($this->getFilePath(), 'r');
            $this->reader->setHeaderOffset($this->getHeaderOffset());
            $this->reader->setDelimiter($this->getDelimiter());
        }

        return $this->reader;
    }

    protected function getWriter(): Writer
    {
        if (!isset($this->writer)) {
            $this->writer = Writer::createFromFileObject(new SplTempFileObject());
            $this->writer->setDelimiter($this->reader->getDelimiter());
            $header = $this->getReader()->getHeader();
            $header[] = self::VALIDATION_ERRORS_FIELD_NAME;
            $this->writer->insertOne($header);
        }

        return $this->writer;
    }

    private function getHeaderOffset(): int
    {
        return isset($this->params[self::HEADER_OFFSET_KEY])
            ? $this->params[self::HEADER_OFFSET_KEY]
            : 0;
    }

    private function getDelimiter(): string
    {
        return isset($this->params[self::DELIMITER_KEY])
            ? $this->params[self::DELIMITER_KEY]
            : ',';
    }

    private function getViolations(): ConstraintViolationListInterface
    {
        if (!isset($this->violations)) {
            $this->violations = new ConstraintViolationList();
        }

        return $this->violations;
    }
}
