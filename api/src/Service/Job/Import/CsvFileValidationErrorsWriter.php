<?php

namespace App\Service\Job\Import;

use League\Csv\Reader;
use League\Csv\Writer;
use LogicException;
use RuntimeException;

class CsvFileValidationErrorsWriter
{
    public const string VALIDATION_ERRORS_FIELD_NAME = 'validation_errors';

    private readonly Writer $writer;

    public bool $persist = false;

    private bool $configured = false;

    private string|bool $tempFilePath;


    public function __construct(?string $filePath = null)
    {
        $filepath ??= sys_get_temp_dir();

        if (!file_exists($filepath) || !is_readable($filepath)) {
            throw new RuntimeException("File path \"$filePath\" does not exist or is not writeable");
        }
        $tempFilePath = tempnam($filepath, 'api_csv_validation_errors_').'.csv';

        if (!$tempFilePath) {
            throw new RuntimeException("Failed to create temporary file \"$tempFilePath\"");
        }

        $this->tempFilePath = $tempFilePath;
        $this->writer = Writer::createFromPath($tempFilePath, 'w+');
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

}
