<?php

namespace App\Exception\Import;

class FileDataValidationException extends \InvalidArgumentException implements AppImportExceptionInterface
{
    public function __construct(private readonly string $validationFilePath, $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct('File data validation failed', $code, $previous);
    }

    public function getValidationFilePath(): string
    {
        return $this->validationFilePath;
    }

    public function getValues(): array
    {
        return [
            'filePath' => $this->validationFilePath,
        ];
    }

    public function isHandled(): bool
    {
        return true;
    }
}
