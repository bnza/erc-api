<?php

namespace App\Serializer;

use Symfony\Component\Serializer\Encoder\EncoderInterface;

class TextPlainEncoder implements EncoderInterface
{
    public function supportsEncoding(string $format, array $context = []): bool
    {
        return 'text' === $format;
    }

    public function encode(mixed $data, string $format, array $context = []): string
    {
        return (string) $data;
    }
}
