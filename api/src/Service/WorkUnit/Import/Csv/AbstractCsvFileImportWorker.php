<?php

namespace App\Service\WorkUnit\Import\Csv;

use App\Exception\Import\FileDataValidationException;
use App\Exception\Import\InvalidHeadersException;
use App\Service\WorkUnit\Import\AbstractFileImportWorker;
use Bnza\JobManagerBundle\Entity\WorkUnitEntity;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Reader;
use League\Csv\UnavailableStream;
use League\Csv\Writer;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractCsvFileImportWorker extends AbstractFileImportWorker
{
    public const string HEADER_OFFSET_KEY = 'CsvHeaderOffset';
    public const string DELIMITER_KEY = 'CsvDelimiter';


    private ?Reader $reader = null;

    private ConstraintViolationList $violations;

    abstract protected function toEntity(object $dto): object;

    /**
     * @return string[]
     */
    abstract protected function getExpectedHeader(): array;

    public function __construct(
        EntityManagerInterface $dataEntityManager,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        LoggerInterface $logger,
        protected readonly CsvFileValidationErrorsWriter $writer
    ) {
        parent::__construct($dataEntityManager, $validator, $serializer, $logger);
    }

    public function reset(): void
    {
        parent::reset();
        $this->reader = null;
        $this->writer->reset();
        $this->violations = new ConstraintViolationList();
    }

    public function executeStep(int $index, mixed $args): void
    {
        if (!is_array($args)) {
            throw new InvalidArgumentException('Argument $args must be an array');
        }
        $_args = array_merge([], $args);
        $dto = $this->serializer->denormalize($_args, $this->getDtoClass());
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $this->violations->addAll($errors);
            $_args[CsvFileValidationErrorsWriter::VALIDATION_ERRORS_FIELD_NAME] = (string)$errors;
            $this->getWriter()->insertOne($_args);

            return;
        }

        $entity = $this->toEntity($dto);
        $errors = $this->validator->validate($entity, null, ['Default', 'import']);
        if (count($errors) > 0) {
            $this->violations->addAll($errors);
            $_args[CsvFileValidationErrorsWriter::VALIDATION_ERRORS_FIELD_NAME] = (string)$errors;
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
        $this->violations = new ConstraintViolationList();
    }

    public function tearDown(): void
    {
        if (isset($this->violations) && $this->violations->count() > 0) {
            $this->writer->persist = true;
            throw new FileDataValidationException($this->writer->getPathname());
        }
        $this->dataEntityManager->flush();
    }

    protected function validateHeaders(): void
    {
        $expectedHeader = $this->getExpectedHeader();
        $header = $this->getReader()->getHeader();
        $missingHeaders = array_diff($expectedHeader, $header);
        if (count($missingHeaders) > 0) {
            throw new InvalidHeadersException($missingHeaders);
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
            throw new  InvalidArgumentException(
                sprintf("File \"%s\" is not a valid CSV file.", $params[self::FILE_PATH_KEY])
            );
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
        if (is_null($this->reader)) {
            $this->reader = Reader::createFromPath($this->getFilePath(), 'r');
            $this->reader->setHeaderOffset($this->getHeaderOffset());
            $this->reader->setDelimiter($this->getDelimiter());
        }

        return $this->reader;
    }

    protected function getWriter(): Writer
    {
        if (!$this->writer->isConfigured()) {
            $this->writer->configure($this->getReader());
        }

        return $this->writer->getWriter();
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
}
