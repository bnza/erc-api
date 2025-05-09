<?php

namespace App\Exception\Import;

class InvalidFileTypeException extends \InvalidArgumentException implements AppImportExceptionInterface
{
    public function __construct(
        private readonly string $fileName,
        private readonly string $fileType,
        $code = 0,
        ?\Throwable $previous = null,
    ) {
        $this->message = sprintf(
            'File "%s" is not a valid "%s" file.',
            basename($this->fileName),
            $this->fileType
        );
        parent::__construct($this->message, $code, $previous);
    }

    public function getValues(): array
    {
        return [
            'file' => basename($this->fileName),
            'fileType' => $this->fileType,
        ];
    }

    public function isHandled(): bool
    {
        return true;
    }
}
