<?php

namespace App\Service;

use App\Entity\Data\MediaObject;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;

class MediaObjectFileHashNamer implements NamerInterface
{
    public function __construct(private readonly SluggerInterface $slugger)
    {
    }

    public function name($object, PropertyMapping $mapping): string
    {
        if (!$object instanceof MediaObject) {
            throw new \InvalidArgumentException('object to rename must be instance of '.MediaObject::class);
        }
        /* @var $file UploadedFile */
        $file = $mapping->getFile($object);
        $name = $file->getClientOriginalName();

        return sprintf('%s_%s', $object->sha256, $this->transliterate($name));
    }

    public function transliterate(string $string, string $separator = '-'): string
    {
        $path_parts = pathinfo($string);

        $filename = $path_parts['filename'];
        $extension = '';
        if (array_key_exists('extension', $path_parts)) {
            $extension = $path_parts['extension'];
        }
        $transliterated = $this->slugger->slug($filename, '-');

        if ('' !== $extension) {
            $transliterated .= '.'.$extension;
        }

        return \strtolower($transliterated);
    }
}
