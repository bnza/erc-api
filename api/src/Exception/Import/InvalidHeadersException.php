<?php

namespace App\Exception\Import;

use InvalidArgumentException;
use Throwable;

class InvalidHeadersException extends InvalidArgumentException implements AppImportExceptionInterface
{
    public function __construct(private readonly array $missingHeaders, $code = 0, ?Throwable $previous = null)
    {
        $this->message = sprintf(
            'Missing headers: %s',
            implode(
                ', ',
                array_map(function ($header) {
                    return "\"$header\"";
                },
                    $missingHeaders
                )
            )
        );
        parent::__construct($this->message, $code, $previous);
    }

    public function getMissingHeaders(): array
    {
        return $this->missingHeaders;
    }

    public function getValues(): array
    {
        return [
            'missingHeaders' => $this->missingHeaders,
        ];
    }

}
