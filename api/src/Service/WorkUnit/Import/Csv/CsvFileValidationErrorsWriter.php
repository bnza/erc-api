<?php

namespace App\Service\WorkUnit\Import\Csv;

use League\Csv\Reader;
use League\Csv\Writer;
use LogicException;
use RuntimeException;
use Symfony\Contracts\Service\ResetInterface;

class CsvFileValidationErrorsWriter implements ResetInterface
{
    public const string VALIDATION_ERRORS_FIELD_NAME = 'validation_errors';

    private Writer $writer;


    public bool $persist = false;

    private bool $configured = false;

    private string|bool $tempFilePath;
    private readonly string $tempDirectory;


    public function __construct(?string $filePath = null)
    {
        $this->tempDirectory = $filepath ??= sys_get_temp_dir();
        $this->initializeWriter();

    }

    public function reset(): void
    {
        $this->initializeWriter();
    }

    public function configure(Reader $reader): void
    {
        if ($this->configured) {
            throw new LogicException('Writer already configured');
        }

        $this->writer->setDelimiter($reader->getDelimiter());
        $header = $reader->getHeader();
        $header[] = self::VALIDATION_ERRORS_FIELD_NAME;
        $this->writer->insertOne($header);
        $this->configured = true;
    }

    public function isConfigured(): bool
    {
        return $this->configured;
    }

    public function getWriter(): Writer
    {
        if (!$this->configured) {
            throw new RuntimeException("Writer must be configured before accessing it");
        }

        return $this->writer;
    }

    public function getPathname(): string
    {
        return $this->writer->getPathname();
    }

    public function __destruct()
    {
        if (!$this->persist) {
            unlink($this->tempFilePath);
        }
    }

    private function initializeWriter(): void
    {
        $this->persist = false;

        $this->configured = false;

        if (!file_exists($this->tempDirectory) || !is_readable($this->tempDirectory)) {
            throw new RuntimeException(
                sprintf("File path \"%s\" does not exist or is not writeable", $this->tempDirectory)
            );
        }
        $tempFilePath = tempnam($this->tempDirectory, 'api_csv_validation_errors_').'.csv';

        if (!$tempFilePath) {
            throw new RuntimeException("Failed to create temporary file \"$tempFilePath\"");
        }

        $this->tempFilePath = $tempFilePath;
        $this->writer = Writer::createFromPath($tempFilePath, 'w+');
    }

}
